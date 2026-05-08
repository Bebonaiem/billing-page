<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\Billing\InvoiceCalculatorService;
use App\Constants\BillingConstants;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class PaymentProcessorService
{
    private InvoiceCalculatorService $invoiceCalculator;

    public function __construct(InvoiceCalculatorService $invoiceCalculator)
    {
        $this->invoiceCalculator = $invoiceCalculator;
    }

    /**
     * Process payment with comprehensive error handling
     */
    public function processPayment(Invoice $invoice, array $paymentData): array
    {
        try {
            DB::beginTransaction();

            // Validate invoice state
            $validationResult = $this->validatePaymentRequest($invoice, $paymentData);
            if (!$validationResult['valid']) {
                throw new Exception($validationResult['error']);
            }

            // Get payment gateway
            $gateway = $this->getPaymentGateway($paymentData['payment_method']);
            if (!$gateway) {
                throw new Exception('Payment gateway not available');
            }

            // Process payment through gateway
            $gatewayResult = $gateway->processPayment($invoice, $paymentData);
            
            if (!$gatewayResult['success']) {
                throw new Exception('Payment gateway error: ' . ($gatewayResult['error'] ?? 'Unknown error'));
            }

            // Create payment record
            $payment = $this->createPaymentRecord($invoice, $gatewayResult);

            // Update invoice status
            $this->updateInvoiceStatus($invoice, $payment);

            // Handle post-payment actions
            $this->handlePostPaymentActions($invoice, $payment);

            DB::commit();

            Log::info("Payment processed successfully", [
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method
            ]);

            return [
                'success' => true,
                'payment' => $payment,
                'message' => 'Payment processed successfully'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error("Payment processing failed", [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'payment_data' => array_except($paymentData, ['card_number', 'cvv'])
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Payment processing failed'
            ];
        }
    }

    /**
     * Validate payment request
     */
    private function validatePaymentRequest(Invoice $invoice, array $paymentData): array
    {
        // Check invoice status
        if ($invoice->status === BillingConstants::INVOICE_STATUS_PAID) {
            return ['valid' => false, 'error' => 'Invoice is already paid'];
        }

        if ($invoice->status === BillingConstants::INVOICE_STATUS_CANCELLED) {
            return ['valid' => false, 'error' => 'Cannot pay cancelled invoice'];
        }

        // Check payment amount
        $paymentAmount = (float) ($paymentData['amount'] ?? 0);
        if ($paymentAmount <= 0) {
            return ['valid' => false, 'error' => 'Payment amount must be greater than 0'];
        }

        if ($paymentAmount > $invoice->balance) {
            return ['valid' => false, 'error' => 'Payment amount exceeds invoice balance'];
        }

        // Check payment method
        $paymentMethod = $paymentData['payment_method'] ?? '';
        if (!in_array($paymentMethod, $this->getValidPaymentMethods())) {
            return ['valid' => false, 'error' => 'Invalid payment method'];
        }

        // Check for duplicate payments
        $existingPayment = Payment::where('invoice_id', $invoice->id)
            ->where('status', BillingConstants::PAYMENT_STATUS_PENDING)
            ->where('transaction_id', $paymentData['transaction_id'] ?? '')
            ->first();

        if ($existingPayment) {
            return ['valid' => false, 'error' => 'Payment already being processed'];
        }

        return ['valid' => true, 'error' => null];
    }

    /**
     * Get payment gateway instance
     */
    private function getPaymentGateway(string $paymentMethod): ?PaymentGatewayInterface
    {
        return match ($paymentMethod) {
            BillingConstants::PAYMENT_METHOD_STRIPE => new StripeGateway(),
            BillingConstants::PAYMENT_METHOD_PAYPAL => new PayPalGateway(),
            BillingConstants::PAYMENT_METHOD_BANK_TRANSFER => new BankTransferGateway(),
            BillingConstants::PAYMENT_METHOD_CREDIT => new CreditGateway(),
            default => null
        };
    }

    /**
     * Get valid payment methods
     */
    private function getValidPaymentMethods(): array
    {
        return [
            BillingConstants::PAYMENT_METHOD_STRIPE,
            BillingConstants::PAYMENT_METHOD_PAYPAL,
            BillingConstants::PAYMENT_METHOD_BANK_TRANSFER,
            BillingConstants::PAYMENT_METHOD_CREDIT,
            BillingConstants::PAYMENT_METHOD_BALANCE
        ];
    }

    /**
     * Create payment record
     */
    private function createPaymentRecord(Invoice $invoice, array $gatewayResult): Payment
    {
        return $invoice->payments()->create([
            'user_id' => $invoice->user_id,
            'amount' => $gatewayResult['amount'],
            'currency' => $invoice->currency,
            'payment_method' => $gatewayResult['payment_method'],
            'transaction_id' => $gatewayResult['transaction_id'] ?? null,
            'status' => BillingConstants::PAYMENT_STATUS_COMPLETED,
            'processed_at' => now(),
            'gateway_response' => $gatewayResult['gateway_response'] ?? null,
            'notes' => $gatewayResult['notes'] ?? null
        ]);
    }

    /**
     * Update invoice status after payment
     */
    private function updateInvoiceStatus(Invoice $invoice, Payment $payment): void
    {
        // Update payment totals
        $invoice->amount_paid += $payment->amount;
        $invoice->balance = max(0, $invoice->balance - $payment->amount);

        // Check if invoice is fully paid
        if ($invoice->balance <= 0.0001) { // Account for floating point precision
            $invoice->status = BillingConstants::INVOICE_STATUS_PAID;
            $invoice->paid_date = now();
            $invoice->paid_by = $payment->user_id;
        }

        $invoice->save();
    }

    /**
     * Handle post-payment actions
     */
    private function handlePostPaymentActions(Invoice $invoice, Payment $payment): void
    {
        // Activate associated order if invoice is fully paid
        if ($invoice->status === BillingConstants::INVOICE_STATUS_PAID && $invoice->order) {
            $order = $invoice->order;
            if ($order->status === BillingConstants::ORDER_STATUS_PENDING) {
                $order->update(['status' => BillingConstants::ORDER_STATUS_PROCESSING]);
                
                // Trigger order activation
                event(new \App\Events\OrderPaid($order, $payment));
            }
        }

        // Send payment confirmation email
        try {
            \App\Jobs\SendPaymentConfirmationEmail::dispatch($invoice, $payment);
        } catch (Exception $e) {
            Log::error("Failed to queue payment confirmation email", [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage()
            ]);
        }

        // Log payment for analytics
        Log::info("Payment recorded for analytics", [
            'invoice_id' => $invoice->id,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'processing_time' => now()->diffInSeconds($payment->created_at)
        ]);
    }

    /**
     * Process refund with proper validation
     */
    public function processRefund(Payment $payment, float $amount, string $reason = ''): array
    {
        try {
            DB::beginTransaction();

            // Validate refund request
            if ($payment->status !== BillingConstants::PAYMENT_STATUS_COMPLETED) {
                throw new Exception('Cannot refund payment that is not completed');
            }

            if ($amount <= 0 || $amount > $payment->amount) {
                throw new Exception('Invalid refund amount');
            }

            // Get payment gateway
            $gateway = $this->getPaymentGateway($payment->payment_method);
            if (!$gateway) {
                throw new Exception('Payment gateway not available for refund');
            }

            // Process refund through gateway
            $refundResult = $gateway->refund($payment, $amount);
            
            if (!$refundResult) {
                throw new Exception('Refund failed at payment gateway');
            }

            // Create refund record
            $refund = $this->createRefundRecord($payment, $amount, $reason);

            // Update invoice
            $this->updateInvoiceAfterRefund($payment->invoice, $amount);

            DB::commit();

            Log::info("Refund processed successfully", [
                'payment_id' => $payment->id,
                'refund_id' => $refund->id,
                'amount' => $amount,
                'reason' => $reason
            ]);

            return [
                'success' => true,
                'refund' => $refund,
                'message' => 'Refund processed successfully'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            
            Log::error("Refund processing failed", [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Refund processing failed'
            ];
        }
    }

    /**
     * Create refund record
     */
    private function createRefundRecord(Payment $payment, float $amount, string $reason): Payment
    {
        return $payment->invoice->payments()->create([
            'user_id' => $payment->user_id,
            'amount' => -$amount,
            'currency' => $payment->currency,
            'payment_method' => $payment->payment_method,
            'transaction_id' => $payment->transaction_id . '_refund',
            'status' => BillingConstants::PAYMENT_STATUS_COMPLETED,
            'processed_at' => now(),
            'parent_payment_id' => $payment->id,
            'notes' => "Refund: {$reason}"
        ]);
    }

    /**
     * Update invoice after refund
     */
    private function updateInvoiceAfterRefund(Invoice $invoice, float $refundAmount): void
    {
        $invoice->amount_paid -= $refundAmount;
        $invoice->balance += $refundAmount;
        
        // If invoice was paid, change status back to unpaid
        if ($invoice->status === BillingConstants::INVOICE_STATUS_PAID) {
            $invoice->status = BillingConstants::INVOICE_STATUS_UNPAID;
            $invoice->paid_date = null;
            $invoice->paid_by = null;
        }
        
        $invoice->save();
    }
}