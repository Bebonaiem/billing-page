<?php

namespace App\Services\Payment;

use App\Models\Invoice;
interface PaymentGatewayInterface
{
    /**
     * Initialize a payment for an invoice.
     * Returns an array with 'success' and gateway-specific data (redirect URL, session id, etc.).
     */
    public function initializePayment(Invoice $invoice, array $paymentData = []): array;

    /**
     * Handle a webhook or callback payload.
     * Returns true if processed successfully.
     */
    public function handleWebhook(array $payload): bool;

    /**
     * Verify a payment by transaction or order id. Returns an array with verification result.
     */
    public function verifyPayment(string $transactionId): array;

    /**
     * Refund a payment by transaction id. Returns true on success.
     */
    public function refundPayment(string $transactionId, float $amount = 0): bool;
}
