<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Crypt;

class PterodactylNode extends Model
{
    protected $fillable = [
        'name',
        'panel_url',
        'api_key',
        'is_active',
        'max_servers',
        'current_servers',
        'location_ids',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_servers' => 'integer',
        'current_servers' => 'integer',
        'location_ids' => 'array',
    ];

    public function setApiKeyAttribute($value)
    {
        $this->attributes['api_key'] = Crypt::encryptString($value);
    }

    public function getApiKeyAttribute($value)
    {
        if (!$value) return null;
        return Crypt::decryptString($value);
    }

    public function eggs(): HasMany
    {
        return $this->hasMany(PterodactylEgg::class, 'node_id');
    }

    public function hasCapacity(): bool
    {
        if (!$this->max_servers) return true;
        return $this->current_servers < $this->max_servers;
    }

    public function incrementServerCount(): void
    {
        $this->increment('current_servers');
    }

    public function decrementServerCount(): void
    {
        if ($this->current_servers > 0) {
            $this->decrement('current_servers');
        }
    }
}
