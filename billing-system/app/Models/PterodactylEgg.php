<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PterodactylEgg extends Model
{
    protected $fillable = [
        'node_id',
        'name',
        'egg_id',
        'nest_id',
        'docker_image',
        'startup_command',
        'environment_variables',
        'is_active',
    ];

    protected $casts = [
        'startup_command' => 'array',
        'environment_variables' => 'array',
        'is_active' => 'boolean',
    ];

    public function node(): BelongsTo
    {
        return $this->belongsTo(PterodactylNode::class, 'node_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
