@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Welcome Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                Welcome back, {{ Auth::user()->name }}!
            </h1>
            <p class="text-secondary text-lg">
                Here's what's happening with your billing system today.
            </p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-card border border-custom rounded-2xl p-6 hover:shadow-glow transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-secondary truncate">Total Clients</dt>
                            <dd class="text-2xl font-bold accent-text">128</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-card border border-custom rounded-2xl p-6 hover:shadow-glow transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 success-bg rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-secondary truncate">Active Services</dt>
                            <dd class="text-2xl font-bold accent-text">342</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-card border border-custom rounded-2xl p-6 hover:shadow-glow transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 warning-bg rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-secondary truncate">Monthly Revenue</dt>
                            <dd class="text-2xl font-bold accent-text">$12,450</dd>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="bg-card border border-custom rounded-2xl p-6 hover:shadow-glow transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 error-bg rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-secondary truncate">Pending Invoices</dt>
                            <dd class="text-2xl font-bold accent-text">23</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow mb-8">
            <div class="px-6 py-5 border-b border-custom">
                <h3 class="text-xl font-semibold accent-text">Recent Activity</h3>
            </div>
            <div class="divide-y divide-custom">
                <div class="px-6 py-4 hover:bg-card/50 transition-colors duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 accent-bg rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">New client registration</p>
                                <p class="text-sm text-secondary">John Doe signed up 5 minutes ago</p>
                            </div>
                        </div>
                        <span class="text-xs text-tertiary">5m ago</span>
                    </div>
                </div>
                <div class="px-6 py-4 hover:bg-card/50 transition-colors duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 success-bg rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">Payment received</p>
                                <p class="text-sm text-secondary">$250.00 from Sarah Smith for invoice #1234</p>
                            </div>
                        </div>
                        <span class="text-xs text-tertiary">1h ago</span>
                    </div>
                </div>
                <div class="px-6 py-4 hover:bg-card/50 transition-colors duration-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 warning-bg rounded-lg flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium">Service created</p>
                                <p class="text-sm text-secondary">Minecraft server created for Mike Johnson</p>
                            </div>
                        </div>
                        <span class="text-xs text-tertiary">2h ago</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow">
            <div class="px-6 py-5 border-b border-custom">
                <h3 class="text-xl font-semibold accent-text">Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @if(Auth::user()->is_admin)
                        <a href="{{ route('admin.users.index') }}" class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                            Manage Users
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="success-bg hover:opacity-90 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                            Add Product
                        </a>
                        <a href="{{ route('admin.invoices.index') }}" class="warning-bg hover:opacity-90 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                            View Invoices
                        </a>
                        <a href="{{ route('admin.orders.index') }}" class="error-bg hover:opacity-90 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                            Recent Orders
                        </a>
                    @else
                        <a href="{{ route('client.services') }}" class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                            My Services
                        </a>
                        <a href="{{ route('client.invoices') }}" class="success-bg hover:opacity-90 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                            My Invoices
                        </a>
                        <a href="{{ route('order') }}" class="warning-bg hover:opacity-90 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                            Order Services
                        </a>
                        <a href="{{ route('client.tickets') }}" class="error-bg hover:opacity-90 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 text-center">
                            Support Tickets
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
