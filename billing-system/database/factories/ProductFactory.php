<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . $this->faker->unique()->numberBetween(1000, 9999),
            'description' => $this->faker->optional()->paragraph(),
            'short_description' => $this->faker->optional()->sentence(),
            'image' => null,
            'gallery' => null,
            'type' => 'custom',
            'price' => $this->faker->randomFloat(4, 5, 200),
            'billing_cycle' => 'monthly',
            'setup_fee' => 0,
            'has_trial' => false,
            'trial_days' => 0,
            'stock_enabled' => false,
            'stock_quantity' => null,
            'is_visible' => true,
            'require_domain' => false,
            'integration_settings' => null,
            'config_options' => null,
            'sort_order' => 0,
        ];
    }
}

