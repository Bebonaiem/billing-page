<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Extension extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'author',
        'type',
        'settings',
        'is_active',
        'is_core',
        'dependencies',
        'installed_at',
        'activated_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_core' => 'boolean',
        'dependencies' => 'array',
        'installed_at' => 'datetime',
        'activated_at' => 'datetime',
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

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function activate(): void
    {
        $this->update([
            'is_active' => true,
            'activated_at' => now(),
        ]);
    }

    public function deactivate(): void
    {
        $this->update([
            'is_active' => false,
            'activated_at' => null,
        ]);
    }

    public function checkDependencies(): array
    {
        $missing = [];
        
        if (!empty($this->dependencies)) {
            foreach ($this->dependencies as $dependency) {
                $exists = self::where('slug', $dependency)
                    ->where('is_active', true)
                    ->exists();
                
                if (!$exists) {
                    $missing[] = $dependency;
                }
            }
        }

        return $missing;
    }

    public function canActivate(): bool
    {
        return empty($this->checkDependencies());
    }
}
