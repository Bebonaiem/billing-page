<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Setting;
use App\Constants\BillingConstants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class InvoiceCalculatorService
{
    /**
     * Calculate invoice totals with proper error handling
     */
    public function calculateInvoiceTotals(Invoice $invoice): array
    {
        try {
            DB::beginTransaction();

            // Get all invoice items with proper locking
            $items = $invoice->items()->lockForUpdate()->get();
            
            if ($items->isEmpty()) {
                throw new Exception('Invoice has no items to calculate');
            }

            // Calculate subtotal
            $subtotal = $items->sum(function ($item) {
                return $this->calculateItemTotal($item);
            });

            // Apply discount if applicable
            $discount = $this->calculateDiscount($invoice, $subtotal);
            
            // Calculate tax
            $tax = $this->calculateTax($invoice, $subtotal - $discount);
            
            // Apply credit if applicable
            $credit = $this->calculateCredit($invoice, $subtotal - $discount + $tax);
            
            // Calculate final total
            $total = $subtotal - $discount + $tax - $credit;
            
            // Ensure total is not negative
            $total = max(0, $total);
            
            // Calculate balance
            $balance = max(0, $total - $invoice->amount_paid);

            $result = [
                'subtotal' => round($subtotal, BillingConstants::DECIMAL_SCALE),
                'discount' => round($discount, BillingConstants::DECIMAL_SCALE),
                'tax' => round($tax, BillingConstants::DECIMAL_SCALE),
                'credit' => round($credit, BillingConstants::DECIMAL_SCALE),
                'total' => round($total, BillingConstants::DECIMAL_SCALE),
                'balance' => round($balance, BillingConstants::DECIMAL_SCALE),
            ];

            // Update invoice with calculated values
            $invoice->update($result);

            DB::commit();
            
            Log::info("Invoice totals calculated successfully", [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total' => $result['total']
            ]);

            return $result;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to calculate invoice totals", [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Calculate individual item total with proper precision
     */
    private function calculateItemTotal(InvoiceItem $item): float
    {
        $quantity = max(1, $item->quantity ?? 1);
        $unitPrice = max(0, $item->unit_price ?? 0);
        
        return $quantity * $unitPrice;
    }

    /**
     * Calculate discount with proper validation
     */
    private function calculateDiscount(Invoice $invoice, float $subtotal): float
    {
        if (!$invoice->coupon_id || $subtotal <= 0) {
            return 0;
        }

        $coupon = Coupon::find($invoice->coupon_id);
        
        if (!$coupon || !$this->isCouponValid($coupon, $invoice)) {
            return 0;
        }

        $discount = match ($coupon->type) {
            BillingConstants::COUPON_TYPE_PERCENTAGE => min(
                $subtotal * ($coupon->value / 100),
                $subtotal
            ),
            BillingConstants::COUPON_TYPE_FIXED => min($coupon->value, $subtotal),
            default => 0
        };

        return round($discount, BillingConstants::DECIMAL_SCALE);
    }

    /**
     * Calculate tax based on settings
     */
    private function calculateTax(Invoice $invoice, float $taxableAmount): float
    {
        if ($taxableAmount <= 0) {
            return 0;
        }

        $taxRate = (float) Setting::get('tax_rate', 0);
        $taxEnabled = (bool) Setting::get('tax_enabled', false);
        
        if (!$taxEnabled || $taxRate <= 0) {
            return 0;
        }

        $tax = $taxableAmount * ($taxRate / 100);
        
        return round($tax, BillingConstants::DECIMAL_SCALE);
    }

    /**
     * Calculate credit to apply
     */
    private function calculateCredit(Invoice $invoice, float $amount): float
    {
        if ($amount <= 0) {
            return 0;
        }

        $user = $invoice->user;
        $availableCredit = $user->getCreditBalance();
        
        if ($availableCredit <= 0) {
            return 0;
        }

        // Apply credit but don't exceed the amount
        $creditToApply = min($availableCredit, $amount);
        
        return round($creditToApply, BillingConstants::DECIMAL_SCALE);
    }

    /**
     * Validate coupon for invoice
     */
    private function isCouponValid(Coupon $coupon, Invoice $invoice): bool
    {
        // Check if coupon is active
        if (!$coupon->active) {
            return false;
        }

        // Check expiry date
        if ($coupon->expiry_date && $coupon->expiry_date < now()) {
            return false;
        }

        // Check usage limits
        if ($coupon->max_uses && $coupon->uses >= $coupon->max_uses) {
            return false;
        }

        // Check user-specific limits
        if ($coupon->max_uses_per_user) {
            $userUses = $coupon->usages()
                ->where('user_id', $invoice->user_id)
                ->count();
            
            if ($userUses >= $coupon->max_uses_per_user) {
                return false;
            }
        }

        // Check minimum order amount
        if ($coupon->minimum_amount && $invoice->subtotal < $coupon->minimum_amount) {
            return false;
        }

        return true;
    }

    /**
     * Recalculate invoice with proper validation
     */
    public function recalculateInvoice(Invoice $invoice): bool
    {
        try {
            // Check if invoice can be recalculated
            if ($invoice->status === BillingConstants::INVOICE_STATUS_PAID) {
                Log::warning("Attempted to recalculate paid invoice", [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number
                ]);
                return false;
            }

            if ($invoice->status === BillingConstants::INVOICE_STATUS_CANCELLED) {
                Log::warning("Attempted to recalculate cancelled invoice", [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number
                ]);
                return false;
            }

            $this->calculateInvoiceTotals($invoice);
            
            return true;

        } catch (Exception $e) {
            Log::error("Failed to recalculate invoice", [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Validate invoice data before calculation
     */
    public function validateInvoiceData(Invoice $invoice): array
    {
        $errors = [];

        // Check if invoice has items
        if ($invoice->items()->count() === 0) {
            $errors[] = 'Invoice must have at least one item';
        }

        // Check if user exists
        if (!$invoice->user) {
            $errors[] = 'Invoice must be associated with a valid user';
        }

        // Validate invoice dates
        if ($invoice->due_date && $invoice->due_date < $invoice->invoice_date) {
            $errors[] = 'Due date cannot be before invoice date';
        }

        // Validate currency
        if ($invoice->user && $invoice->currency !== $invoice->user->currency) {
            $errors[] = 'Invoice currency must match user currency';
        }

        return $errors;
    }
}