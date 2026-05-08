@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900/20 to-blue-900/20 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-4">Checkout</h1>
            <p class="text-secondary">Complete your order and start using our services</p>
        </div>

        <!-- Checkout Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Checkout -->
            <div class="lg:col-span-2">
                <div class="glass-effect rounded-2xl p-6 border border-custom">
                    <!-- Cart Items -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-white mb-4">Order Summary</h2>
                        <div id="cart-items" class="space-y-4">
                            <!-- Cart items will be loaded here via Livewire -->
                            @livewire('cart')
                        </div>
                    </div>

                    <!-- Billing Information -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-white mb-4">Billing Information</h2>
                        <form id="checkout-form" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-2">First Name</label>
                                    <input type="text" name="first_name" required
                                           class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-secondary mb-2">Last Name</label>
                                    <input type="text" name="last_name" required
                                           class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-2">Email Address</label>
                                <input type="email" name="email" required
                                       class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-2">Phone Number</label>
                                <input type="tel" name="phone"
                                       class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-secondary mb-2">Company (Optional)</label>
                                <input type="text" name="company"
                                       class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            </div>
                        </form>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-white mb-4">Payment Method</h2>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 bg-card/30 rounded-lg border border-custom cursor-pointer hover:bg-card/50 transition-colors duration-200">
                                <input type="radio" name="payment_method" value="stripe" class="mr-3" checked>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="text-white font-medium">Credit Card</span>
                                        <span class="ml-2 text-xs text-secondary">(Powered by Stripe)</span>
                                    </div>
                                    <p class="text-sm text-secondary mt-1">Pay securely with Visa, Mastercard, or American Express</p>
                                </div>
                            </label>
                            
                            <label class="flex items-center p-4 bg-card/30 rounded-lg border border-custom cursor-pointer hover:bg-card/50 transition-colors duration-200">
                                <input type="radio" name="payment_method" value="paypal" class="mr-3">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <span class="text-white font-medium">PayPal</span>
                                        <span class="ml-2 text-xs text-secondary">(Recommended)</span>
                                    </div>
                                    <p class="text-sm text-secondary mt-1">Fast and secure payment with your PayPal account</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-6">
                        <label class="flex items-start">
                            <input type="checkbox" name="terms" required class="mt-1 mr-3">
                            <span class="text-sm text-secondary">
                                I agree to the <a href="#" class="text-blue-400 hover:text-blue-300 underline">Terms of Service</a> 
                                and <a href="#" class="text-blue-400 hover:text-blue-300 underline">Privacy Policy</a>
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" form="checkout-form" 
                            class="w-full accent-bg-hover text-white py-4 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Complete Order
                    </button>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="glass-effect rounded-2xl p-6 border border-custom sticky top-8">
                    <h3 class="text-lg font-semibold text-white mb-4">Order Summary</h3>
                    
                    <!-- Order Details -->
                    <div id="order-summary" class="space-y-3 mb-6">
                        <!-- Order summary will be loaded here via Livewire -->
                    </div>

                    <!-- Coupon Code -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-secondary mb-2">Coupon Code</label>
                        <div class="flex gap-2">
                            <input type="text" id="coupon-code" placeholder="Enter coupon code"
                                   class="flex-1 px-4 py-2 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <button type="button" onclick="applyCoupon()" 
                                    class="px-4 py-2 bg-card border border-custom rounded-lg text-white hover:bg-card/80 transition-colors duration-200">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="border-t border-custom pt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-secondary">Subtotal</span>
                            <span id="subtotal" class="text-white">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-secondary">Discount</span>
                            <span id="discount" class="text-green-400">-$0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-secondary">Tax</span>
                            <span id="tax" class="text-white">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-custom">
                            <span class="text-lg font-semibold text-white">Total</span>
                            <span id="total" class="text-lg font-semibold text-white">$0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applyCoupon() {
    const couponCode = document.getElementById('coupon-code').value;
    if (!couponCode) return;
    
    // This would typically make an AJAX call to apply the coupon
    // For now, just show a success message
    alert('Coupon applied successfully!');
}

// Update order summary when cart changes
document.addEventListener('livewire:init', () => {
    Livewire.on('cartUpdated', (cart) => {
        updateOrderSummary(cart);
    });
});

function updateOrderSummary(cart) {
    // Update order summary based on cart data
    const subtotal = cart.items?.reduce((sum, item) => sum + (item.price * item.quantity), 0) || 0;
    const discount = cart.discount || 0;
    const tax = subtotal * 0.08; // 8% tax
    const total = subtotal - discount + tax;
    
    document.getElementById('subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('discount').textContent = `-$${discount.toFixed(2)}`;
    document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('total').textContent = `$${total.toFixed(2)}`;
}
</script>
@endsection
