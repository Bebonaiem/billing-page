@extends('layouts.client')

@section('title', 'Services')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
            Your Services
        </h1>
        <p class="text-secondary text-lg">Manage your active services and subscriptions</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Total Services</p>
                    <p class="text-2xl font-bold accent-text">8</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 success-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Active</p>
                    <p class="text-2xl font-bold accent-text">6</p>
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
                    <p class="text-secondary text-sm">Pending</p>
                    <p class="text-2xl font-bold accent-text">2</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 error-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Expired</p>
                    <p class="text-2xl font-bold accent-text">0</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Service Card 1 -->
        <div class="glass-effect rounded-2xl border border-custom p-6 hover:shadow-glow transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium success-bg text-white">Active</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">Web Hosting Pro</h3>
            <p class="text-secondary text-sm mb-4">Professional web hosting with unlimited bandwidth</p>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-secondary">Next billing:</span>
                <span class="text-sm font-medium">Feb 15, 2024</span>
            </div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-secondary">Monthly cost:</span>
                <span class="text-lg font-bold accent-text">$29.99</span>
            </div>
            <div class="flex space-x-2">
                <button class="flex-1 bg-card/50 border border-custom text-secondary hover:text-white px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    Manage
                </button>
                <button class="flex-1 accent-bg-hover text-white px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    Renew
                </button>
            </div>
        </div>

        <!-- Service Card 2 -->
        <div class="glass-effect rounded-2xl border border-custom p-6 hover:shadow-glow transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                    </svg>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium success-bg text-white">Active</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">Cloud Storage</h3>
            <p class="text-secondary text-sm mb-4">100GB secure cloud storage with automatic backup</p>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-secondary">Next billing:</span>
                <span class="text-sm font-medium">Jan 20, 2024</span>
            </div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-secondary">Monthly cost:</span>
                <span class="text-lg font-bold accent-text">$9.99</span>
            </div>
            <div class="flex space-x-2">
                <button class="flex-1 bg-card/50 border border-custom text-secondary hover:text-white px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    Manage
                </button>
                <button class="flex-1 accent-bg-hover text-white px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    Renew
                </button>
            </div>
        </div>

        <!-- Service Card 3 -->
        <div class="glass-effect rounded-2xl border border-custom p-6 hover:shadow-glow transition-all duration-300 transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium warning-bg text-white">Pending</span>
            </div>
            <h3 class="text-xl font-semibold mb-2">Mobile App</h3>
            <p class="text-secondary text-sm mb-4">Custom mobile application development service</p>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-secondary">Setup date:</span>
                <span class="text-sm font-medium">Jan 25, 2024</span>
            </div>
            <div class="flex items-center justify-between mb-4">
                <span class="text-sm text-secondary">One-time cost:</span>
                <span class="text-lg font-bold accent-text">$499.00</span>
            </div>
            <div class="flex space-x-2">
                <button class="flex-1 bg-card/50 border border-custom text-secondary hover:text-white px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    View Details
                </button>
                <button class="flex-1 accent-bg-hover text-white px-3 py-2 rounded-lg transition-all duration-200 text-sm">
                    Activate
                </button>
            </div>
        </div>
    </div>

    <!-- Empty State (if no services) -->
    <div class="glass-effect rounded-2xl border border-custom p-12 text-center hidden">
        <div class="w-20 h-20 accent-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold accent-text mb-2">No services yet</h3>
        <p class="text-secondary mb-6">Start by purchasing one of our amazing services.</p>
        <a href="{{ route('order') }}" class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            Browse Services
        </a>
    </div>
</div>
@endsection
