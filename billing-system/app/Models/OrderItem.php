<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'price',
        'setup_fee',
        'billing_cycle',
        'config_options',
        'notes',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'setup_fee' => 'decimal:4',
        'config_options' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function getTotalPrice(): float
    {
        return $this->price + $this->setup_fee;
    }
}
