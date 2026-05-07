@extends('layouts.admin')

@section('header', 'Orders')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search orders..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            
            <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="suspended">Suspended</option>
                <option value="cancelled">Cancelled</option>
            </select>
            
            <select wire:model="userId" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Customers</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->getFullName() }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Order #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $order->user->getFullName() }}</p>
                                <p class="text-xs text-gray-500">{{ $order->user->email }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $order->items->count() }} items
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                            ${{ number_format($order->total, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($order->status === 'active') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'suspended') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $order->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button wire:click="viewOrder({{ $order->id }})" class="text-blue-600 hover:text-blue-800 mr-2">View</button>
                            
                            @if($order->status === 'pending')
                                <button wire:click="confirmActivate({{ $order->id }})" class="text-green-600 hover:text-green-800 mr-2">Activate</button>
                            @endif
                            
                            @if($order->status === 'active')
                                <button wire:click="confirmSuspend({{ $order->id }})" class="text-yellow-600 hover:text-yellow-800 mr-2">Suspend</button>
                            @endif
                            
                            @if(in_array($order->status, ['pending', 'active']))
                                <button wire:click="confirmCancel({{ $order->id }})" class="text-red-600 hover:text-red-800">Cancel</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $orders->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal && $editingOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        @if($modalAction === 'view')
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Details</h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-500">Order Number</p>
                                    <p class="font-medium">{{ $editingOrder->order_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($editingOrder->status === 'active') bg-green-100 text-green-800
                                        @elseif($editingOrder->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($editingOrder->status === 'suspended') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($editingOrder->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Customer</p>
                                    <p class="font-medium">{{ $editingOrder->user->getFullName() }}</p>
                                    <p class="text-sm text-gray-500">{{ $editingOrder->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total</p>
                                    <p class="font-medium">${{ number_format($editingOrder->total, 2) }}</p>
                                </div>
                            </div>

                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Order Items</h4>
                            <table class="min-w-full mb-4">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Product</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Price</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Setup Fee</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($editingOrder->items as $item)
                                        <tr>
                                            <td class="px-3 py-2 text-sm">{{ $item->product_name }}</td>
                                            <td class="px-3 py-2 text-sm">${{ number_format($item->price, 2) }}</td>
                                            <td class="px-3 py-2 text-sm">${{ number_format($item->setup_fee, 2) }}</td>
                                        </tr>
                                        @if(!empty($item->config_summary))
                                            <tr>
                                                <td colspan="3" class="px-3 pb-3 text-xs text-gray-500">
                                                    @foreach($item->config_summary as $config)
                                                        <span class="mr-3 inline-block">
                                                            {{ $config['option'] }}: {{ $config['value'] }}
                                                            @if($config['price'] != 0)
                                                                ({{ $config['price_type'] === 'percentage' ? '+' . number_format($config['price'], 2) . '%' : '+$' . number_format($config['price'], 2) }})
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>

                            @if($editingOrder->services->count() > 0)
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Services</h4>
                                <div class="space-y-2">
                                    @foreach($editingOrder->services as $service)
                                        <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                            <span>{{ $service->name }}</span>
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if($service->status === 'active') bg-green-100 text-green-800
                                                @elseif($service->status === 'suspended') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($service->status) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                {{ ucfirst($modalAction) }} Order
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Are you sure you want to {{ $modalAction }} order <strong>{{ $editingOrder->order_number }}</strong>?
                            </p>
                            
                            @if(in_array($modalAction, ['suspend', 'cancel']))
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason/Note (optional)</label>
                                    <textarea wire:model="modalNote" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"></textarea>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if($modalAction !== 'view')
                            <button wire:click="executeAction" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                {{ ucfirst($modalAction) }}
                            </button>
                        @endif
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
