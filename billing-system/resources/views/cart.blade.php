@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900/20 to-blue-900/20 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-white mb-4">Shopping Cart</h1>
            <p class="text-secondary">Review your items before proceeding to checkout</p>
        </div>

        <!-- Cart Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="glass-effect rounded-2xl p-6 border border-custom">
                    @livewire('cart')
                    
                    <!-- Empty Cart Message -->
                    <div id="empty-cart" class="text-center py-12 hidden">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-card/50 rounded-2xl mb-4">
                            <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">Your cart is empty</h3>
                        <p class="text-secondary mb-6">Looks like you haven't added any items to your cart yet.</p>
                        <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 accent-bg-hover text-white rounded-lg font-medium transition-all duration-200">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="lg:col-span-1">
                <div class="glass-effect rounded-2xl p-6 border border-custom sticky top-8">
                    <h3 class="text-lg font-semibold text-white mb-4">Cart Summary</h3>
                    
                    <!-- Cart Summary Details -->
                    <div id="cart-summary" class="space-y-3 mb-6">
                        <div class="flex justify-between items-center">
                            <span class="text-secondary">Subtotal</span>
                            <span id="cart-subtotal" class="text-white">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-secondary">Discount</span>
                            <span id="cart-discount" class="text-green-400">-$0.00</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-secondary">Tax</span>
                            <span id="cart-tax" class="text-white">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-custom">
                            <span class="text-lg font-semibold text-white">Total</span>
                            <span id="cart-total" class="text-lg font-semibold text-white">$0.00</span>
                        </div>
                    </div>

                    <!-- Coupon Code -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-secondary mb-2">Coupon Code</label>
                        <div class="flex gap-2">
                            <input type="text" id="cart-coupon-code" placeholder="Enter coupon code"
                                   class="flex-1 px-4 py-2 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                            <button type="button" onclick="applyCartCoupon()" 
                                    class="px-4 py-2 bg-card border border-custom rounded-lg text-white hover:bg-card/80 transition-colors duration-200">
                                Apply
                            </button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <button id="checkout-btn" onclick="proceedToCheckout()" 
                                class="w-full accent-bg-hover text-white py-3 px-6 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            Proceed to Checkout
                        </button>
                        <a href="{{ route('home') }}" 
                           class="block w-full text-center px-6 py-3 bg-card border border-custom rounded-lg text-white hover:bg-card/80 transition-colors duration-200">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applyCartCoupon() {
    const couponCode = document.getElementById('cart-coupon-code').value;
    if (!couponCode) return;
    
    // This would typically make an AJAX call to apply the coupon
    // For now, just show a success message
    alert('Coupon applied successfully!');
}

function proceedToCheckout() {
    window.location.href = '{{ route('checkout') }}';
}

// Update cart summary when cart changes
document.addEventListener('livewire:init', () => {
    Livewire.on('cartUpdated', (cart) => {
        updateCartSummary(cart);
        toggleEmptyCart(cart);
    });
});

function updateCartSummary(cart) {
    // Update cart summary based on cart data
    const subtotal = cart.items?.reduce((sum, item) => sum + (item.price * item.quantity), 0) || 0;
    const discount = cart.discount || 0;
    const tax = subtotal * 0.08; // 8% tax
    const total = subtotal - discount + tax;
    
    document.getElementById('cart-subtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('cart-discount').textContent = `-$${discount.toFixed(2)}`;
    document.getElementById('cart-tax').textContent = `$${tax.toFixed(2)}`;
    document.getElementById('cart-total').textContent = `$${total.toFixed(2)}`;
    
    // Enable/disable checkout button based on cart items
    const checkoutBtn = document.getElementById('checkout-btn');
    if (cart.items && cart.items.length > 0) {
        checkoutBtn.disabled = false;
        checkoutBtn.classList.remove('opacity-50', 'cursor-not-allowed');
    } else {
        checkoutBtn.disabled = true;
        checkoutBtn.classList.add('opacity-50', 'cursor-not-allowed');
    }
}

function toggleEmptyCart(cart) {
    const emptyCart = document.getElementById('empty-cart');
    const cartItems = document.querySelector('[wire\\:key]');
    
    if (cart.items && cart.items.length > 0) {
        emptyCart.classList.add('hidden');
        if (cartItems) cartItems.classList.remove('hidden');
    } else {
        emptyCart.classList.remove('hidden');
        if (cartItems) cartItems.classList.add('hidden');
    }
}
</script>
@endsection
