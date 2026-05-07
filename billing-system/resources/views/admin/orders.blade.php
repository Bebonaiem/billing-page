@extends('layouts.admin')

@section('title', 'Orders')

@section('header', 'Orders Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Total Orders</p>
                    <p class="text-2xl font-bold accent-text">3,456</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 success-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Completed</p>
                    <p class="text-2xl font-bold accent-text">2,847</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 warning-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Processing</p>
                    <p class="text-2xl font-bold accent-text">489</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 error-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Cancelled</p>
                    <p class="text-2xl font-bold accent-text">120</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="glass-effect rounded-2xl border border-custom shadow-glow">
        <div class="px-6 py-5 border-b border-custom flex items-center justify-between">
            <h3 class="text-xl font-semibold accent-text">Recent Orders</h3>
            <div class="flex space-x-3">
                <button class="bg-card/50 border border-custom text-secondary hover:text-white px-4 py-2 rounded-lg transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                    </svg>
                    Filter
                </button>
                <button class="accent-bg-hover text-white px-4 py-2 rounded-lg transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-custom">
                <thead class="bg-card/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="accent-text hover:opacity-80">#ORD-001</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 accent-bg rounded-full flex items-center justify-center mr-2">
                                    <span class="text-white text-xs font-medium">JD</span>
                                </div>
                                <div class="text-sm">John Doe</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Web Hosting Pro</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">$29.99</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium success-bg text-white">Completed</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Jan 25, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="#" class="accent-text hover:opacity-80 mr-3">View</a>
                            <a href="#" class="text-secondary hover:text-white">Invoice</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="accent-text hover:opacity-80">#ORD-002</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 success-bg rounded-full flex items-center justify-center mr-2">
                                    <span class="text-white text-xs font-medium">JS</span>
                                </div>
                                <div class="text-sm">Jane Smith</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Cloud Storage</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">$9.99</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium warning-bg text-white">Processing</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Jan 24, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="#" class="accent-text hover:opacity-80 mr-3">View</a>
                            <a href="#" class="text-secondary hover:text-white">Process</a>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="accent-text hover:opacity-80">#ORD-003</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 warning-bg rounded-full flex items-center justify-center mr-2">
                                    <span class="text-white text-xs font-medium">MB</span>
                                </div>
                                <div class="text-sm">Mike Brown</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Mobile App Dev</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">$499.00</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium error-bg text-white">Cancelled</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-secondary">Jan 23, 2024</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="#" class="accent-text hover:opacity-80 mr-3">View</a>
                            <a href="#" class="text-secondary hover:text-white">Refund</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
