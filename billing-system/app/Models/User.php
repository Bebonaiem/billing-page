<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Order;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\UserNote;
use App\Models\CreditTransaction;
use App\Models\UserCredit;
use App\Models\LoginHistory;
use App\Constants\BillingConstants;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'company',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country',
        'status',
        'language',
        'timezone',
        'currency',
        'marketing_emails',
        'last_login_at',
    ];

    /**
     * The attributes that should be guarded from mass assignment.
     *
     * @var list<string>
     */
    protected $guarded = [
        'is_admin',
        'two_factor_secret',
        'two_factor_enabled',
        'credit_balance',
        'stripe_customer_id',
        'paypal_customer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'api_token',
        'panel_credentials',
    ];

    /**
     * Get attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'marketing_emails' => 'boolean',
            'is_admin' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function creditTransactions(): HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    public function credit(): HasOne
    {
        return $this->hasOne(UserCredit::class);
    }

    public function loginHistory(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(UserNote::class);
    }

    
    public function getUnpaidInvoicesCount(): int
    {
        return $this->invoices()->where('status', BillingConstants::INVOICE_STATUS_UNPAID)->count();
    }

    public function getOverdueInvoicesCount(): int
    {
        return $this->invoices()
            ->where('status', BillingConstants::INVOICE_STATUS_UNPAID)
            ->whereDate('due_date', '<', now())
            ->count();
    }

    public function getOpenTicketsCount(): int
    {
        return $this->tickets()
            ->whereIn('status', [
                BillingConstants::TICKET_STATUS_OPEN,
                BillingConstants::TICKET_STATUS_ANSWERED,
                BillingConstants::TICKET_STATUS_CUSTOMER_REPLY
            ])
            ->count();
    }

    public function getActiveServicesCount(): int
    {
        return $this->services()->where('status', BillingConstants::SERVICE_STATUS_ACTIVE)->count();
    }

    public function getCreditBalance(): float
    {
        return $this->credit?->balance ?? 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', BillingConstants::USER_STATUS_ACTIVE);
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', BillingConstants::USER_STATUS_SUSPENDED);
    }

    public function scopeAdmins($query)
    {
        return $query->where('is_admin', true);
    }

    // Attributes
    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '') . ' ' . ($this->last_name ?? '')) ?: $this->name;
    }

    public function getInitialsAttribute(): string
    {
        $name = $this->full_name;
        $words = explode(' ', $name);
        $initials = '';
        
        foreach ($words as $word) {
            $initials .= strtoupper(substr($word, 0, 1));
        }
        
        return substr($initials, 0, 2);
    }

    // Methods
    public function isAdmin(): bool
    {
        return $this->is_admin ?? false;
    }

    public function isActive(): bool
    {
        return $this->status === BillingConstants::USER_STATUS_ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->status === BillingConstants::USER_STATUS_SUSPENDED;
    }

    public function canLogin(): bool
    {
        return $this->isActive() && !$this->isBanned();
    }

    public function isBanned(): bool
    {
        return $this->status === BillingConstants::USER_STATUS_BANNED;
    }

    public function isStaff(): bool
    {
        return $this->isAdmin() || 
               $this->status === BillingConstants::USER_STATUS_STAFF || 
               $this->status === BillingConstants::USER_STATUS_SUPPORT;
    }

    public function hasActiveServices(): bool
    {
        return $this->services()->where('status', BillingConstants::SERVICE_STATUS_ACTIVE)->exists();
    }

    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    public function addCredit(float $amount, string $description = ''): void
    {
        // Update credit balance through relationship
        if ($this->credit) {
            $this->credit->increment('balance', $amount);
        } else {
            $this->credit()->create(['balance' => $amount]);
        }
        
        // Record credit transaction
        $this->creditTransactions()->create([
            'amount' => $amount,
            'type' => 'credit',
            'description' => $description,
            'balance_after' => $this->getCreditBalance(),
        ]);
    }

    public function deductCredit(float $amount, string $description = ''): bool
    {
        if ($this->getCreditBalance() < $amount) {
            return false;
        }

        if ($this->credit) {
            $this->credit->decrement('balance', $amount);
        }
        
        // Record credit transaction
        $this->creditTransactions()->create([
            'amount' => -$amount,
            'type' => 'debit',
            'description' => $description,
            'balance_after' => $this->getCreditBalance(),
        ]);

        return true;
    }

    

    public function getFormattedAddress(): string
    {
        $parts = array_filter([
            $this->address_line1,
            $this->address_line2,
            trim("{$this->city}, {$this->state} {$this->postal_code}"),
            $this->country,
        ]);

        return implode("\n", $parts);
    }
}