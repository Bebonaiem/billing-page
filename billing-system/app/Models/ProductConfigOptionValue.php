<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductConfigOptionValue extends Model
{
    protected $table = 'product_config_option_values';

    protected $fillable = [
        'config_option_id',
        'label',
        'value',
        'price',
        'price_type',
        'sort_order',
        'is_default',
    ];

    protected $casts = [
        'price' => 'decimal:4',
        'is_default' => 'boolean',
    ];

    public function configOption(): BelongsTo
    {
        return $this->belongsTo(ProductConfigOption::class, 'config_option_id');
    }
}
