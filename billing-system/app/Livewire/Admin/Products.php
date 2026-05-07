<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductConfigOption;
use App\Models\ProductConfigOptionValue;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Products extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $categoryId = null;
    public string $status = 'all';
    public bool $showModal = false;
    public ?Product $editingProduct = null;
    public array $configOptions = [];
    
    // Form fields
    public array $form = [
        'name' => '',
        'slug' => '',
        'category_id' => '',
        'description' => '',
        'short_description' => '',
        'type' => 'game_server',
        'price' => 0,
        'billing_cycle' => 'monthly',
        'setup_fee' => 0,
        'has_trial' => false,
        'trial_days' => 0,
        'stock_enabled' => false,
        'stock_quantity' => null,
        'is_visible' => true,
    ];

    protected $queryString = ['search', 'categoryId', 'status'];

    public function mount()
    {
        $this->editingProduct = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function createProduct()
    {
        $this->editingProduct = null;
        $this->resetForm();
        $this->showModal = true;
    }

    public function editProduct(int $productId)
    {
        $this->editingProduct = Product::with(['configOptions.values'])->findOrFail($productId);
        $this->form = [
            'name' => $this->editingProduct->name,
            'slug' => $this->editingProduct->slug,
            'category_id' => $this->editingProduct->category_id,
            'description' => $this->editingProduct->description,
            'short_description' => $this->editingProduct->short_description,
            'type' => $this->editingProduct->type,
            'price' => $this->editingProduct->price,
            'billing_cycle' => $this->editingProduct->billing_cycle,
            'setup_fee' => $this->editingProduct->setup_fee,
            'has_trial' => $this->editingProduct->has_trial,
            'trial_days' => $this->editingProduct->trial_days,
            'stock_enabled' => $this->editingProduct->stock_enabled,
            'stock_quantity' => $this->editingProduct->stock_quantity,
            'is_visible' => $this->editingProduct->is_visible,
        ];

        $this->configOptions = $this->editingProduct->configOptions
            ->sortBy('sort_order')
            ->values()
            ->map(function (ProductConfigOption $option) {
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'type' => $option->type,
                    'is_required' => $option->is_required,
                    'sort_order' => $option->sort_order,
                    'values' => $option->values
                        ->sortBy('sort_order')
                        ->values()
                        ->map(function (ProductConfigOptionValue $value) {
                            return [
                                'id' => $value->id,
                                'label' => $value->label,
                                'value' => $value->value,
                                'price' => $value->price,
                                'price_type' => $value->price_type,
                                'sort_order' => $value->sort_order,
                                'is_default' => $value->is_default,
                            ];
                        })
                        ->toArray(),
                ];
            })
            ->toArray();

        $this->showModal = true;
    }

    public function saveProduct()
    {
        $rules = [
            'form.name' => 'required|string|max:255',
            'form.slug' => 'required|string|max:255|unique:products,slug' . ($this->editingProduct ? ',' . $this->editingProduct->id : ''),
            'form.category_id' => 'nullable|exists:categories,id',
            'form.description' => 'nullable|string',
            'form.short_description' => 'nullable|string|max:500',
            'form.type' => 'required|in:game_server,web_hosting,vps,dedicated,domain,custom',
            'form.price' => 'required|numeric|min:0',
            'form.billing_cycle' => 'required|in:hourly,daily,weekly,monthly,quarterly,semi_annually,annually,biennially,one_time',
            'form.setup_fee' => 'nullable|numeric|min:0',
            'form.trial_days' => 'nullable|integer|min:0',
            'form.stock_quantity' => 'nullable|integer|min:0',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->form['name'],
            'slug' => $this->form['slug'],
            'category_id' => $this->form['category_id'] ?: null,
            'description' => $this->form['description'],
            'short_description' => $this->form['short_description'],
            'type' => $this->form['type'],
            'price' => $this->form['price'],
            'billing_cycle' => $this->form['billing_cycle'],
            'setup_fee' => $this->form['setup_fee'],
            'has_trial' => $this->form['has_trial'],
            'trial_days' => $this->form['trial_days'],
            'stock_enabled' => $this->form['stock_enabled'],
            'stock_quantity' => $this->form['stock_quantity'],
            'is_visible' => $this->form['is_visible'],
        ];

        if ($this->editingProduct) {
            $this->editingProduct->update($data);
            $product = $this->editingProduct->fresh();
            session()->flash('success', 'Product updated successfully.');
        } else {
            $product = Product::create($data);
            session()->flash('success', 'Product created successfully.');
        }

        $this->syncConfigOptions($product);

        $this->showModal = false;
        $this->resetForm();
    }

    public function deleteProduct(int $productId)
    {
        $product = Product::findOrFail($productId);
        $product->delete();
        session()->flash('success', 'Product deleted successfully.');
    }

    public function toggleVisibility(int $productId)
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_visible' => !$product->is_visible]);
        session()->flash('success', 'Product visibility updated.');
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function addConfigOption(): void
    {
        $this->configOptions[] = [
            'name' => '',
            'type' => 'select',
            'is_required' => false,
            'sort_order' => count($this->configOptions),
            'values' => [[
                'label' => '',
                'value' => '',
                'price' => 0,
                'price_type' => 'fixed',
                'sort_order' => 0,
                'is_default' => false,
            ]],
        ];
    }

    public function removeConfigOption(int $index): void
    {
        unset($this->configOptions[$index]);
        $this->configOptions = array_values($this->configOptions);
    }

    public function addConfigOptionValue(int $optionIndex): void
    {
        $this->configOptions[$optionIndex]['values'][] = [
            'label' => '',
            'value' => '',
            'price' => 0,
            'price_type' => 'fixed',
            'sort_order' => count($this->configOptions[$optionIndex]['values'] ?? []),
            'is_default' => false,
        ];
    }

    public function removeConfigOptionValue(int $optionIndex, int $valueIndex): void
    {
        unset($this->configOptions[$optionIndex]['values'][$valueIndex]);
        $this->configOptions[$optionIndex]['values'] = array_values($this->configOptions[$optionIndex]['values']);
    }

    public function updatedFormName($value)
    {
        if (empty($this->form['slug']) || !$this->editingProduct) {
            $this->form['slug'] = Str::slug($value);
        }
    }

    protected function resetForm()
    {
        $this->form = [
            'name' => '',
            'slug' => '',
            'category_id' => '',
            'description' => '',
            'short_description' => '',
            'type' => 'game_server',
            'price' => 0,
            'billing_cycle' => 'monthly',
            'setup_fee' => 0,
            'has_trial' => false,
            'trial_days' => 0,
            'stock_enabled' => false,
            'stock_quantity' => null,
            'is_visible' => true,
        ];
        $this->configOptions = [];
        $this->resetValidation();
    }

    protected function syncConfigOptions(Product $product): void
    {
        $product->configOptions()->delete();

        foreach ($this->configOptions as $optionIndex => $optionData) {
            if (blank($optionData['name'] ?? '')) {
                continue;
            }

            $option = $product->configOptions()->create([
                'name' => $optionData['name'],
                'type' => $optionData['type'] ?? 'select',
                'is_required' => (bool) ($optionData['is_required'] ?? false),
                'sort_order' => (int) ($optionData['sort_order'] ?? $optionIndex),
            ]);

            foreach (($optionData['values'] ?? []) as $valueIndex => $valueData) {
                if (blank($valueData['label'] ?? '') && blank($valueData['value'] ?? '')) {
                    continue;
                }

                $option->values()->create([
                    'label' => $valueData['label'] ?? '',
                    'value' => $valueData['value'] ?? '',
                    'price' => $valueData['price'] ?? 0,
                    'price_type' => $valueData['price_type'] ?? 'fixed',
                    'sort_order' => (int) ($valueData['sort_order'] ?? $valueIndex),
                    'is_default' => (bool) ($valueData['is_default'] ?? false),
                ]);
            }
        }
    }

    public function render()
    {
        $query = Product::query()
            ->with(['category', 'configOptions.values'])
            ->when($this->search, function ($query) {
                $query->where(function ($searchQuery) {
                    $searchQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%");
                });
            })
            ->when($this->categoryId, function ($query) {
                $query->where('category_id', $this->categoryId);
            })
            ->when($this->status !== 'all', function ($query) {
                if ($this->status === 'visible') {
                    $query->where('is_visible', true);
                } elseif ($this->status === 'hidden') {
                    $query->where('is_visible', false);
                }
            });

        $products = $query->latest()->paginate(10);
        $categories = Category::visible()->orderBy('name')->get();

        return view('livewire.admin.products', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
