<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'is_public',
        'target_roles',
        'show_in_client_area',
        'show_on_login',
        'dismissible',
        'starts_at',
        'ends_at',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'target_roles' => 'array',
        'is_public' => 'boolean',
        'show_in_client_area' => 'boolean',
        'show_on_login' => 'boolean',
        'dismissible' => 'boolean',
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            });
    }

    public function scopeForClientArea($query)
    {
        return $query->active()->where('show_in_client_area', true);
    }

    public function scopeForLogin($query)
    {
        return $query->active()->where('show_on_login', true);
    }

    public function isVisibleTo(User $user): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && $this->starts_at > now()) return false;
        if ($this->ends_at && $this->ends_at < now()) return false;

        if ($this->is_public) return true;

        // Check role restrictions
        if (!empty($this->target_roles)) {
            $userRoles = $user->roles->pluck('name')->toArray();
            return !empty(array_intersect($this->target_roles, $userRoles));
        }

        return false;
    }
}
