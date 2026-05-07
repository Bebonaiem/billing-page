<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initialize payment for an invoice
     *
     * @param Invoice $invoice
     * @param array $paymentData
     * @return array Contains 'redirect_url' or 'token' for the payment
     */
    public function initializePayment(Invoice $invoice, array $paymentData = []): array;

    /**
     * Process the payment callback/webhook
     *
     * @param array $requestData
     * @return Payment
     */
    public function processCallback(array $requestData): Payment;

    /**
     * Verify if a payment is valid
     *
     * @param string $transactionId
     * @return bool
     */
    public function verifyPayment(string $transactionId): bool;

    /**
     * Refund a payment
     *
     * @param Payment $payment
     * @param float $amount
     * @return bool
     */
    public function refund(Payment $payment, float $amount): bool;

    /**
     * Get the gateway name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check if gateway supports recurring payments
     *
     * @return bool
     */
    public function supportsRecurring(): bool;

    /**
     * Check if gateway supports refunds
     *
     * @return bool
     */
    public function supportsRefunds(): bool;
}
