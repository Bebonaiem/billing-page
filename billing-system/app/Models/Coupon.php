<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'max_discount',
        'max_uses',
        'uses_count',
        'max_uses_per_user',
        'applies_to_products',
        'product_ids',
        'category_ids',
        'applies_to_recurring',
        'applies_to_setup',
        'min_order_amount',
        'new_customers_only',
        'user_ids',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'max_discount' => 'decimal:4',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
        'max_uses_per_user' => 'integer',
        'product_ids' => 'array',
        'category_ids' => 'array',
        'user_ids' => 'array',
        'applies_to_products' => 'boolean',
        'applies_to_recurring' => 'boolean',
        'applies_to_setup' => 'boolean',
        'min_order_amount' => 'decimal:4',
        'new_customers_only' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && $this->starts_at > now()) return false;
        if ($this->expires_at && $this->expires_at < now()) return false;
        if ($this->max_uses && $this->uses_count >= $this->max_uses) return false;

        return true;
    }

    public function canBeUsedBy(User $user): bool
    {
        if (!$this->isValid()) return false;

        // Check per-user usage limit
        $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
        if ($userUsageCount >= $this->max_uses_per_user) return false;

        // Check specific users restriction
        if (!empty($this->user_ids) && !in_array($user->id, $this->user_ids)) return false;

        // Check new customers only
        if ($this->new_customers_only) {
            $hasPreviousOrders = Order::where('user_id', $user->id)->exists();
            if ($hasPreviousOrders) return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        $discount = 0;

        if ($this->type === 'percentage') {
            $discount = $amount * ($this->value / 100);
            if ($this->max_discount) {
                $discount = min($discount, $this->max_discount);
            }
        } elseif ($this->type === 'fixed') {
            $discount = min($this->value, $amount);
        }

        return round($discount, 4);
    }

    public function recordUsage(User $user, float $discountAmount, ?Order $order = null, ?Invoice $invoice = null): void
    {
        $this->usages()->create([
            'user_id' => $user->id,
            'order_id' => $order?->id,
            'invoice_id' => $invoice?->id,
            'discount_amount' => $discountAmount,
        ]);

        $this->increment('uses_count');
    }
}
