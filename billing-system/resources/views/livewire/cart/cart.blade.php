<div class="relative">
    <!-- Cart Toggle Button -->
    <button wire:click="toggleCart" class="relative p-2 text-secondary hover:text-white transition-all duration-200 glass-effect rounded-lg border border-custom">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        @if($this->totals['item_count'] > 0)
            <span class="absolute -top-1 -right-1 error-bg text-white text-xs rounded-full w-5 h-5 flex items-center justify-center animate-pulse">
                {{ $this->totals['item_count'] }}
            </span>
        @endif
    </button>

    <!-- Cart Slide-out Panel -->
    @if($showCart)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-black/80 backdrop-blur-sm transition-opacity" wire:click="toggleCart"></div>
                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="w-screen max-w-md transform transition ease-in-out duration-500 sm:duration-700 translate-x-0">
                        <div class="h-full flex flex-col glass-effect shadow-glow overflow-y-scroll">
                            <div class="flex-1 py-6 overflow-y-auto px-4 sm:px-6">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-xl font-bold accent-text" id="slide-over-title">Shopping Cart</h2>
                                    <div class="ml-3 h-7 flex items-center">
                                        <button wire:click="toggleCart" class="p-2 text-secondary hover:text-white rounded-lg transition-all duration-200">
                                            <span class="sr-only">Close panel</span>
                                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-8">
                                    <div class="flow-root">
                                        @if(!empty($cart['items']))
                                            <ul class="-my-6 divide-y divide-custom">
                                                @foreach($cart['items'] as $itemId => $item)
                                                    <li class="py-6">
                                                        <div class="glass-effect rounded-xl p-4 border border-custom">
                                                            <div class="flex justify-between items-start mb-3">
                                                                <div>
                                                                    <h3 class="text-lg font-semibold text-white">{{ $item['product_name'] }}</h3>
                                                                    <p class="text-sm text-secondary mt-1">{{ $item['billing_cycle'] }}</p>
                                                                </div>
                                                                <p class="text-xl font-bold accent-text">${{ number_format($item['subtotal'], 2) }}</p>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <div class="flex items-center space-x-3">
                                                                    <button wire:click="updateQuantity('{{ $itemId }}', {{ $item['quantity'] - 1 }})" class="w-8 h-8 accent-bg hover:opacity-80 text-white rounded-lg transition-all duration-200 flex items-center justify-center">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                                        </svg>
                                                                    </button>
                                                                    <span class="text-lg font-medium text-white min-w-[2rem] text-center">{{ $item['quantity'] }}</span>
                                                                    <button wire:click="updateQuantity('{{ $itemId }}', {{ $item['quantity'] + 1 }})" class="w-8 h-8 accent-bg hover:opacity-80 text-white rounded-lg transition-all duration-200 flex items-center justify-center">
                                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                                <button wire:click="removeItem('{{ $itemId }}')" type="button" class="error-bg hover:opacity-80 text-white px-3 py-1 rounded-lg transition-all duration-200 text-sm">
                                                                    Remove
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <div class="text-center py-12">
                                                <div class="w-16 h-16 bg-card/50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                                    <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-secondary text-lg">Your cart is empty</p>
                                                <p class="text-tertiary text-sm mt-2">Add some services to get started!</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(!empty($cart['items']))
                                <div class="border-t border-custom py-6 px-4 sm:px-6">
                                    <!-- Coupon Code -->
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-white mb-2">Coupon Code</label>
                                        <div class="flex rounded-lg shadow-sm">
                                            <input type="text" wire:model="couponCode" class="flex-1 min-w-0 block w-full px-4 py-3 rounded-l-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent sm:text-sm" placeholder="Enter code">
                                            <button wire:click="applyCoupon" class="inline-flex items-center px-6 py-3 border border-l-0 border-custom rounded-r-lg accent-bg hover:opacity-90 text-white text-sm font-medium transition-all duration-200">
                                                Apply
                                            </button>
                                        </div>
                                        @if(!empty($cart['coupon']))
                                            <div class="mt-2 flex items-center text-sm">
                                                <span class="success-bg text-white px-2 py-1 rounded-full text-xs">Coupon: {{ $cart['coupon'] }}</span>
                                                <button wire:click="removeCoupon" class="ml-2 error-bg hover:opacity-80 text-white px-2 py-1 rounded-full text-xs transition-all duration-200">Remove</button>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Order Summary -->
                                    <div class="glass-effect rounded-xl p-4 border border-custom">
                                        <h3 class="text-lg font-semibold text-white mb-4">Order Summary</h3>
                                        
                                        <div class="flex justify-between text-base font-medium mb-2">
                                            <p class="text-secondary">Subtotal</p>
                                            <p class="text-white">${{ number_format($cart['subtotal'] ?? 0, 2) }}</p>
                                        </div>
                                        @if(($cart['setup_fees'] ?? 0) > 0)
                                            <div class="flex justify-between text-sm text-secondary mb-2">
                                                <p>Setup Fees</p>
                                                <p class="text-white">${{ number_format($cart['setup_fees'], 2) }}</p>
                                            </div>
                                        @endif
                                        @if(($cart['discount'] ?? 0) > 0)
                                            <div class="flex justify-between text-sm success-bg text-white px-3 py-1 rounded-lg mb-2">
                                                <p>Discount</p>
                                                <p>-${{ number_format($cart['discount'], 2) }}</p>
                                            </div>
                                        @endif
                                        <div class="flex justify-between text-xl font-bold text-white mt-4 pt-4 border-t border-custom">
                                            <p>Total</p>
                                            <p class="accent-text">${{ number_format($cart['total'] ?? 0, 2) }}</p>
                                        </div>
                                        <p class="mt-2 text-sm text-tertiary">Shipping and taxes calculated at checkout.</p>
                                    </div>
                                    
                                    <div class="mt-6 space-y-3">
                                        <a href="{{ route('checkout') }}" class="w-full flex justify-center items-center px-6 py-4 accent-bg hover:opacity-90 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                            Proceed to Checkout
                                        </a>
                                        <button wire:click="toggleCart" type="button" class="w-full flex justify-center items-center px-6 py-3 bg-card/50 border border-custom text-secondary hover:text-white rounded-xl font-medium transition-all duration-200">
                                            Continue Shopping
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
