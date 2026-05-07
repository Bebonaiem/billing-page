<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class StripeGateway implements PaymentGatewayInterface
{
    protected ?PaymentGateway $config;
    protected bool $sandbox;

    public function __construct()
    {
        $this->config = PaymentGateway::where('driver', 'stripe')->first();
        $this->sandbox = $this->config?->sandbox_mode ?? true;
    }

    public function initializePayment(Invoice $invoice, array $paymentData = []): array
    {
        $settings = $this->config?->settings ?? [];

        $secretKey = $settings['secret_key'] ?? env('STRIPE_SECRET_KEY');

        if (empty($secretKey)) {
            return [
                'success' => false,
                'error' => 'Stripe secret key is not configured.',
            ];
        }

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->asForm()
                ->post('https://api.stripe.com/v1/checkout/sessions', [
                    'mode' => 'payment',
                    'success_url' => route('client.invoices') . '?payment=success',
                    'cancel_url' => route('client.invoices') . '?payment=cancelled',
                    'customer_email' => $invoice->user->email,
                    'client_reference_id' => (string) $invoice->id,
                    'metadata[invoice_id]' => $invoice->id,
                    'metadata[invoice_number]' => $invoice->invoice_number,
                    'line_items[0][quantity]' => 1,
                    'line_items[0][price_data][currency]' => strtolower($invoice->user->currency ?? 'usd'),
                    'line_items[0][price_data][unit_amount]' => (int) round($invoice->balance * 100),
                    'line_items[0][price_data][product_data][name]' => "Invoice {$invoice->invoice_number}",
                    'line_items[0][price_data][product_data][description]' => 'Billing system invoice payment',
                ]);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => $response->json('error.message') ?? 'Failed to create Stripe checkout session.',
                ];
            }

            $session = $response->json();

            return [
                'success' => true,
                'publishable_key' => $settings['publishable_key'] ?? env('STRIPE_PUBLISHABLE_KEY'),
                'redirect_url' => $session['url'] ?? null,
                'session_id' => $session['id'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function processCallback(array $requestData): Payment
    {
        $payload = $requestData;

        if (isset($payload['data']['object'])) {
            $object = $payload['data']['object'];
            $eventType = $payload['type'] ?? null;

            $invoiceId = $object['metadata']['invoice_id'] ?? null;
            $invoice = Invoice::find($invoiceId);

            if (!$invoice) {
                throw new \Exception('Invoice not found');
            }

            if ($eventType === 'checkout.session.completed' && (($object['payment_status'] ?? null) === 'paid' || ($object['status'] ?? null) === 'complete')) {
                return $invoice->addPayment(
                    (float) (($object['amount_total'] ?? 0) / 100),
                    'stripe',
                    $object['payment_intent'] ?? $object['id'],
                    [
                        'gateway_id' => $this->config?->id,
                        'currency' => strtoupper($object['currency'] ?? $invoice->user->currency ?? 'usd'),
                        'gateway_response' => $payload,
                    ]
                );
            }

            if ($eventType === 'payment_intent.succeeded') {
                return $invoice->addPayment(
                    (float) (($object['amount_received'] ?? 0) / 100),
                    'stripe',
                    $object['id'],
                    [
                        'gateway_id' => $this->config?->id,
                        'currency' => strtoupper($object['currency'] ?? $invoice->user->currency ?? 'usd'),
                        'gateway_response' => $payload,
                    ]
                );
            }
        }
        
        throw new \Exception('Invalid webhook payload');
    }

    public function verifyPayment(string $transactionId): bool
    {
        $settings = $this->config?->settings ?? [];
        $secretKey = $settings['secret_key'] ?? env('STRIPE_SECRET_KEY');

        if (empty($secretKey)) {
            return false;
        }

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->get("https://api.stripe.com/v1/payment_intents/{$transactionId}");

            return $response->successful() && $response->json('status') === 'succeeded';
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refund(Payment $payment, float $amount): bool
    {
        $settings = $this->config?->settings ?? [];
        $secretKey = $settings['secret_key'] ?? env('STRIPE_SECRET_KEY');

        if (empty($secretKey)) {
            return false;
        }

        try {
            $response = Http::withBasicAuth($secretKey, '')
                ->asForm()
                ->post('https://api.stripe.com/v1/refunds', [
                    'payment_intent' => $payment->transaction_id,
                    'amount' => (int) round($amount * 100),
                ]);

            return $response->successful() && in_array($response->json('status'), ['succeeded', 'pending'], true);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getName(): string
    {
        return 'Stripe';
    }

    public function supportsRecurring(): bool
    {
        return true;
    }

    public function supportsRefunds(): bool
    {
        return true;
    }

    public function handleWebhook(string $payload, string $sigHeader): bool
    {
        $settings = $this->config?->settings ?? [];
        $webhookSecret = $settings['webhook_secret'] ?? env('STRIPE_WEBHOOK_SECRET');

        if (empty($webhookSecret)) {
            return false;
        }
        
        try {
            $event = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);

            if (!$this->verifyStripeSignature($payload, $sigHeader, $webhookSecret)) {
                return false;
            }

            if (in_array($event['type'] ?? '', ['checkout.session.completed', 'payment_intent.succeeded'], true)) {
                $this->processCallback($event);
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function verifyStripeSignature(string $payload, string $sigHeader, string $secret): bool
    {
        if (empty($sigHeader)) {
            return false;
        }

        $pairs = [];
        foreach (explode(',', $sigHeader) as $segment) {
            [$key, $value] = array_pad(explode('=', trim($segment), 2), 2, null);
            if ($key && $value) {
                $pairs[$key] = $value;
            }
        }

        $timestamp = $pairs['t'] ?? null;
        $signature = $pairs['v1'] ?? null;

        if (!$timestamp || !$signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
