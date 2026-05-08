<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'order_item_id' => OrderItem::factory(),
            'name' => $this->faker->optional()->words(3, true),
            'status' => 'active',
            'price' => $this->faker->randomFloat(4, 5, 200),
            'billing_cycle' => 'monthly',
            'next_invoice_date' => now()->addMonth(),
            'last_invoice_date' => now()->subMonth(),
            'auto_renew' => true,
            'panel_type' => 'pterodactyl',
            'panel_url' => null,
            'panel_server_id' => null,
            'panel_credentials' => null,
            'activated_at' => now()->subDays(3),
            'suspended_at' => null,
            'terminated_at' => null,
            'cancelled_at' => null,
            'cancellation_requested' => false,
            'cancellation_date' => null,
            'cancellation_type' => null,
            'cancellation_reason' => null,
            'metadata' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Service $service) {
            // Ensure order + order item + product are coherent and owned by the same user/order.
            $user = $service->user()->exists() ? $service->user : User::factory()->create();
            $order = $service->order()->exists() ? $service->order : Order::factory()->for($user)->create();
            $product = $service->product()->exists() ? $service->product : Product::factory()->create();
            $orderItem = $service->orderItem()->exists()
                ? $service->orderItem
                : OrderItem::factory()->for($order)->for($product)->create([
                    'product_name' => $product->name,
                    'price' => $service->price,
                    'billing_cycle' => $service->billing_cycle,
                ]);

            // Sync FK values for consistency.
            $service->forceFill([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'product_id' => $product->id,
                'order_item_id' => $orderItem->id,
            ])->save();
        });
    }
}

