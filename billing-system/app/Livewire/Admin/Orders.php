<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\ProductConfigOptionValue;
use App\Models\User;
use App\Services\Order\OrderService;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public ?int $userId = null;
    public bool $showModal = false;
    public ?Order $editingOrder = null;
    public string $modalAction = '';
    public string $modalNote = '';

    protected $queryString = ['search', 'status', 'userId'];

    public function mount()
    {
        $this->editingOrder = null;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewOrder(int $orderId)
    {
        $this->editingOrder = $this->hydrateOrderItems(
            Order::with(['user', 'items.product.configOptions.values', 'services'])->findOrFail($orderId)
        );
        $this->modalAction = 'view';
        $this->showModal = true;
    }

    protected function hydrateOrderItems(Order $order): Order
    {
        $valueIds = [];

        foreach ($order->items as $item) {
            foreach (($item->config_options ?? []) as $valueId) {
                if (!empty($valueId)) {
                    $valueIds[] = (int) $valueId;
                }
            }
        }

        $valueIds = array_unique($valueIds);
        if (empty($valueIds)) {
            return $order;
        }

        $values = ProductConfigOptionValue::with('configOption')
            ->whereIn('id', $valueIds)
            ->get()
            ->keyBy('id');

        foreach ($order->items as $item) {
            $summary = [];

            foreach (($item->config_options ?? []) as $valueId) {
                $value = $values->get((int) $valueId);
                if (!$value) {
                    continue;
                }

                $summary[] = [
                    'option' => $value->configOption?->name ?? 'Option',
                    'value' => $value->label,
                    'price' => (float) $value->price,
                    'price_type' => $value->price_type,
                ];
            }

            $item->setAttribute('config_summary', $summary);
        }

        return $order;
    }

    public function confirmActivate(int $orderId)
    {
        $this->editingOrder = Order::findOrFail($orderId);
        $this->modalAction = 'activate';
        $this->showModal = true;
    }

    public function confirmSuspend(int $orderId)
    {
        $this->editingOrder = Order::findOrFail($orderId);
        $this->modalAction = 'suspend';
        $this->showModal = true;
    }

    public function confirmCancel(int $orderId)
    {
        $this->editingOrder = Order::findOrFail($orderId);
        $this->modalAction = 'cancel';
        $this->showModal = true;
    }

    public function executeAction()
    {
        if (!$this->editingOrder) {
            return;
        }

        $service = new OrderService();

        match($this->modalAction) {
            'activate' => $service->activateOrder($this->editingOrder),
            'suspend' => $service->suspendOrder($this->editingOrder, $this->modalNote),
            'cancel' => $service->cancelOrder($this->editingOrder, 'immediate', $this->modalNote),
            default => null,
        };

        session()->flash('success', "Order {$this->modalAction}d successfully.");
        $this->closeModal();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingOrder = null;
        $this->modalAction = '';
        $this->modalNote = '';
    }

    public function render()
    {
        $query = Order::query()
            ->with(['user', 'items'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('order_number', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('email', 'like', "%{$this->search}%")
                                ->orWhere('name', 'like', "%{$this->search}%");
                        });
                    });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->userId, function ($query) {
                $query->where('user_id', $this->userId);
            });

        $orders = $query->latest()->paginate(15);
        $users = User::orderBy('name')->get();

        return view('livewire.admin.orders', [
            'orders' => $orders,
            'users' => $users,
        ]);
    }
}
