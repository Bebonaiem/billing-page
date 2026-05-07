<?php

namespace App\Livewire\Cart;

use App\Services\Cart\CartService;
use Livewire\Component;

class Cart extends Component
{
    protected CartService $cartService;
    
    public array $cart = [];
    public string $couponCode = '';
    public bool $showCart = false;
    
    protected $listeners = ['cartUpdated' => 'refreshCart'];
    
    public function boot()
    {
        $this->cartService = new CartService();
    }
    
    public function mount()
    {
        $this->refreshCart();
    }
    
    public function refreshCart()
    {
        $this->cart = $this->cartService->getCart();
        $this->cartService->recalculateTotals();
    }
    
    public function removeItem(string $itemId)
    {
        $this->cartService->removeItem($itemId);
        $this->refreshCart();
        $this->dispatch('cartUpdated');
    }
    
    public function updateQuantity(string $itemId, int $quantity)
    {
        if ($quantity > 0) {
            $this->cartService->updateQuantity($itemId, $quantity);
            $this->refreshCart();
            $this->dispatch('cartUpdated');
        }
    }
    
    public function applyCoupon()
    {
        if (empty($this->couponCode)) {
            return;
        }
        
        $success = $this->cartService->applyCoupon($this->couponCode);
        
        if ($success) {
            $this->dispatch('notify', type: 'success', message: 'Coupon applied successfully!');
        } else {
            $this->dispatch('notify', type: 'error', message: 'Invalid or expired coupon code.');
        }
        
        $this->refreshCart();
    }
    
    public function removeCoupon()
    {
        $this->cartService->removeCoupon();
        $this->couponCode = '';
        $this->refreshCart();
        $this->dispatch('notify', type: 'success', message: 'Coupon removed.');
    }
    
    public function toggleCart()
    {
        $this->showCart = !$this->showCart;
    }
    
    public function getTotalsProperty(): array
    {
        return $this->cartService->getTotals();
    }
    
    public function render()
    {
        return view('livewire.cart.cart');
    }
}
