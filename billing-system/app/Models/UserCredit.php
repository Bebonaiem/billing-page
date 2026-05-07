<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserCredit extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'lifetime_credits',
    ];

    protected $casts = [
        'balance' => 'decimal:4',
        'lifetime_credits' => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function addCredit(float $amount, string $description = '', ?Invoice $invoice = null, ?Payment $payment = null): CreditTransaction
    {
        $transaction = $this->transactions()->create([
            'user_id' => $this->user_id,
            'type' => 'add',
            'amount' => $amount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance + $amount,
            'description' => $description,
            'related_invoice_id' => $invoice?->id,
            'related_payment_id' => $payment?->id,
        ]);

        $this->balance += $amount;
        $this->lifetime_credits += $amount;
        $this->save();

        return $transaction;
    }

    public function deductCredit(float $amount, string $description = '', ?Invoice $invoice = null): ?CreditTransaction
    {
        if ($this->balance < $amount) {
            return null;
        }

        $transaction = $this->transactions()->create([
            'user_id' => $this->user_id,
            'type' => 'deduct',
            'amount' => -$amount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance - $amount,
            'description' => $description,
            'related_invoice_id' => $invoice?->id,
        ]);

        $this->balance -= $amount;
        $this->save();

        return $transaction;
    }

    public function canAfford(float $amount): bool
    {
        return $this->balance >= $amount;
    }
}
