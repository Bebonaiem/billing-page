<?php

namespace App\Services\Cart;

use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected string $sessionKey = 'cart';

    public function getCart(): array
    {
        return Session::get($this->sessionKey, ['items' => [], 'coupon' => null]);
    }

    public function saveCart(array $cart): void
    {
        Session::put($this->sessionKey, $cart);
    }

    public function clearCart(): void
    {
        Session::forget($this->sessionKey);
    }

    public function addItem(Product $product, array $configOptions = [], int $quantity = 1): array
    {
        $cart = $this->getCart();
        
        $itemId = $this->generateItemId($product->id, $configOptions);
        
        $price = $product->getFullPrice($configOptions);
        
        $cart['items'][$itemId] = [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $price,
            'setup_fee' => $product->setup_fee,
            'billing_cycle' => $product->billing_cycle,
            'config_options' => $configOptions,
            'quantity' => $quantity,
            'subtotal' => $price * $quantity,
        ];
        
        $this->saveCart($cart);
        $this->recalculateTotals();
        
        return $cart;
    }

    public function removeItem(string $itemId): array
    {
        $cart = $this->getCart();
        
        if (isset($cart['items'][$itemId])) {
            unset($cart['items'][$itemId]);
        }
        
        $this->saveCart($cart);
        $this->recalculateTotals();
        
        return $cart;
    }

    public function updateQuantity(string $itemId, int $quantity): array
    {
        $cart = $this->getCart();
        
        if (isset($cart['items'][$itemId]) && $quantity > 0) {
            $cart['items'][$itemId]['quantity'] = $quantity;
            $cart['items'][$itemId]['subtotal'] = $cart['items'][$itemId]['price'] * $quantity;
        }
        
        $this->saveCart($cart);
        $this->recalculateTotals();
        
        return $cart;
    }

    public function applyCoupon(string $couponCode): bool
    {
        $coupon = Coupon::where('code', $couponCode)->first();
        
        if (!$coupon || !$coupon->isValid()) {
            return false;
        }
        
        $cart = $this->getCart();
        $cart['coupon'] = $couponCode;
        $this->saveCart($cart);
        $this->recalculateTotals();
        
        return true;
    }

    public function removeCoupon(): void
    {
        $cart = $this->getCart();
        $cart['coupon'] = null;
        $cart['discount'] = 0;
        $this->saveCart($cart);
        $this->recalculateTotals();
    }

    public function recalculateTotals(): array
    {
        $cart = $this->getCart();
        
        $subtotal = 0;
        $setupFees = 0;
        
        foreach ($cart['items'] as $item) {
            $subtotal += $item['subtotal'];
            $setupFees += $item['setup_fee'] * $item['quantity'];
        }
        
        $cart['subtotal'] = $subtotal;
        $cart['setup_fees'] = $setupFees;
        
        // Calculate discount
        $discount = 0;
        if (!empty($cart['coupon'])) {
            $coupon = Coupon::where('code', $cart['coupon'])->first();
            if ($coupon && $coupon->isValid()) {
                $discount = $this->calculateDiscount($coupon, $subtotal);
            }
        }
        $cart['discount'] = $discount;
        
        $cart['total'] = $subtotal + $setupFees - $discount;
        
        $this->saveCart($cart);
        
        return $cart;
    }

    public function getTotals(): array
    {
        $cart = $this->getCart();
        
        return [
            'subtotal' => $cart['subtotal'] ?? 0,
            'setup_fees' => $cart['setup_fees'] ?? 0,
            'discount' => $cart['discount'] ?? 0,
            'total' => $cart['total'] ?? 0,
            'item_count' => count($cart['items'] ?? []),
        ];
    }

    protected function generateItemId(int $productId, array $configOptions): string
    {
        return $productId . '_' . md5(serialize($configOptions));
    }

    protected function calculateDiscount(Coupon $coupon, float $amount): float
    {
        return $coupon->calculateDiscount($amount);
    }

    public function isEmpty(): bool
    {
        $cart = $this->getCart();
        return empty($cart['items']);
    }

    public function getItemCount(): int
    {
        $cart = $this->getCart();
        return count($cart['items'] ?? []);
    }
}
