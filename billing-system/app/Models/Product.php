<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'short_description',
        'image',
        'gallery',
        'type',
        'price',
        'billing_cycle',
        'setup_fee',
        'has_trial',
        'trial_days',
        'stock_enabled',
        'stock_quantity',
        'is_visible',
        'require_domain',
        'integration_settings',
        'config_options',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'setup_fee' => 'decimal:4',
        'gallery' => 'array',
        'integration_settings' => 'array',
        'config_options' => 'array',
        'has_trial' => 'boolean',
        'trial_days' => 'integer',
        'stock_enabled' => 'boolean',
        'stock_quantity' => 'integer',
        'is_visible' => 'boolean',
        'require_domain' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function configOptions(): HasMany
    {
        return $this->hasMany(ProductConfigOption::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeInStock($query)
    {
        return $query->where(function ($q) {
            $q->where('stock_enabled', false)
              ->orWhere(function ($q2) {
                  $q2->where('stock_enabled', true)
                     ->whereColumn('stock_quantity', '>', 0);
              });
        });
    }

    public function hasStock(): bool
    {
        return !$this->stock_enabled || $this->stock_quantity > 0;
    }

    public function decrementStock(int $quantity = 1): void
    {
        if ($this->stock_enabled) {
            $this->decrement('stock_quantity', $quantity);
        }
    }

    public function getFullPrice(array $configOptions = []): float
    {
        $price = $this->price;

        foreach ($this->configOptions as $option) {
            if (isset($configOptions[$option->id])) {
                $selectedValue = $option->values->firstWhere('id', $configOptions[$option->id]);
                if ($selectedValue) {
                    if ($selectedValue->price_type === 'percentage') {
                        $price += ($this->price * $selectedValue->price / 100);
                    } else {
                        $price += $selectedValue->price;
                    }
                }
            }
        }

        return $price;
    }
}
