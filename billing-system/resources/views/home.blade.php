@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="gradient-bg text-white relative overflow-hidden">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative max-w-7xl mx-auto py-32 px-4 sm:px-6 lg:px-8 text-center">
        <div class="animate-float">
            <h1 class="text-6xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white to-blue-200">
                Professional Billing & Invoicing
            </h1>
        </div>
        <p class="text-xl mb-12 max-w-3xl mx-auto text-blue-100">
            Complete billing solution for service providers. Manage clients, invoices, payments, and automate your business.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('order') }}" class="bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                View Services
            </a>
            @guest
                <a href="{{ route('register') }}" class="accent-bg-hover text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Get Started Free
                </a>
            @endguest
        </div>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-transparent to-transparent"></div>
</div>

<!-- Features Section -->
<div class="py-24 bg-card">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <h2 class="text-4xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                Everything You Need to Grow
            </h2>
            <p class="text-xl text-secondary max-w-2xl mx-auto">
                Powerful features designed to streamline your billing workflow and help you get paid faster.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-card border border-custom rounded-2xl p-8 text-center hover:shadow-glow transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-20 h-20 accent-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold mb-4">Smart Invoicing</h3>
                <p class="text-secondary">
                    Create professional invoices, automate recurring billing, and send reminders automatically.
                </p>
            </div>
            
            <div class="bg-card border border-custom rounded-2xl p-8 text-center hover:shadow-glow transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-20 h-20 success-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold mb-4">Multiple Payment Gateways</h3>
                <p class="text-secondary">
                    Accept payments via Stripe, PayPal, bank transfers, and more. Your customers choose how to pay.
                </p>
            </div>
            
            <div class="bg-card border border-custom rounded-2xl p-8 text-center hover:shadow-glow transition-all duration-300 transform hover:-translate-y-2">
                <div class="w-20 h-20 warning-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold mb-4">Client Management</h3>
                <p class="text-secondary">
                    Keep track of all your clients, their services, and payment history in one centralized place.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Pricing Section -->
<div class="py-24 bg-gradient-to-br from-blue-900/50 to-purple-900/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <h2 class="text-4xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                Simple, Transparent Pricing
            </h2>
            <p class="text-xl text-secondary max-w-2xl mx-auto">
                No hidden fees. No per-seat pricing. Just one flat rate.
            </p>
        </div>
        
        <div class="max-w-4xl mx-auto">
            <div class="glass-effect rounded-3xl p-12 text-center border border-custom shadow-glow">
                <h3 class="text-3xl font-bold mb-4">Professional</h3>
                <p class="text-secondary mb-8">Everything you need to run your billing business</p>
                <div class="text-6xl font-bold mb-8 accent-text">
                    $29<span class="text-xl font-normal text-secondary">/month</span>
                </div>
                <ul class="text-left space-y-4 mb-12 max-w-md mx-auto">
                    <li class="flex items-center">
                        <svg class="w-6 h-6 success-bg rounded-full p-1 mr-4" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="text-white"></path>
                        </svg>
                        <span class="text-lg">Unlimited clients</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-6 h-6 success-bg rounded-full p-1 mr-4" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="text-white"></path>
                        </svg>
                        <span class="text-lg">Unlimited invoices</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-6 h-6 success-bg rounded-full p-1 mr-4" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="text-white"></path>
                        </svg>
                        <span class="text-lg">All payment gateways</span>
                    </li>
                    <li class="flex items-center">
                        <svg class="w-6 h-6 success-bg rounded-full p-1 mr-4" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="text-white"></path>
                        </svg>
                        <span class="text-lg">24/7 email support</span>
                    </li>
                </ul>
                <a href="{{ route('register') }}" class="accent-bg-hover text-white px-10 py-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Start Free Trial
                </a>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="gradient-bg text-white py-24">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-4xl font-bold mb-6">Ready to streamline your billing?</h2>
        <p class="text-xl mb-12 text-blue-100">
            Join thousands of businesses using BillingHub to manage their invoicing.
        </p>
        <a href="{{ route('register') }}" class="bg-white text-blue-600 px-10 py-4 rounded-xl font-semibold hover:bg-gray-100 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
            Get Started Now
        </a>
    </div>
</div>
@endsection
