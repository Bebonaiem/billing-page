@extends('layouts.admin')

@section('header', 'Products')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search products..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            
            <select wire:model="categoryId" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
            
            <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="all">All Status</option>
                <option value="visible">Visible</option>
                <option value="hidden">Hidden</option>
            </select>
        </div>
        
        <button wire:click="createProduct" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Add Product
        </button>
    </div>

    <!-- Products Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Billing</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Options</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($products as $product)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->slug }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $product->type)) }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $product->category?->name ?? 'Uncategorized' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">
                            ${{ number_format($product->price, 2) }}
                            @if($product->setup_fee > 0)
                                <p class="text-xs text-gray-500">+ ${{ number_format($product->setup_fee, 2) }} setup</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ ucfirst($product->billing_cycle) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $product->configOptions->count() }} option{{ $product->configOptions->count() === 1 ? '' : 's' }}
                        </td>
                        <td class="px-6 py-4">
                            <button wire:click="toggleVisibility({{ $product->id }})" class="px-2 py-1 text-xs rounded-full {{ $product->is_visible ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $product->is_visible ? 'Visible' : 'Hidden' }}
                            </button>
                            @if($product->stock_enabled)
                                <span class="ml-2 px-2 py-1 text-xs rounded-full {{ $product->stock_quantity > 0 ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                    Stock: {{ $product->stock_quantity }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button wire:click="editProduct({{ $product->id }})" class="text-blue-600 hover:text-blue-800 mr-3">Edit</button>
                            <button wire:click="deleteProduct({{ $product->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-800">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No products found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4" id="modal-title">
                            {{ $editingProduct ? 'Edit Product' : 'Create Product' }}
                        </h3>
                        
                        <form wire:submit.prevent="saveProduct" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input type="text" wire:model="form.name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                @error('form.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                                <input type="text" wire:model="form.slug" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                @error('form.slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                                <select wire:model="form.category_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
                                <select wire:model="form.type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                    <option value="game_server">Game Server</option>
                                    <option value="web_hosting">Web Hosting</option>
                                    <option value="vps">VPS</option>
                                    <option value="dedicated">Dedicated Server</option>
                                    <option value="domain">Domain</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price</label>
                                    <input type="number" step="0.01" wire:model="form.price" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                    @error('form.price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Setup Fee</label>
                                    <input type="number" step="0.01" wire:model="form.setup_fee" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Billing Cycle</label>
                                <select wire:model="form.billing_cycle" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                    <option value="hourly">Hourly</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="semi_annually">Semi-Annually</option>
                                    <option value="annually">Annually</option>
                                    <option value="biennially">Biennially</option>
                                    <option value="one_time">One Time</option>
                                </select>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="form.is_visible" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Visible</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="form.stock_enabled" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Stock Control</span>
                                </label>
                            </div>
                            
                            @if($form['stock_enabled'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Stock Quantity</label>
                                    <input type="number" wire:model="form.stock_quantity" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                </div>
                            @endif
                            
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="form.has_trial" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Free Trial</span>
                            </div>
                            
                            @if($form['has_trial'])
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Trial Days</label>
                                    <input type="number" wire:model="form.trial_days" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                </div>
                            @endif

                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white">Configurable Options</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Define selectable upgrades, required fields, and add-on pricing.</p>
                                    </div>
                                    <button type="button" wire:click="addConfigOption" class="px-3 py-2 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 text-sm">
                                        + Add Option
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    @forelse($configOptions as $optionIndex => $option)
                                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-4" wire:key="config-option-{{ $optionIndex }}">
                                            <div class="flex items-center justify-between gap-3 mb-4">
                                                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 flex-1">
                                                    <input type="text" wire:model="configOptions.{{ $optionIndex }}.name" placeholder="Option name" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                                    <select wire:model="configOptions.{{ $optionIndex }}.type" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                                        <option value="select">Select</option>
                                                        <option value="radio">Radio</option>
                                                        <option value="number">Number</option>
                                                        <option value="text">Text</option>
                                                        <option value="checkbox">Checkbox</option>
                                                    </select>
                                                    <input type="number" wire:model="configOptions.{{ $optionIndex }}.sort_order" placeholder="Sort" class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                                        <input type="checkbox" wire:model="configOptions.{{ $optionIndex }}.is_required" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                                        Required
                                                    </label>
                                                </div>
                                                <button type="button" wire:click="removeConfigOption({{ $optionIndex }})" class="text-sm text-red-600 hover:text-red-700">
                                                    Remove
                                                </button>
                                            </div>

                                            <div class="space-y-3">
                                                <div class="flex items-center justify-between">
                                                    <h5 class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Values</h5>
                                                    <button type="button" wire:click="addConfigOptionValue({{ $optionIndex }})" class="text-xs text-blue-600 hover:text-blue-700">
                                                        + Add Value
                                                    </button>
                                                </div>

                                                <div class="space-y-3">
                                                    @foreach($option['values'] as $valueIndex => $value)
                                                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-start" wire:key="config-option-{{ $optionIndex }}-value-{{ $valueIndex }}">
                                                            <input type="text" wire:model="configOptions.{{ $optionIndex }}.values.{{ $valueIndex }}.label" placeholder="Label" class="md:col-span-3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                                            <input type="text" wire:model="configOptions.{{ $optionIndex }}.values.{{ $valueIndex }}.value" placeholder="Value" class="md:col-span-3 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                                            <input type="number" step="0.01" wire:model="configOptions.{{ $optionIndex }}.values.{{ $valueIndex }}.price" placeholder="Price" class="md:col-span-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                                            <select wire:model="configOptions.{{ $optionIndex }}.values.{{ $valueIndex }}.price_type" class="md:col-span-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm text-sm">
                                                                <option value="fixed">Fixed</option>
                                                                <option value="percentage">Percentage</option>
                                                            </select>
                                                            <div class="md:col-span-1 flex items-center justify-end gap-2">
                                                                <label class="flex items-center text-xs text-gray-600 dark:text-gray-300 gap-1">
                                                                    <input type="checkbox" wire:model="configOptions.{{ $optionIndex }}.values.{{ $valueIndex }}.is_default" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                                                    Default
                                                                </label>
                                                                <button type="button" wire:click="removeConfigOptionValue({{ $optionIndex }}, {{ $valueIndex }})" class="text-xs text-red-600 hover:text-red-700">
                                                                    Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="rounded-lg border border-dashed border-gray-300 dark:border-gray-600 p-4 text-sm text-gray-500 dark:text-gray-400">
                                            No configurable options yet. Add one to let customers customize this product.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="saveProduct" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
