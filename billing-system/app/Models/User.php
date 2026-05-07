<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
     * Get the attributes that should be cast.
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

    // Helper methods
    public function getFullName(): string
    {
        return trim("{$this->first_name} {$this->last_name}") ?: $this->name;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isStaff(): bool
    {
        return $this->hasAnyRole(['admin', 'staff', 'support']);
    }

    public function getCreditBalance(): float
    {
        return $this->credit?->balance ?? 0;
    }

    public function hasActiveServices(): bool
    {
        return $this->services()->where('status', 'active')->exists();
    }

    public function getActiveServicesCount(): int
    {
        return $this->services()->where('status', 'active')->count();
    }

    public function getUnpaidInvoicesCount(): int
    {
        return $this->invoices()->where('status', 'unpaid')->count();
    }

    public function getOverdueInvoicesCount(): int
    {
        return $this->invoices()->where('status', 'unpaid')->whereDate('due_date', '<', now())->count();
    }

    public function getOpenTicketsCount(): int
    {
        return $this->tickets()->whereIn('status', ['open', 'answered', 'customer_reply'])->count();
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
