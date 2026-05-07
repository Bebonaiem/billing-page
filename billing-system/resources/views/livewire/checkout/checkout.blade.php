<div class="max-w-4xl mx-auto py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center">
            <div class="flex-1 text-center">
                <div class="w-12 h-12 mx-auto rounded-full {{ $step === 'review' || $step === 'payment' || $step === 'complete' ? 'accent-bg text-white shadow-lg' : 'bg-card/50 border border-custom' }} flex items-center justify-center transition-all duration-300">
                    <span class="text-lg font-semibold">{{ $step === 'review' || $step === 'payment' || $step === 'complete' ? '1' : '1' }}</span>
                </div>
                <p class="mt-2 text-sm font-medium {{ $step === 'review' || $step === 'payment' || $step === 'complete' ? 'accent-text' : 'text-secondary' }}">Review</p>
            </div>
            <div class="w-full h-1 mx-2 {{ $step === 'payment' || $step === 'complete' ? 'accent-bg' : 'bg-card/30' }} rounded-full transition-all duration-300"></div>
            <div class="flex-1 text-center">
                <div class="w-12 h-12 mx-auto rounded-full {{ $step === 'payment' || $step === 'complete' ? 'accent-bg text-white shadow-lg' : 'bg-card/50 border border-custom' }} flex items-center justify-center transition-all duration-300">
                    <span class="text-lg font-semibold">{{ $step === 'payment' || $step === 'complete' ? '2' : '2' }}</span>
                </div>
                <p class="mt-2 text-sm font-medium {{ $step === 'payment' || $step === 'complete' ? 'accent-text' : 'text-secondary' }}">Payment</p>
            </div>
            <div class="w-full h-1 mx-2 {{ $step === 'complete' ? 'accent-bg' : 'bg-card/30' }} rounded-full transition-all duration-300"></div>
            <div class="flex-1 text-center">
                <div class="w-12 h-12 mx-auto rounded-full {{ $step === 'complete' ? 'accent-bg text-white shadow-lg' : 'bg-card/50 border border-custom' }} flex items-center justify-center transition-all duration-300">
                    <span class="text-lg font-semibold">{{ $step === 'complete' ? '3' : '3' }}</span>
                </div>
                <p class="mt-2 text-sm font-medium {{ $step === 'complete' ? 'accent-text' : 'text-secondary' }}">Complete</p>
            </div>
        </div>
    </div>

    @if($step === 'review')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Order Summary -->
            <div class="glass-effect rounded-2xl border border-custom shadow-glow p-6">
                <h2 class="text-xl font-bold accent-text mb-6">Order Summary</h2>
                
                @if(!empty($cart['items']))
                    <div class="space-y-4">
                        @foreach($cart['items'] as $item)
                            <div class="glass-effect rounded-xl p-4 border border-custom">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <p class="font-semibold text-white">{{ $item['product_name'] }}</p>
                                        <p class="text-sm text-secondary mt-1">{{ $item['billing_cycle'] }}</p>
                                        @if(!empty($item['config_summary']))
                                            <div class="mt-3 space-y-2">
                                                @foreach($item['config_summary'] as $config)
                                                    <div class="flex items-center text-xs text-tertiary bg-card/50 rounded-lg px-3 py-2">
                                                        <span class="font-medium">{{ $config['option'] }}:</span>
                                                        <span class="ml-1">{{ $config['value'] }}</span>
                                                        @if($config['price'] != 0)
                                                            <span class="ml-1 success-bg text-white px-2 py-0.5 rounded-full text-xs">
                                                                {{ $config['price_type'] === 'percentage' ? '+' . number_format($config['price'], 2) . '%' : '+$' . number_format($config['price'], 2) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right ml-4">
                                        <p class="text-lg font-bold accent-text">${{ number_format($item['subtotal'], 2) }}</p>
                                        @if($item['setup_fee'] > 0)
                                            <p class="text-sm text-secondary">+${{ number_format($item['setup_fee'], 2) }} setup</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 space-y-3 pt-6 border-t border-custom">
                        <div class="flex justify-between text-secondary">
                            <span>Subtotal</span>
                            <span>${{ number_format($cart['subtotal'] ?? 0, 2) }}</span>
                        </div>
                        @if(($cart['setup_fees'] ?? 0) > 0)
                            <div class="flex justify-between text-secondary">
                                <span>Setup Fees</span>
                                <span>${{ number_format($cart['setup_fees'], 2) }}</span>
                            </div>
                        @endif
                        @if(($cart['discount'] ?? 0) > 0)
                            <div class="flex justify-between success-bg text-white px-4 py-2 rounded-lg">
                                <span>Discount</span>
                                <span>-${{ number_format($cart['discount'], 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-2xl font-bold text-white pt-4 border-t border-custom">
                            <span>Total</span>
                            <span class="accent-text">${{ number_format($cart['total'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Billing Information -->
            <div class="space-y-6">
                <div class="glass-effect rounded-2xl border border-custom shadow-glow p-6">
                    <h2 class="text-xl font-bold accent-text mb-6">Billing Information</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">First Name</label>
                            <input type="text" wire:model="billingInfo.first_name" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Last Name</label>
                            <input type="text" wire:model="billingInfo.last_name" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-white mb-2">Email</label>
                        <input type="email" wire:model="billingInfo.email" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-white mb-2">Company (Optional)</label>
                        <input type="text" wire:model="billingInfo.company" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-white mb-2">Address</label>
                        <input type="text" wire:model="billingInfo.address_line1" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200" placeholder="Address Line 1">
                        <input type="text" wire:model="billingInfo.address_line2" class="mt-2 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200" placeholder="Address Line 2">
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">City</label>
                            <input type="text" wire:model="billingInfo.city" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">State</label>
                            <input type="text" wire:model="billingInfo.state" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white mb-2">Postal Code</label>
                            <input type="text" wire:model="billingInfo.postal_code" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-white mb-2">Country</label>
                        <select wire:model="billingInfo.country" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                            <option value="">Select Country</option>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="UK">United Kingdom</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                        </select>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-white mb-2">Phone</label>
                        <input type="tel" wire:model="billingInfo.phone" class="mt-1 block w-full rounded-lg border border-custom bg-card/50 text-white placeholder-gray-400 focus:ring-2 focus:ring-accent focus:border-transparent transition-all duration-200">
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="glass-effect rounded-2xl border border-custom shadow-glow p-6">
                    <h2 class="text-xl font-bold accent-text mb-6">Payment Method</h2>
                    
                    <div class="space-y-3">
                        @foreach($gateways as $gateway)
                            <label class="flex items-center p-4 border border-custom rounded-xl cursor-pointer hover:bg-card/50 transition-all duration-200 {{ $selectedGateway === $gateway['id'] ? 'accent-bg border-accent' : '' }}">
                                <input type="radio" wire:model="selectedGateway" value="{{ $gateway['id'] }}" class="h-4 w-4 accent-bg">
                                <div class="ml-3 flex-1">
                                    <p class="font-semibold text-white">{{ $gateway['display_name'] }}</p>
                                    <p class="text-sm text-secondary">{{ $gateway['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Terms -->
                <div class="glass-effect rounded-2xl border border-custom shadow-glow p-6">
                    <label class="flex items-start">
                        <input type="checkbox" wire:model="termsAccepted" class="h-4 w-4 accent-bg rounded border-custom">
                        <span class="ml-3 text-sm text-secondary">
                            I agree to the <a href="#" class="accent-text hover:opacity-80">Terms of Service</a> and 
                            <a href="#" class="accent-text hover:opacity-80">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button wire:click="placeOrder" class="w-full accent-bg hover:opacity-90 text-white py-4 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Place Order
                </button>
            </div>
        </div>
    @endif

    @if($step === 'payment')
        <div class="text-center py-12">
            <div class="glass-effect rounded-2xl border border-custom shadow-glow p-12 max-w-md mx-auto">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 accent-bg mx-auto mb-4"></div>
                <h3 class="text-xl font-bold accent-text mb-2">Processing Payment</h3>
                <p class="text-secondary">Redirecting to payment gateway...</p>
            </div>
        </div>
    @endif

    @if($step === 'complete')
        <div class="text-center py-12">
            <div class="glass-effect rounded-2xl border border-custom shadow-glow p-12 max-w-md mx-auto">
                <div class="w-20 h-20 success-bg rounded-full flex items-center justify-center mx-auto mb-6 animate-float">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold accent-text mb-4">Order Complete!</h2>
                <p class="text-secondary mb-6">Thank you for your order. You will receive a confirmation email shortly.</p>
                <a href="{{ route('client.dashboard') }}" class="inline-flex items-center accent-bg hover:opacity-90 text-white py-3 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Go to Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
