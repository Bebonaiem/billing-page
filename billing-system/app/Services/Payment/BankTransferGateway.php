<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;

class BankTransferGateway implements PaymentGatewayInterface
{
    protected ?PaymentGateway $config;

    public function __construct()
    {
        $this->config = PaymentGateway::where('driver', 'bank_transfer')->first();
    }

    public function initializePayment(Invoice $invoice, array $paymentData = []): array
    {
        try {
            $settings = $this->config?->settings ?? [];
            
            // Create a pending payment record
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $invoice->balance,
                'gateway' => 'bank_transfer',
                'status' => 'pending',
                'transaction_id' => 'BANK_TRANSFER_' . time() . '_' . $invoice->id,
                'metadata' => [
                    'bank_account' => $settings['account_number'] ?? 'N/A',
                    'bank_name' => $settings['bank_name'] ?? 'N/A',
                    'account_holder' => $settings['account_holder'] ?? 'N/A',
                    'account_type' => $settings['account_type'] ?? 'N/A',
                    'routing_number' => $settings['routing_number'] ?? 'N/A',
                ],
            ]);

            return [
                'success' => true,
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'message' => 'Please transfer the funds to the provided bank account details.',
                'bank_details' => $payment->metadata,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'Failed to initialize bank transfer: ' . $e->getMessage(),
            ];
        }
    }

    public function handleWebhook(mixed $payload): bool
    {
        // Bank transfers don't typically have webhooks - manual verification required
        return false;
    }

    public function verifyPayment(string $transactionId): array
    {
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return ['success' => false, 'message' => 'Payment not found'];
        }

        return [
            'success' => true,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'created_at' => $payment->created_at,
        ];
    }

    public function refundPayment(string $transactionId, float $amount = 0): bool
    {
        $payment = Payment::where('transaction_id', $transactionId)->first();

        if (!$payment) {
            return false;
        }

        $refundAmount = $amount ?: $payment->amount;

        Payment::create([
            'invoice_id' => $payment->invoice_id,
            'amount' => -$refundAmount,
            'gateway' => 'bank_transfer',
            'status' => 'refunded',
            'transaction_id' => 'REFUND_' . $transactionId,
            'metadata' => ['original_transaction' => $transactionId],
        ]);

        return true;
    }
}
