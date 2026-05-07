<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'product_id',
        'order_item_id',
        'name',
        'status',
        'price',
        'billing_cycle',
        'next_invoice_date',
        'last_invoice_date',
        'auto_renew',
        'panel_type',
        'panel_url',
        'panel_server_id',
        'panel_credentials',
        'activated_at',
        'suspended_at',
        'terminated_at',
        'cancelled_at',
        'cancellation_requested',
        'cancellation_date',
        'cancellation_type',
        'cancellation_reason',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'auto_renew' => 'boolean',
        'cancellation_requested' => 'boolean',
        'panel_credentials' => 'encrypted:array',
        'metadata' => 'array',
        'next_invoice_date' => 'datetime',
        'last_invoice_date' => 'datetime',
        'activated_at' => 'datetime',
        'suspended_at' => 'datetime',
        'terminated_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'cancellation_date' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function activate(): void
    {
        $this->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);
    }

    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);
    }

    public function unsuspend(): void
    {
        $this->update([
            'status' => 'active',
            'suspended_at' => null,
        ]);
    }

    public function terminate(): void
    {
        $this->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'auto_renew' => false,
        ]);
    }

    public function cancel(string $type = 'immediate', ?string $reason = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_requested' => true,
            'cancellation_date' => now(),
            'cancellation_type' => $type,
            'cancellation_reason' => $reason,
            'auto_renew' => false,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeForRenewal($query)
    {
        return $query->where('status', 'active')
            ->where('auto_renew', true)
            ->whereNotNull('next_invoice_date')
            ->whereDate('next_invoice_date', '<=', now()->addDays(7));
    }
}
