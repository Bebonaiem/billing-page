<div class="relative">
    <!-- Cart Toggle Button -->
    <button wire:click="toggleCart" class="relative p-2 text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
        </svg>
        @if($this->totals['item_count'] > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                {{ $this->totals['item_count'] }}
            </span>
        @endif
    </button>

    <!-- Cart Slide-out Panel -->
    @if($showCart)
        <div class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="toggleCart"></div>
                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="w-screen max-w-md transform transition ease-in-out duration-500 sm:duration-700 translate-x-0">
                        <div class="h-full flex flex-col bg-white dark:bg-gray-800 shadow-xl overflow-y-scroll">
                            <div class="flex-1 py-6 overflow-y-auto px-4 sm:px-6">
                                <div class="flex items-start justify-between">
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-white" id="slide-over-title">Shopping Cart</h2>
                                    <div class="ml-3 h-7 flex items-center">
                                        <button wire:click="toggleCart" class="-m-2 p-2 text-gray-400 hover:text-gray-500">
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
                                            <ul class="-my-6 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($cart['items'] as $itemId => $item)
                                                    <li class="py-6 flex">
                                                        <div class="flex-1 ml-4">
                                                            <div>
                                                                <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                                                                    <h3>{{ $item['product_name'] }}</h3>
                                                                    <p class="ml-4">${{ number_format($item['subtotal'], 2) }}</p>
                                                                </div>
                                                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $item['billing_cycle'] }}</p>
                                                            </div>
                                                            <div class="flex-1 flex items-end justify-between text-sm">
                                                                <div class="flex items-center">
                                                                    <button wire:click="updateQuantity('{{ $itemId }}', {{ $item['quantity'] - 1 }})" class="px-2 py-1 border rounded">-</button>
                                                                    <span class="mx-3 text-gray-700 dark:text-gray-300">{{ $item['quantity'] }}</span>
                                                                    <button wire:click="updateQuantity('{{ $itemId }}', {{ $item['quantity'] + 1 }})" class="px-2 py-1 border rounded">+</button>
                                                                </div>
                                                                <button wire:click="removeItem('{{ $itemId }}')" type="button" class="font-medium text-red-600 hover:text-red-500">Remove</button>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">Your cart is empty</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(!empty($cart['items']))
                                <div class="border-t border-gray-200 dark:border-gray-700 py-6 px-4 sm:px-6">
                                    <!-- Coupon Code -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Coupon Code</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <input type="text" wire:model="couponCode" class="flex-1 min-w-0 block w-full px-3 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter code">
                                            <button wire:click="applyCoupon" class="inline-flex items-center px-4 py-2 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-md bg-gray-50 dark:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-500">Apply</button>
                                        </div>
                                        @if(!empty($cart['coupon']))
                                            <div class="mt-2 flex items-center text-sm">
                                                <span class="text-green-600">Coupon: {{ $cart['coupon'] }}</span>
                                                <button wire:click="removeCoupon" class="ml-2 text-red-600 hover:text-red-500">Remove</button>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex justify-between text-base font-medium text-gray-900 dark:text-white">
                                        <p>Subtotal</p>
                                        <p>${{ number_format($cart['subtotal'] ?? 0, 2) }}</p>
                                    </div>
                                    @if(($cart['setup_fees'] ?? 0) > 0)
                                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mt-2">
                                            <p>Setup Fees</p>
                                            <p>${{ number_format($cart['setup_fees'], 2) }}</p>
                                        </div>
                                    @endif
                                    @if(($cart['discount'] ?? 0) > 0)
                                        <div class="flex justify-between text-sm text-green-600 mt-2">
                                            <p>Discount</p>
                                            <p>-${{ number_format($cart['discount'], 2) }}</p>
                                        </div>
                                    @endif
                                    <div class="flex justify-between text-xl font-bold text-gray-900 dark:text-white mt-4 pt-4 border-t">
                                        <p>Total</p>
                                        <p>${{ number_format($cart['total'] ?? 0, 2) }}</p>
                                    </div>
                                    <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">Shipping and taxes calculated at checkout.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('checkout') }}" class="flex justify-center items-center px-6 py-3 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-blue-600 hover:bg-blue-700">Checkout</a>
                                    </div>
                                    <div class="mt-6 flex justify-center text-sm text-center text-gray-500 dark:text-gray-400">
                                        <p>
                                            or <button wire:click="toggleCart" type="button" class="text-blue-600 font-medium hover:text-blue-500">Continue Shopping<span aria-hidden="true"> &rarr;</span></button>
                                        </p>
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
