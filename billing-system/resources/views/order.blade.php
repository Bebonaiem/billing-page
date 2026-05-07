@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-900/20 to-purple-900/20">
    <!-- Hero Section -->
    <div class="gradient-bg text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-black opacity-20"></div>
        <div class="relative max-w-7xl mx-auto py-24 px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <div class="animate-float">
                    <h1 class="text-5xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white to-blue-200">
                        Our Services
                    </h1>
                </div>
                <p class="text-xl mb-8 text-blue-100">Choose the perfect plan for your needs</p>
            </div>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold mb-4 accent-text">Select Your Perfect Plan</h2>
            <p class="text-secondary text-lg">Flexible pricing options designed for every business size</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($products as $product)
                <div class="bg-card border border-custom rounded-2xl p-8 hover:shadow-glow transition-all duration-300 transform hover:-translate-y-2">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 accent-bg rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">{{ $product->name }}</h3>
                        <p class="text-secondary mb-6">{{ $product->short_description ?: $product->description ?: 'A flexible service plan ready to be customized for your needs.' }}</p>
                        <div class="text-4xl font-bold accent-text mb-6">${{ number_format((float) $product->price, 2) }}<span class="text-lg font-normal text-secondary">/{{ str_replace('_', ' ', $product->billing_cycle) }}</span></div>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 success-bg rounded-full p-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="text-white"></path>
                            </svg>
                            <span class="text-lg">{{ $product->category?->name ?? 'Service Plan' }}</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 success-bg rounded-full p-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="text-white"></path>
                            </svg>
                            <span class="text-lg">{{ $product->type }}</span>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 success-bg rounded-full p-1 mr-3" fill="none" stroke="currentColor" viewBox="0 0 20 20">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" class="text-white"></path>
                            </svg>
                            <span class="text-lg">{{ $product->billing_cycle }}</span>
                        </li>
                    </ul>
                    <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-auto">
                        @csrf
                        @if($product->configOptions->isNotEmpty())
                            <div class="mb-6 space-y-4 rounded-xl border border-custom bg-card/50 p-4">
                                <h3 class="text-sm font-semibold uppercase tracking-wide text-secondary">Configuration</h3>
                                @foreach($product->configOptions as $option)
                                    @php
                                        $defaultValueId = $option->values->firstWhere('is_default', true)?->id ?? $option->values->first()?->id;
                                    @endphp
                                    <div>
                                        <label class="mb-2 block text-sm font-medium text-secondary">
                                            {{ $option->name }}
                                            @if($option->is_required)
                                                <span class="text-red-400">*</span>
                                            @endif
                                        </label>
                                        <select name="config_options[{{ $option->id }}]" class="block w-full rounded-lg border-custom bg-card text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" @required($option->is_required)>
                                            @if(!$option->is_required)
                                                <option value="">No selection</option>
                                            @endif
                                            @foreach($option->values as $value)
                                                <option value="{{ $value->id }}" @selected((string) $defaultValueId === (string) $value->id)>
                                                    {{ $value->label }}
                                                    @if((float) $value->price !== 0.0)
                                                        ({{ $value->price_type === 'percentage' ? '+' . number_format((float) $value->price, 2) . '%' : '$' . number_format((float) $value->price, 2) }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        <button type="submit" class="w-full accent-bg-hover text-white py-3 px-4 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Add to Cart
                        </button>
                    </form>
                </div>
            @empty
                <div class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-custom bg-card p-10 text-center">
                    <h2 class="text-xl font-semibold text-white">No products are available yet</h2>
                    <p class="mt-2 text-secondary">Create visible products in the admin panel to make the catalog available for customers.</p>
                </div>
            @endforelse
        </div>

        <!-- Additional Features -->
        <div class="mt-20 text-center">
            <h3 class="text-2xl font-bold mb-8 accent-text">All Plans Include</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <div class="flex items-center justify-center space-x-3">
                    <div class="w-12 h-12 accent-bg rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <span class="text-lg">SSL Certificate</span>
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <div class="w-12 h-12 accent-bg rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <span class="text-lg">Daily Backups</span>
                </div>
                <div class="flex items-center justify-center space-x-3">
                    <div class="w-12 h-12 accent-bg rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <span class="text-lg">99.9% Uptime</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
