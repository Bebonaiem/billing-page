<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'description' => $this->faker->optional()->sentence(),
            'type' => 'percentage',
            'value' => 10,
            'max_discount' => null,
            'max_uses' => null,
            'uses_count' => 0,
            'max_uses_per_user' => 1,
            'applies_to_products' => true,
            'product_ids' => null,
            'category_ids' => null,
            'applies_to_recurring' => true,
            'applies_to_setup' => false,
            'min_order_amount' => null,
            'new_customers_only' => false,
            'user_ids' => null,
            'starts_at' => null,
            'expires_at' => null,
            'is_active' => true,
        ];
    }
}

