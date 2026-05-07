<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Product;
use App\Services\Cart\CartService;
use App\Services\Billing\InvoiceService;
use App\Services\Pterodactyl\PterodactylService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected CartService $cartService;
    protected InvoiceService $invoiceService;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->invoiceService = new InvoiceService();
    }

    /**
     * Create order from cart
     */
    public function createFromCart(int $userId, ?string $couponCode = null): Order
    {
        $cart = $this->cartService->getCart();
        
        if (empty($cart['items'])) {
            throw new \Exception('Cart is empty');
        }

        return DB::transaction(function () use ($userId, $cart, $couponCode) {
            // Create order
            $order = new Order([
                'user_id' => $userId,
                'status' => 'pending',
                'total' => $cart['total'] ?? 0,
                'discount' => $cart['discount'] ?? 0,
                'coupon_code' => $couponCode,
            ]);
            $order->save();

            // Create order items
            foreach ($cart['items'] as $item) {
                $orderItem = new OrderItem([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'setup_fee' => $item['setup_fee'],
                    'billing_cycle' => $item['billing_cycle'],
                    'config_options' => $item['config_options'] ?? [],
                ]);
                $orderItem->save();

                // Decrement product stock
                $product = Product::find($item['product_id']);
                if ($product && $product->stock_enabled) {
                    $product->decrementStock();
                }
            }

            // Record coupon usage
            if ($couponCode && !empty($cart['discount'])) {
                $coupon = \App\Models\Coupon::where('code', $couponCode)->first();
                if ($coupon) {
                    $coupon->recordUsage(
                        \App\Models\User::find($userId),
                        $cart['discount'],
                        $order
                    );
                }
            }

            // Clear cart
            $this->cartService->clearCart();

            return $order;
        });
    }

    /**
     * Activate order and create services
     */
    public function activateOrder(Order $order): bool
    {
        if ($order->status !== 'pending') {
            return false;
        }

        try {
            DB::transaction(function () use ($order) {
                // Activate order
                $order->activate();

                // Create services for each order item
                foreach ($order->items as $item) {
                    $this->createService($order, $item);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Order activation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Create service from order item
     */
    protected function createService(Order $order, OrderItem $item): Service
    {
        $product = Product::find($item->product_id);
        
        $service = new Service([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'product_id' => $item->product_id,
            'order_item_id' => $item->id,
            'name' => $item->product_name,
            'status' => 'active',
            'price' => $item->price,
            'billing_cycle' => $item->billing_cycle,
            'next_invoice_date' => $this->calculateNextInvoiceDate($item->billing_cycle),
            'panel_type' => 'pterodactyl',
            'activated_at' => now(),
        ]);
        $service->save();

        // If game server product, provision on Pterodactyl
        if ($product && $product->type === 'game_server') {
            try {
                $this->provisionGameServer($service, $item->config_options ?? []);
            } catch (\Exception $e) {
                Log::error('Game server provisioning failed', [
                    'service_id' => $service->id,
                    'error' => $e->getMessage(),
                ]);
                // Mark service as pending provisioning
                $service->update(['status' => 'pending']);
            }
        }

        return $service;
    }

    /**
     * Provision game server on Pterodactyl
     */
    protected function provisionGameServer(Service $service, array $configOptions): void
    {
        $pterodactyl = new PterodactylService();
        
        // Get egg from config options or product default
        $eggId = $configOptions['egg_id'] ?? null;
        $egg = \App\Models\PterodactylEgg::find($eggId);
        
        if (!$egg) {
            throw new \Exception('No valid game egg selected');
        }

        // Get memory and disk from config options or product defaults
        $memory = ($configOptions['memory'] ?? 1024); // MB
        $disk = ($configOptions['disk'] ?? 10240); // MB
        $cpu = ($configOptions['cpu'] ?? 100); // Percentage

        // Get available allocation
        $allocations = $pterodactyl->getAllocations($egg->node_id);
        $allocation = $allocations[0] ?? null;

        if (!$allocation) {
            throw new \Exception('No available server allocations');
        }

        $serverConfig = [
            'egg_id' => $egg->id,
            'name' => $service->name,
            'memory' => $memory,
            'disk' => $disk,
            'cpu' => $cpu,
            'allocation_id' => $allocation['attributes']['id'],
            'environment' => $egg->environment_variables ?? [],
        ];

        $pterodactyl->createServer($service, $serverConfig);
    }

    /**
     * Suspend order and all associated services
     */
    public function suspendOrder(Order $order, ?string $reason = null): bool
    {
        try {
            DB::transaction(function () use ($order, $reason) {
                $order->suspend();

                foreach ($order->services as $service) {
                    $service->suspend();
                    
                    // Suspend on Pterodactyl if game server
                    if ($service->panel_type === 'pterodactyl' && $service->panel_server_id) {
                        try {
                            $pterodactyl = new PterodactylService();
                            $pterodactyl->suspendServer($service);
                        } catch (\Exception $e) {
                            Log::error('Failed to suspend server', [
                                'service_id' => $service->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Order suspension failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Unsuspend order and services
     */
    public function unsuspendOrder(Order $order): bool
    {
        try {
            DB::transaction(function () use ($order) {
                foreach ($order->services as $service) {
                    $service->unsuspend();
                    
                    if ($service->panel_type === 'pterodactyl' && $service->panel_server_id) {
                        try {
                            $pterodactyl = new PterodactylService();
                            $pterodactyl->unsuspendServer($service);
                        } catch (\Exception $e) {
                            Log::error('Failed to unsuspend server', [
                                'service_id' => $service->id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }
                }

                $order->update(['status' => 'active', 'suspended_at' => null]);
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Order unsuspension failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Cancel order and terminate services
     */
    public function cancelOrder(Order $order, string $type = 'immediate', ?string $reason = null): bool
    {
        try {
            DB::transaction(function () use ($order, $type, $reason) {
                if ($type === 'immediate') {
                    foreach ($order->services as $service) {
                        $this->terminateService($service);
                    }
                    $order->update(['status' => 'cancelled']);
                } else {
                    // Schedule for end of term
                    $order->update([
                        'status' => 'cancelled',
                        'notes' => "Scheduled cancellation: {$reason}",
                    ]);
                }
            });

            return true;
        } catch (\Exception $e) {
            Log::error('Order cancellation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Terminate a service
     */
    protected function terminateService(Service $service): void
    {
        // Delete from Pterodactyl if game server
        if ($service->panel_type === 'pterodactyl' && $service->panel_server_id) {
            try {
                $pterodactyl = new PterodactylService();
                $pterodactyl->deleteServer($service);
            } catch (\Exception $e) {
                Log::error('Failed to delete server', [
                    'service_id' => $service->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $service->terminate();
    }

    /**
     * Calculate next invoice date based on billing cycle
     */
    protected function calculateNextInvoiceDate(string $billingCycle): ?\DateTime
    {
        return match($billingCycle) {
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            'semi_annually' => now()->addMonths(6),
            'annually' => now()->addYear(),
            'biennially' => now()->addYears(2),
            default => now()->addMonth(),
        };
    }

    /**
     * Get order statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'active_orders' => Order::where('status', 'active')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'month_revenue' => Order::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('total'),
        ];
    }
}
