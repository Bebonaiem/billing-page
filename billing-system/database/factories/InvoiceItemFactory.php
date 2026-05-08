<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 5);
        $unitPrice = $this->faker->randomFloat(4, 1, 100);

        return [
            'invoice_id' => Invoice::factory(),
            'service_id' => null,
            'order_id' => null,
            'type' => 'service',
            'description' => $this->faker->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => round($quantity * $unitPrice, 4),
            'tax' => 0,
            'period_start' => null,
            'period_end' => null,
            'sort_order' => 0,
        ];
    }
}

