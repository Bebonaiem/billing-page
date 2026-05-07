@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-8 bg-card text-white">
    <!-- Invoice Header -->
    <div class="border-b border-custom pb-8 mb-8">
        <div class="flex justify-between items-start">
            <div>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold accent-text">INVOICE</h1>
                </div>
                <p class="text-secondary">Invoice #{{ $invoice->id }}</p>
            </div>
            <div class="text-right">
                <div class="text-sm">
                    <p><strong class="text-secondary">Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                    <p><strong class="text-secondary">Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                    <p><strong class="text-secondary">Status:</strong> <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $invoice->status === 'paid' ? 'success-bg' : ($invoice->status === 'pending' ? 'warning-bg' : 'error-bg') }} text-white">{{ ucfirst($invoice->status) }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bill To Section -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold accent-text mb-4">Bill To:</h2>
        <div class="bg-card/50 border border-custom p-4 rounded-xl">
            <p class="font-medium text-lg">{{ $invoice->user->name }}</p>
            <p class="text-secondary">{{ $invoice->user->email }}</p>
            <p class="text-secondary">{{ $invoice->user->address ?? 'N/A' }}</p>
        </div>
    </div>

    <!-- Invoice Items -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold accent-text mb-4">Invoice Items:</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-custom">
                <thead class="bg-card/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->description }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">${{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">${{ number_format($item->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary -->
    <div class="mb-8">
        <div class="flex justify-end">
            <div class="w-full max-w-xs">
                <div class="glass-effect rounded-xl p-4 border border-custom">
                    <div class="flex justify-between mb-2">
                        <span class="text-secondary">Subtotal:</span>
                        <span class="font-medium">${{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-secondary">Tax:</span>
                        <span class="font-medium">${{ number_format($invoice->tax, 2) }}</span>
                    </div>
                    <div class="flex justify-between mb-2">
                        <span class="text-secondary">Discount:</span>
                        <span class="font-medium">-${{ number_format($invoice->discount, 2) }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-custom">
                        <span class="font-semibold">Total:</span>
                        <span class="font-bold text-lg accent-text">${{ number_format($invoice->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    @if ($invoice->payments->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xl font-semibold accent-text mb-4">Payment History:</h2>
            <div class="bg-card/50 border border-custom p-4 rounded-xl">
                @foreach ($invoice->payments as $payment)
                    <div class="flex justify-between mb-2">
                        <div>
                            <p class="font-medium">{{ $payment->method }}</p>
                            <p class="text-sm text-secondary">{{ $payment->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <span class="font-medium">${{ number_format($payment->amount, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="border-t border-custom pt-8">
        <div class="text-center text-sm">
            <p class="accent-text">Thank you for your business!</p>
            <p class="text-secondary mt-2">For questions, please contact support@billinghub.com</p>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>
@endsection
