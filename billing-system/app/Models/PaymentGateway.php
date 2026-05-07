<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class PaymentGateway extends Model
{
    protected $fillable = [
        'name',
        'driver',
        'display_name',
        'description',
        'settings',
        'is_active',
        'sandbox_mode',
        'supports_recurring',
        'supports_refunds',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sandbox_mode' => 'boolean',
        'supports_recurring' => 'boolean',
        'supports_refunds' => 'boolean',
    ];

    public function setSettingsAttribute($value)
    {
        $this->attributes['settings'] = Crypt::encryptString(json_encode($value));
    }

    public function getSettingsAttribute($value)
    {
        if (!$value) return [];
        return json_decode(Crypt::decryptString($value), true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function isSandbox(): bool
    {
        return $this->sandbox_mode;
    }
}
