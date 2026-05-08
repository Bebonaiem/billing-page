<?php

namespace App\Services\Service;

use App\Models\Service;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Product;
use App\Constants\BillingConstants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class ServiceActivationService
{
    /**
     * Activate service with proper race condition handling
     */
    public function activateService(Service $service, array $activationData = []): array
    {
        $lockKey = "service_activation_{$service->id}";
        
        try {
            // Use cache lock to prevent race conditions
            $lock = Cache::lock($lockKey, 30);
            
            if (!$lock->get()) {
                return [
                    'success' => false,
                    'error' => 'Service activation already in progress',
                    'message' => 'Please wait and try again'
                ];
            }

            DB::beginTransaction();

            // Validate service state
            $validationResult = $this->validateServiceActivation($service);
            if (!$validationResult['valid']) {
                throw new Exception($validationResult['error']);
            }

            // Check if payment is confirmed
            if (!$this->isPaymentConfirmed($service)) {
                throw new Exception('Payment not confirmed for service activation');
            }

            // Activate service
            $this->performServiceActivation($service, $activationData);

            // Update related records
            $this->updateRelatedRecords($service);

            // Handle post-activation actions
            $this->handlePostActivationActions($service);

            DB::commit();

            Log::info("Service activated successfully", [
                'service_id' => $service->id,
                'user_id' => $service->user_id,
                'product_id' => $service->product_id
            ]);

            return [
                'success' => true,
                'service' => $service->fresh(),
                'message' => 'Service activated successfully'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error("Service activation failed", [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Service activation failed'
            ];

        } finally {
            if (isset($lock)) {
                $lock->release();
            }
        }
    }

    /**
     * Validate service activation request
     */
    private function validateServiceActivation(Service $service): array
    {
        // Check service status
        if ($service->status === BillingConstants::SERVICE_STATUS_ACTIVE) {
            return ['valid' => false, 'error' => 'Service is already active'];
        }

        if ($service->status === BillingConstants::SERVICE_STATUS_TERMINATED) {
            return ['valid' => false, 'error' => 'Cannot activate terminated service'];
        }

        // Check if user exists and is active
        if (!$service->user || !$service->user->isActive()) {
            return ['valid' => false, 'error' => 'User account is not active'];
        }

        // Check if product exists and is available
        if (!$service->product || !$service->product->is_visible) {
            return ['valid' => false, 'error' => 'Product is not available'];
        }

        // Check for duplicate services
        $duplicateService = Service::where('user_id', $service->user_id)
            ->where('product_id', $service->product_id)
            ->where('status', BillingConstants::SERVICE_STATUS_ACTIVE)
            ->where('id', '!=', $service->id)
            ->first();

        if ($duplicateService) {
            return ['valid' => false, 'error' => 'User already has an active service of this type'];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Check if payment is confirmed for service
     */
    private function isPaymentConfirmed(Service $service): bool
    {
        // Check if associated invoice is paid
        if ($service->invoice) {
            return $service->invoice->status === BillingConstants::INVOICE_STATUS_PAID;
        }

        // Check if associated order is paid
        if ($service->order) {
            return $service->order->invoices()
                ->where('status', BillingConstants::INVOICE_STATUS_PAID)
                ->exists();
        }

        // For manual activation, allow admin override
        return true;
    }

    /**
     * Perform the actual service activation
     */
    private function performServiceActivation(Service $service, array $activationData): void
    {
        $now = now();
        
        // Calculate next billing date based on billing cycle
        $nextBillingDate = $this->calculateNextBillingDate($service->billing_cycle, $now);

        // Update service
        $service->update([
            'status' => BillingConstants::SERVICE_STATUS_ACTIVE,
            'activated_at' => $now,
            'next_billing_date' => $nextBillingDate,
            'activation_data' => $activationData,
            'last_billed_at' => $now,
        ]);

        // Handle product-specific activation
        $this->handleProductSpecificActivation($service);
    }

    /**
     * Calculate next billing date
     */
    private function calculateNextBillingDate(string $billingCycle, \Carbon\Carbon $startDate): \Carbon\Carbon
    {
        return match ($billingCycle) {
            BillingConstants::BILLING_CYCLE_HOURLY => $startDate->addHour(),
            BillingConstants::BILLING_CYCLE_DAILY => $startDate->addDay(),
            BillingConstants::BILLING_CYCLE_WEEKLY => $startDate->addWeek(),
            BillingConstants::BILLING_CYCLE_MONTHLY => $startDate->addMonth(),
            BillingConstants::BILLING_CYCLE_QUARTERLY => $startDate->addMonths(3),
            BillingConstants::BILLING_CYCLE_SEMI_ANNUALLY => $startDate->addMonths(6),
            BillingConstants::BILLING_CYCLE_ANNUALLY => $startDate->addYear(),
            BillingConstants::BILLING_CYCLE_BIENNIALLY => $startDate->addYears(2),
            BillingConstants::BILLING_CYCLE_ONE_TIME => null, // No next billing for one-time
            default => $startDate->addMonth()
        };
    }

    /**
     * Handle product-specific activation
     */
    private function handleProductSpecificActivation(Service $service): void
    {
        $product = $service->product;

        match ($product->type) {
            BillingConstants::PRODUCT_TYPE_GAME_SERVER => $this->activateGameServer($service),
            BillingConstants::PRODUCT_TYPE_WEB_HOSTING => $this->activateWebHosting($service),
            BillingConstants::PRODUCT_TYPE_VPS => $this->activateVPS($service),
            BillingConstants::PRODUCT_TYPE_DEDICATED => $this->activateDedicated($service),
            default => null
        };
    }

    /**
     * Activate game server service
     */
    private function activateGameServer(Service $service): void
    {
        try {
            $pterodactylService = app(\App\Services\Pterodactyl\PterodactylService::class);
            
            $serverData = [
                'name' => $service->name ?? "Server-{$service->id}",
                'user' => $service->user,
                'egg' => $service->product->integration_settings['egg_id'] ?? null,
                'nest' => $service->product->integration_settings['nest_id'] ?? null,
                'allocation' => $service->config_data['allocation'] ?? null,
            ];

            $result = $pterodactylService->createServer($serverData);
            
            if ($result['success']) {
                $service->update([
                    'external_id' => $result['server_id'],
                    'config_data' => array_merge($service->config_data ?? [], $result['config'])
                ]);
            } else {
                throw new Exception('Failed to create game server: ' . $result['error']);
            }

        } catch (Exception $e) {
            Log::error("Game server activation failed", [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Activate web hosting service
     */
    private function activateWebHosting(Service $service): void
    {
        // Implementation for web hosting activation
        // This would integrate with cPanel, Plesk, or other control panels
        Log::info("Web hosting service activated", [
            'service_id' => $service->id
        ]);
    }

    /**
     * Activate VPS service
     */
    private function activateVPS(Service $service): void
    {
        // Implementation for VPS activation
        // This would integrate with virtualization platforms
        Log::info("VPS service activated", [
            'service_id' => $service->id
        ]);
    }

    /**
     * Activate dedicated server service
     */
    private function activateDedicated(Service $service): void
    {
        // Implementation for dedicated server activation
        Log::info("Dedicated server service activated", [
            'service_id' => $service->id
        ]);
    }

    /**
     * Update related records after activation
     */
    private function updateRelatedRecords(Service $service): void
    {
        // Update order status if applicable
        if ($service->order) {
            $order = $service->order;
            if ($order->status === BillingConstants::ORDER_STATUS_PROCESSING) {
                $order->update(['status' => BillingConstants::ORDER_STATUS_COMPLETED]);
            }
        }

        // Clear cache for user services
        Cache::forget("user_services_{$service->user_id}");
    }

    /**
     * Handle post-activation actions
     */
    private function handlePostActivationActions(Service $service): void
    {
        // Send activation notification
        try {
            \App\Jobs\SendServiceActivationEmail::dispatch($service);
        } catch (Exception $e) {
            Log::error("Failed to queue service activation email", [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);
        }

        // Log activation for analytics
        Log::info("Service activation recorded", [
            'service_id' => $service->id,
            'user_id' => $service->user_id,
            'product_id' => $service->product_id,
            'billing_cycle' => $service->billing_cycle,
            'price' => $service->price
        ]);

        // Trigger activation event
        event(new \App\Events\ServiceActivated($service));
    }

    /**
     * Suspend service with proper validation
     */
    public function suspendService(Service $service, string $reason = ''): array
    {
        $lockKey = "service_suspension_{$service->id}";
        
        try {
            $lock = Cache::lock($lockKey, 30);
            
            if (!$lock->get()) {
                return [
                    'success' => false,
                    'error' => 'Service suspension already in progress'
                ];
            }

            DB::beginTransaction();

            if ($service->status !== BillingConstants::SERVICE_STATUS_ACTIVE) {
                throw new Exception('Cannot suspend non-active service');
            }

            $service->update([
                'status' => BillingConstants::SERVICE_STATUS_SUSPENDED,
                'suspended_at' => now(),
                'suspension_reason' => $reason
            ]);

            // Handle product-specific suspension
            $this->handleProductSpecificSuspension($service);

            DB::commit();

            Log::info("Service suspended", [
                'service_id' => $service->id,
                'reason' => $reason
            ]);

            return ['success' => true, 'message' => 'Service suspended successfully'];

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Service suspension failed", [
                'service_id' => $service->id,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];

        } finally {
            if (isset($lock)) {
                $lock->release();
            }
        }
    }

    /**
     * Handle product-specific suspension
     */
    private function handleProductSpecificSuspension(Service $service): void
    {
        $product = $service->product;

        if ($product->type === BillingConstants::PRODUCT_TYPE_GAME_SERVER && $service->external_id) {
            try {
                $pterodactylService = app(\App\Services\Pterodactyl\PterodactylService::class);
                $pterodactylService->suspendServer($service->external_id);
            } catch (Exception $e) {
                Log::error("Failed to suspend game server", [
                    'service_id' => $service->id,
                    'external_id' => $service->external_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}