<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Invoice {{ $invoice->invoice_number }} | {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="no-print flex justify-between items-center mb-6">
            <a href="{{ route('client.invoices') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to invoices</a>
            <div class="flex items-center gap-3">
                <a href="{{ route('client.invoices.pdf', $invoice) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 hover:bg-gray-50">Download PDF</a>
                <button onclick="window.print()" class="rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Print / Save as PDF</button>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-200">
            <div class="bg-gray-900 px-8 py-6 text-white flex items-start justify-between gap-6">
                <div>
                    <p class="text-sm uppercase tracking-[0.2em] text-gray-300">Invoice</p>
                    <h1 class="mt-2 text-3xl font-bold">{{ $invoice->invoice_number }}</h1>
                    <p class="mt-2 text-gray-300">{{ config('app.name') }}</p>
                </div>
                <div class="text-right text-sm text-gray-300">
                    <p><span class="text-white">Date:</span> {{ $invoice->invoice_date->format('M d, Y') }}</p>
                    <p><span class="text-white">Due:</span> {{ $invoice->due_date->format('M d, Y') }}</p>
                    <p><span class="text-white">Status:</span> {{ ucfirst($invoice->status) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 px-8 py-6 border-b border-gray-200">
                <div>
                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Billed To</p>
                    <p class="font-semibold">{{ $invoice->user->getFullName() }}</p>
                    <p class="text-sm text-gray-600">{{ $invoice->user->email }}</p>
                    @if($invoice->user->address_line1)
                        <p class="mt-2 text-sm text-gray-600 leading-6">
                            {!! nl2br(e($invoice->user->getFormattedAddress())) !!}
                        </p>
                    @endif
                </div>
                <div class="md:text-right">
                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Summary</p>
                    <p class="text-sm text-gray-600">Amount Paid: <span class="font-semibold text-gray-900">${{ number_format($invoice->amount_paid, 2) }}</span></p>
                    <p class="text-sm text-gray-600">Balance: <span class="font-semibold text-gray-900">${{ number_format($invoice->balance, 2) }}</span></p>
                    @if($invoice->paid_date)
                        <p class="text-sm text-gray-600">Paid: <span class="font-semibold text-gray-900">{{ $invoice->paid_date->format('M d, Y') }}</span></p>
                    @endif
                </div>
            </div>

            <div class="px-8 py-6">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs uppercase tracking-wide text-gray-500">
                            <th class="py-3 pr-4">Description</th>
                            <th class="py-3 pr-4 text-center">Qty</th>
                            <th class="py-3 pr-4 text-right">Unit Price</th>
                            <th class="py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr class="border-b border-gray-100 align-top">
                                <td class="py-4 pr-4">
                                    <p class="font-medium">{{ $item->description }}</p>
                                    <p class="text-xs uppercase tracking-wide text-gray-500">{{ str_replace('_', ' ', $item->type) }}</p>
                                </td>
                                <td class="py-4 pr-4 text-center">{{ $item->quantity }}</td>
                                <td class="py-4 pr-4 text-right">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-4 text-right font-medium">${{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-8 flex justify-end">
                    <div class="w-full max-w-sm space-y-2 rounded-2xl bg-gray-50 p-5">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>${{ number_format($invoice->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Discount</span>
                            <span>- ${{ number_format($invoice->discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tax</span>
                            <span>${{ number_format($invoice->tax, 2) }}</span>
                        </div>
                        @if($invoice->late_fee_amount > 0)
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Late Fee</span>
                                <span>${{ number_format($invoice->late_fee_amount, 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between border-t border-gray-200 pt-3 text-lg font-bold">
                            <span>Total Due</span>
                            <span>${{ number_format($invoice->balance, 2) }}</span>
                        </div>
                    </div>
                </div>

                @if($invoice->payments->count() > 0)
                    <div class="mt-8">
                        <h2 class="text-lg font-semibold mb-3">Payments</h2>
                        <div class="space-y-2">
                            @foreach($invoice->payments as $payment)
                                <div class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3">
                                    <div>
                                        <p class="font-medium">{{ ucfirst($payment->payment_method) }}</p>
                                        <p class="text-xs text-gray-500">{{ $payment->transaction_id ?? 'Manual payment' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold">${{ number_format($payment->amount, 2) }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst($payment->status) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            if (window.location.search.includes('autoprint=1')) {
                window.print();
            }
        });
    </script>
</body>
</html>
