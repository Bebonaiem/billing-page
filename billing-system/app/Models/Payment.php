<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'gateway_id',
        'transaction_id',
        'payment_method',
        'amount',
        'currency',
        'status',
        'refunded_amount',
        'last_four',
        'card_type',
        'payment_email',
        'gateway_response',
        'notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:4',
        'refunded_amount' => 'decimal:4',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function gateway(): BelongsTo
    {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canRefund(): bool
    {
        return in_array($this->status, ['completed']) && $this->refunded_amount < $this->amount;
    }

    public function getRefundableAmount(): float
    {
        return max(0, $this->amount - $this->refunded_amount);
    }

    public function refund(float $amount): bool
    {
        if (!$this->canRefund() || $amount > $this->getRefundableAmount()) {
            return false;
        }

        $this->refunded_amount += $amount;

        if ($this->refunded_amount >= $this->amount) {
            $this->status = 'refunded';
        } else {
            $this->status = 'partially_refunded';
        }

        $this->save();

        return true;
    }
}
