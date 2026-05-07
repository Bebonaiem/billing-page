@extends('layouts.client')

@section('title', 'My Invoices')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">My Invoices</h1>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total Unpaid</p>
            <p class="text-2xl font-bold text-yellow-600">${{ number_format($totalUnpaid, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Overdue</p>
            <p class="text-2xl font-bold text-red-600">${{ number_format($totalOverdue, 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Invoice Count</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $invoices->total() }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex space-x-4">
        <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="all">All Invoices</option>
            <option value="unpaid">Unpaid</option>
            <option value="paid">Paid</option>
            <option value="cancelled">Cancelled</option>
        </select>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Due Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($invoices as $invoice)
                    <tr class="{{ $invoice->isOverdue() ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                            {{ $invoice->invoice_number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $invoice->invoice_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm {{ $invoice->isOverdue() ? 'text-red-600 font-medium' : 'text-gray-600 dark:text-gray-300' }}">
                            {{ $invoice->due_date->format('M d, Y') }}
                            @if($invoice->isOverdue())
                                <span class="text-xs">(Overdue)</span>
                            @endif
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
                                @elseif($invoice->status === 'cancelled') bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($invoice->status === 'unpaid')
                                <button wire:click="payInvoice({{ $invoice->id }})" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-sm mr-2">Pay Now</button>
                            @endif
                            <a href="{{ route('client.invoices.print', $invoice) }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">View Invoice</a>
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

    <!-- Payment Modal -->
    @if($showPaymentModal && $payingInvoice)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closePaymentModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pay Invoice</h3>
                        
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded mb-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-600 dark:text-gray-400">Invoice</span>
                                <span class="font-medium">{{ $payingInvoice->invoice_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Amount Due</span>
                                <span class="text-xl font-bold text-gray-800 dark:text-white">${{ number_format($payingInvoice->balance, 2) }}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Select Payment Method</p>
                            
                            @foreach($gateways as $gateway)
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 {{ $selectedGateway === $gateway->id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                    <input type="radio" wire:model="selectedGateway" value="{{ $gateway->id }}" class="h-4 w-4 text-blue-600">
                                    <div class="ml-3">
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $gateway->display_name }}</p>
                                        @if($gateway->driver === 'account_credit')
                                            <p class="text-sm text-gray-500">Balance: ${{ number_format(auth()->user()->getCreditBalance(), 2) }}</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        @if($paymentNotice)
                            <div class="mt-4 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 dark:border-blue-800 dark:bg-blue-900/20 dark:text-blue-100">
                                <p class="font-semibold">{{ $paymentNotice['title'] }}</p>
                                <p class="mt-2 whitespace-pre-line">{{ $paymentNotice['message'] }}</p>
                                <p class="mt-2 text-xs uppercase tracking-wide">Reference: {{ $paymentNotice['reference'] }}</p>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mt-4 p-3 bg-red-100 text-red-700 rounded">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex flex-row-reverse">
                        <button wire:click="processPayment" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Pay ${{ number_format($payingInvoice->balance, 2) }}
                        </button>
                        <button wire:click="closePaymentModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
