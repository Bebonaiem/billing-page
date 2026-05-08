<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use App\Services\Billing\InvoiceService;
use App\Services\Order\OrderService;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'coupon_id',
        'invoice_number',
        'status',
        'subtotal',
        'discount',
        'tax',
        'credit',
        'total',
        'amount_paid',
        'balance',
        'invoice_date',
        'due_date',
        'paid_date',
        'cancelled_date',
        'late_fee_added',
        'late_fee_amount',
        'notes',
        'paid_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:4',
        'discount' => 'decimal:4',
        'tax' => 'decimal:4',
        'credit' => 'decimal:4',
        'total' => 'decimal:4',
        'amount_paid' => 'decimal:4',
        'balance' => 'decimal:4',
        'late_fee_amount' => 'decimal:4',
        'late_fee_added' => 'boolean',
        'invoice_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_date' => 'datetime',
        'cancelled_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        return "{$prefix}-{$date}-{$random}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', 'unpaid');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'unpaid')
            ->whereDate('due_date', '<', now());
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isUnpaid(): bool
    {
        return $this->status === 'unpaid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'unpaid' && $this->due_date < now();
    }

    public function getBalance(): float
    {
        return max(0, $this->total - $this->amount_paid);
    }

    public function addPayment(float $amount, string $paymentMethod = 'credit', ?string $transactionId = null, array $paymentAttributes = []): Payment
    {
        $payment = $this->payments()->create([
            'user_id' => $this->user_id,
            'amount' => $amount,
            'currency' => $this->user->currency ?? 'USD',
            'payment_method' => $paymentMethod,
            'transaction_id' => $transactionId,
            'status' => 'completed',
            'processed_at' => now(),
        ] + $paymentAttributes);

        $this->amount_paid += $amount;
        $this->balance = $this->getBalance();

        if ($this->balance <= 0) {
            $this->status = 'paid';
            $this->paid_date = now();
            $this->paid_by = Auth::id();
        }

        $this->save();

        if ($this->status === 'paid' && $this->order && $this->order->isPending()) {
            app(OrderService::class)->activateOrder($this->order);
        }

        return $payment;
    }

    public function recalculateTotals(): void
    {
        $this->subtotal = $this->items->sum('total');
        $this->total = $this->subtotal - $this->discount + $this->tax - $this->credit;
        $this->balance = $this->getBalance();
        $this->save();
    }

    public function generatePdf(): string
    {
        return app(InvoiceService::class)->generatePdf($this);
    }
}
