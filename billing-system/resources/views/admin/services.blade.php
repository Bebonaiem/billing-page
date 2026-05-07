@extends('layouts.admin')

@section('title', 'Services')

@section('header', 'Services Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Total Services</p>
                    <p class="text-2xl font-bold accent-text">24</p>
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
                    <p class="text-secondary text-sm">Active</p>
                    <p class="text-2xl font-bold accent-text">18</p>
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
                    <p class="text-secondary text-sm">Maintenance</p>
                    <p class="text-2xl font-bold accent-text">4</p>
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
                    <p class="text-secondary text-sm">Disabled</p>
                    <p class="text-2xl font-bold accent-text">2</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="glass-effect rounded-2xl border border-custom shadow-glow">
        <div class="px-6 py-5 border-b border-custom flex items-center justify-between">
            <h3 class="text-xl font-semibold accent-text">Service Catalog</h3>
            <button class="accent-bg-hover text-white px-4 py-2 rounded-lg transition-all duration-200">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Service
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Service Card 1 -->
                <div class="glass-effect rounded-xl border border-custom p-4 hover:shadow-glow transition-all duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium success-bg text-white">Active</span>
                        <span class="text-lg font-bold accent-text">$29.99/mo</span>
                    </div>
                    <h4 class="text-white font-semibold mb-2">Web Hosting Pro</h4>
                    <p class="text-secondary text-sm mb-3">Professional web hosting with unlimited bandwidth</p>
                    <div class="flex space-x-2">
                        <button class="flex-1 bg-card/50 border border-custom text-secondary hover:text-white px-3 py-1 rounded-lg transition-all duration-200 text-sm">Edit</button>
                        <button class="flex-1 accent-bg-hover text-white px-3 py-1 rounded-lg transition-all duration-200 text-sm">Manage</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
