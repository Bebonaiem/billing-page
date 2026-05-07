<div class="max-w-4xl mx-auto py-8">
    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center">
            <div class="flex-1 text-center">
                <div class="w-10 h-10 mx-auto rounded-full {{ $step === 'review' || $step === 'payment' || $step === 'complete' ? 'bg-blue-600 text-white' : 'bg-gray-300' }} flex items-center justify-center">1</div>
                <p class="mt-2 text-sm font-medium">Review</p>
            </div>
            <div class="w-full h-1 mx-2 {{ $step === 'payment' || $step === 'complete' ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
            <div class="flex-1 text-center">
                <div class="w-10 h-10 mx-auto rounded-full {{ $step === 'payment' || $step === 'complete' ? 'bg-blue-600 text-white' : 'bg-gray-300' }} flex items-center justify-center">2</div>
                <p class="mt-2 text-sm font-medium">Payment</p>
            </div>
            <div class="w-full h-1 mx-2 {{ $step === 'complete' ? 'bg-blue-600' : 'bg-gray-300' }}"></div>
            <div class="flex-1 text-center">
                <div class="w-10 h-10 mx-auto rounded-full {{ $step === 'complete' ? 'bg-blue-600 text-white' : 'bg-gray-300' }} flex items-center justify-center">3</div>
                <p class="mt-2 text-sm font-medium">Complete</p>
            </div>
        </div>
    </div>

    @if($step === 'review')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Order Summary -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Order Summary</h2>
                
                @if(!empty($cart['items']))
                    <div class="space-y-4">
                        @foreach($cart['items'] as $item)
                            <div class="flex justify-between items-center py-4 border-b dark:border-gray-700">
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $item['product_name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $item['billing_cycle'] }}</p>
                                    @if(!empty($item['config_summary']))
                                        <div class="mt-2 space-y-1 text-xs text-gray-500">
                                            @foreach($item['config_summary'] as $config)
                                                <p>
                                                    {{ $config['option'] }}: {{ $config['value'] }}
                                                    @if($config['price'] != 0)
                                                        ({{ $config['price_type'] === 'percentage' ? '+' . number_format($config['price'], 2) . '%' : '+$' . number_format($config['price'], 2) }})
                                                    @endif
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-800 dark:text-white">${{ number_format($item['subtotal'], 2) }}</p>
                                    @if($item['setup_fee'] > 0)
                                        <p class="text-sm text-gray-500">+${{ number_format($item['setup_fee'], 2) }} setup</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 space-y-2">
                        <div class="flex justify-between text-gray-600 dark:text-gray-400">
                            <span>Subtotal</span>
                            <span>${{ number_format($cart['subtotal'] ?? 0, 2) }}</span>
                        </div>
                        @if(($cart['setup_fees'] ?? 0) > 0)
                            <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>Setup Fees</span>
                                <span>${{ number_format($cart['setup_fees'], 2) }}</span>
                            </div>
                        @endif
                        @if(($cart['discount'] ?? 0) > 0)
                            <div class="flex justify-between text-green-600">
                                <span>Discount</span>
                                <span>-${{ number_format($cart['discount'], 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-xl font-bold text-gray-800 dark:text-white pt-4 border-t dark:border-gray-700">
                            <span>Total</span>
                            <span>${{ number_format($cart['total'] ?? 0, 2) }}</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Billing Information -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Billing Information</h2>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                            <input type="text" wire:model="billingInfo.first_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                            <input type="text" wire:model="billingInfo.last_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <input type="email" wire:model="billingInfo.email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company (Optional)</label>
                        <input type="text" wire:model="billingInfo.company" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                        <input type="text" wire:model="billingInfo.address_line1" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Address Line 1">
                        <input type="text" wire:model="billingInfo.address_line2" class="mt-2 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Address Line 2">
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4 mt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">City</label>
                            <input type="text" wire:model="billingInfo.city" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">State</label>
                            <input type="text" wire:model="billingInfo.state" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Postal Code</label>
                            <input type="text" wire:model="billingInfo.postal_code" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Country</label>
                        <select wire:model="billingInfo.country" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Select Country</option>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="UK">United Kingdom</option>
                            <option value="DE">Germany</option>
                            <option value="FR">France</option>
                        </select>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                        <input type="tel" wire:model="billingInfo.phone" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Payment Method</h2>
                    
                    <div class="space-y-3">
                        @foreach($gateways as $gateway)
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 {{ $selectedGateway === $gateway['id'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                <input type="radio" wire:model="selectedGateway" value="{{ $gateway['id'] }}" class="h-4 w-4 text-blue-600">
                                <div class="ml-3">
                                    <p class="font-medium text-gray-800 dark:text-white">{{ $gateway['display_name'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $gateway['description'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Terms -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <label class="flex items-start">
                        <input type="checkbox" wire:model="termsAccepted" class="h-4 w-4 text-blue-600 rounded border-gray-300">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">
                            I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms of Service</a> and 
                            <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button wire:click="placeOrder" class="w-full bg-blue-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Place Order
                </button>
            </div>
        </div>
    @endif

    @if($step === 'payment')
        <div class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
            <p class="mt-4 text-gray-600 dark:text-gray-400">Redirecting to payment gateway...</p>
        </div>
    @endif

    @if($step === 'complete')
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mt-4">Order Complete!</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Thank you for your order. You will receive a confirmation email shortly.</p>
            <a href="{{ route('client.dashboard') }}" class="inline-block mt-6 bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700">Go to Dashboard</a>
        </div>
    @endif
</div>
