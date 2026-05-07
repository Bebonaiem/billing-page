@extends('layouts.client')

@section('title', 'Invoices')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
            Your Invoices
        </h1>
        <p class="text-secondary text-lg">View and manage your invoices in one place</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 success-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Paid Invoices</p>
                    <p class="text-2xl font-bold accent-text">12</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 warning-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Pending Invoices</p>
                    <p class="text-2xl font-bold accent-text">3</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Total Amount</p>
                    <p class="text-2xl font-bold accent-text">$2,450.00</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="glass-effect rounded-2xl border border-custom shadow-glow">
        <div class="px-6 py-5 border-b border-custom">
            <h3 class="text-xl font-semibold accent-text">Recent Invoices</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-custom">
                <thead class="bg-card/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="accent-text hover:opacity-80">#INV-001</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Jan 15, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Feb 15, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">$299.00</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium success-bg text-white">Paid</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="#" class="accent-text hover:opacity-80 mr-3">View</a>
                            <a href="#" class="text-secondary hover:text-white">Download</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="accent-text hover:opacity-80">#INV-002</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Jan 20, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Feb 20, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">$149.00</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium warning-bg text-white">Pending</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="#" class="accent-text hover:opacity-80 mr-3">View</a>
                            <a href="#" class="accent-bg-hover text-white px-3 py-1 rounded-lg text-xs">Pay Now</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="accent-text hover:opacity-80">#INV-003</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Jan 25, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Feb 25, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">$599.00</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium error-bg text-white">Overdue</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="#" class="accent-text hover:opacity-80 mr-3">View</a>
                            <a href="#" class="accent-bg-hover text-white px-3 py-1 rounded-lg text-xs">Pay Now</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Empty State (if no invoices) -->
    <div class="glass-effect rounded-2xl border border-custom p-12 text-center hidden">
        <div class="w-20 h-20 accent-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold accent-text mb-2">No invoices yet</h3>
        <p class="text-secondary mb-6">Your invoices will appear here once you make purchases.</p>
        <a href="{{ route('order') }}" class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            Browse Services
        </a>
    </div>
</div>
@endsection
