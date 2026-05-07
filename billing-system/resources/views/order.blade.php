@extends('layouts.app')

@section('content')
<!-- Order Section -->
<div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Browse Products</h1>
            <p class="mt-2 text-gray-600">Choose a plan, add it to your cart, and continue to checkout when you are ready.</p>
        </div>
        <a href="{{ route('checkout') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-5 py-3 font-semibold text-white hover:bg-blue-700">
            Go to Checkout
        </a>
    </div>

    <div class="grid grid-cols-1 gap-8 md:grid-cols-2 xl:grid-cols-3">
        @forelse($products as $product)
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 flex flex-col">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                            {{ $product->category?->name ?? 'Service Plan' }}
                        </p>
                        <h2 class="mt-1 text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                    </div>
                    @if($product->stock_enabled)
                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->stock_quantity > 0 ? 'In stock' : 'Sold out' }}
                        </span>
                    @endif
                </div>

                <p class="mb-5 text-sm leading-6 text-gray-600">
                    {{ $product->short_description ?: $product->description ?: 'A flexible service plan ready to be customized for your needs.' }}
                </p>

                <div class="mb-5 rounded-xl bg-gray-50 p-4">
                    <div class="flex items-baseline gap-2">
                        <span class="text-4xl font-extrabold text-gray-900">${{ number_format((float) $product->price, 2) }}</span>
                        <span class="text-sm text-gray-500">/{{ str_replace('_', ' ', $product->billing_cycle) }}</span>
                    </div>
                    @if((float) $product->setup_fee > 0)
                        <p class="mt-2 text-sm text-gray-500">+ ${{ number_format((float) $product->setup_fee, 2) }} setup fee</p>
                    @endif
                </div>

                <div class="mb-6 flex-1 space-y-2 text-sm text-gray-600">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span>Type</span>
                        <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $product->type)) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span>Billing</span>
                        <span class="font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $product->billing_cycle)) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Visibility</span>
                        <span class="font-medium text-gray-900">{{ $product->is_visible ? 'Public' : 'Hidden' }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('cart.add', $product) }}" class="mt-auto">
                    @csrf
                    @if($product->configOptions->isNotEmpty())
                        <div class="mb-6 space-y-4 rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <h3 class="text-sm font-semibold uppercase tracking-wide text-gray-600">Configuration</h3>
                            @foreach($product->configOptions as $option)
                                @php
                                    $defaultValueId = $option->values->firstWhere('is_default', true)?->id ?? $option->values->first()?->id;
                                @endphp
                                <div>
                                    <label class="mb-2 block text-sm font-medium text-gray-800">
                                        {{ $option->name }}
                                        @if($option->is_required)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>

                                    <select name="config_options[{{ $option->id }}]" class="block w-full rounded-lg border-gray-300 bg-white text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500" @required($option->is_required)>
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
                    <button type="submit" class="block w-full rounded-lg bg-blue-600 py-3 text-center font-semibold text-white hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-gray-400" @disabled($product->stock_enabled && $product->stock_quantity <= 0)>
                        Add to Cart
                    </button>
                </form>
            </div>
        @empty
            <div class="md:col-span-2 xl:col-span-3 rounded-2xl border border-dashed border-gray-300 bg-white p-10 text-center">
                <h2 class="text-xl font-semibold text-gray-900">No products are available yet</h2>
                <p class="mt-2 text-gray-600">Create visible products in the admin panel to make the catalog available for customers.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
