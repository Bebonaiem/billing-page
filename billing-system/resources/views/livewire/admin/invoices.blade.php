@extends('layouts.admin')

@section('header', 'Invoices')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search invoices..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            
            <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="all">All Status</option>
                <option value="unpaid">Unpaid</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
                <option value="refunded">Refunded</option>
            </select>
            
            <select wire:model="userId" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Customers</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->getFullName() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($invoices as $invoice)
                    <tr class="{{ $invoice->isOverdue() ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                            {{ $invoice->invoice_number }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800 dark:text-white">{{ $invoice->user->getFullName() }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->user->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                            ${{ number_format($invoice->total, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm {{ $invoice->balance > 0 ? 'text-red-600 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
                            ${{ number_format($invoice->balance, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($invoice->status === 'paid') bg-green-100 text-green-800
                                @elseif($invoice->status === 'unpaid') bg-yellow-100 text-yellow-800
                                @elseif($invoice->status === 'overdue' || $invoice->isOverdue()) bg-red-100 text-red-800
                                @elseif($invoice->status === 'cancelled') bg-gray-100 text-gray-800
                                @elseif($invoice->status === 'refunded') bg-blue-100 text-blue-800
                                @endif">
                                {{ ucfirst($invoice->status) }}
                                @if($invoice->isOverdue())
                                    (Overdue)
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm {{ $invoice->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
                            {{ $invoice->due_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button wire:click="viewInvoice({{ $invoice->id }})" class="text-blue-600 hover:text-blue-800 mr-2">View</button>
                            
                            @if($invoice->status === 'unpaid')
                                <button wire:click="markAsPaid({{ $invoice->id }})" class="text-green-600 hover:text-green-800 mr-2">Mark Paid</button>
                            @endif
                            
                            @if($invoice->status === 'unpaid')
                                <button wire:click="cancelInvoice({{ $invoice->id }})" class="text-red-600 hover:text-red-800">Cancel</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No invoices found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal && $editingInvoice)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Invoice Details</h3>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-sm text-gray-500">Invoice Number</p>
                                <p class="font-medium">{{ $editingInvoice->invoice_number }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($editingInvoice->status === 'paid') bg-green-100 text-green-800
                                    @elseif($editingInvoice->status === 'unpaid') bg-yellow-100 text-yellow-800
                                    @elseif($editingInvoice->status === 'overdue') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($editingInvoice->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Customer</p>
                                <p class="font-medium">{{ $editingInvoice->user->getFullName() }}</p>
                                <p class="text-sm text-gray-500">{{ $editingInvoice->user->email }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Invoice Date</p>
                                <p class="font-medium">{{ $editingInvoice->invoice_date->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4 mb-4 bg-gray-50 dark:bg-gray-700 p-4 rounded">
                            <div>
                                <p class="text-xs text-gray-500">Subtotal</p>
                                <p class="font-medium">${{ number_format($editingInvoice->subtotal, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Discount</p>
                                <p class="font-medium">${{ number_format($editingInvoice->discount, 2) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Tax</p>
                                <p class="font-medium">${{ number_format($editingInvoice->tax, 2) }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded">
                            <span class="font-medium">Total</span>
                            <span class="text-xl font-bold">${{ number_format($editingInvoice->total, 2) }}</span>
                        </div>

                        @if($editingInvoice->payments->count() > 0)
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Payments</h4>
                            <div class="space-y-2 mb-4">
                                @foreach($editingInvoice->payments as $payment)
                                    <div class="flex items-center justify-between rounded border border-gray-200 bg-gray-50 px-3 py-2 dark:border-gray-700 dark:bg-gray-700">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ ucfirst($payment->payment_method) }}</p>
                                            <p class="text-xs text-gray-500">{{ $payment->transaction_id ?? 'Manual payment' }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">${{ number_format($payment->amount, 2) }}</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($payment->status) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Invoice Items</h4>
                        <table class="min-w-full mb-4">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Description</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Qty</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Price</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($editingInvoice->items as $item)
                                    <tr>
                                        <td class="px-3 py-2 text-sm">{{ $item->description }}</td>
                                        <td class="px-3 py-2 text-sm">{{ $item->quantity }}</td>
                                        <td class="px-3 py-2 text-sm">${{ number_format($item->unit_price, 2) }}</td>
                                        <td class="px-3 py-2 text-sm font-medium">${{ number_format($item->total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeModal" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
