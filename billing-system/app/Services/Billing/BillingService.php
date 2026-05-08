<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use App\Models\CreditTransaction;
use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BillingService
{
    /**
     * Calculate recurring billing for services
     */
    public function calculateRecurringBilling(Service $service): float
    {
        $monthlyRate = match($service->billing_cycle) {
            'monthly' => $service->price,
            'quarterly' => $service->price / 3,
            'semiannually' => $service->price / 6,
            'annually' => $service->price / 12,
            default => $service->price,
        };

        return round($monthlyRate, 2);
    }

    /**
     * Generate late fees for overdue invoices
     */
    public function generateLateFees(): int
    {
        $overdueInvoices = Invoice::where('status', 'unpaid')
            ->whereDate('due_date', '<', now()->subDays(1))
            ->whereDate('created_at', '!=', now()->toDateString())
            ->get();

        $processed = 0;

        foreach ($overdueInvoices as $invoice) {
            try {
                $lateFeePercent = (int) \App\Models\Setting::getValue('late_fee_percentage', '5');
                $lateFee = round($invoice->balance * ($lateFeePercent / 100), 2);

                if ($lateFee > 0) {
                    $invoice->total += $lateFee;
                    $invoice->balance += $lateFee;
                    $invoice->save();
                    $processed++;

                    Log::info("Late fee added to invoice {$invoice->invoice_number}: {$lateFee}");
                }
            } catch (\Exception $e) {
                Log::error("Failed to add late fee to invoice {$invoice->id}: " . $e->getMessage());
            }
        }

        return $processed;
    }

    /**
     * Apply coupon to an invoice or order
     */
    public function applyCoupon(Invoice $invoice, string $couponCode): array
    {
        try {
            $coupon = Coupon::where('code', $couponCode)
                ->where('active', true)
                ->where('expiry_date', '>=', now())
                ->first();

            if (!$coupon) {
                return ['success' => false, 'message' => 'Invalid or expired coupon code'];
            }

            if ($coupon->max_uses && $coupon->uses >= $coupon->max_uses) {
                return ['success' => false, 'message' => 'Coupon has reached maximum usage'];
            }

            $discountAmount = $this->calculateDiscount($invoice->subtotal, $coupon);

            $invoice->coupon_id = $coupon->id;
            $invoice->discount = $discountAmount;
            $invoice->total = $invoice->subtotal - $discountAmount;
            $invoice->balance = $invoice->total;
            $invoice->save();

            $coupon->increment('uses');

            return ['success' => true, 'discount' => $discountAmount, 'message' => 'Coupon applied successfully'];
        } catch (\Exception $e) {
            Log::error("Error applying coupon: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while applying the coupon'];
        }
    }

    /**
     * Calculate discount based on coupon type
     */
    private function calculateDiscount(float $amount, Coupon $coupon): float
    {
        if ($coupon->type === 'percentage') {
            return round($amount * ($coupon->value / 100), 2);
        }

        return min($coupon->value, $amount);
    }

    /**
     * Process user credit transactions
     */
    public function deductCredit(User $user, float $amount): bool
    {
        try {
            DB::beginTransaction();

            if ($user->credit < $amount) {
                return false;
            }

            $user->decrement('credit', $amount);

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'type' => 'deduction',
                'description' => 'Manual deduction',
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to deduct credit: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add credit to user account
     */
    public function addCredit(User $user, float $amount, string $reason = 'Manual addition'): bool
    {
        try {
            DB::beginTransaction();

            $user->increment('credit', $amount);

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => 'addition',
                'description' => $reason,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to add credit: " . $e->getMessage());
            return false;
        }
    }
}
