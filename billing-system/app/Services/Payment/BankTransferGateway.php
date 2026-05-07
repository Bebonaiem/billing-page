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
        $settings = $this->config?->settings ?? [];

        return [
            'success' => true,
            'instructions' => $settings['instructions'] ?? 'Please transfer the invoice total to the bank account details configured in the gateway settings.',
            'reference' => $invoice->invoice_number,
            'redirect_url' => null,
        ];
    }

    public function processCallback(array $requestData): Payment
    {
        throw new \Exception('Bank transfer payments are processed manually.');
    }

    public function verifyPayment(string $transactionId): bool
    {
        return false;
    }

    public function refund(Payment $payment, float $amount): bool
    {
        return false;
    }

    public function getName(): string
    {
        return 'Bank Transfer';
    }

    public function supportsRecurring(): bool
    {
        return false;
    }

    public function supportsRefunds(): bool
    {
        return false;
    }
}