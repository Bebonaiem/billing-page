<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class PayPalGateway implements PaymentGatewayInterface
{
    protected ?PaymentGateway $config;
    protected bool $sandbox;
    protected string $apiUrl;

    public function __construct()
    {
        $this->config = PaymentGateway::where('driver', 'paypal')->first();
        $this->sandbox = $this->config?->sandbox_mode ?? true;
        $this->apiUrl = $this->sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
    }

    public function initializePayment(Invoice $invoice, array $paymentData = []): array
    {
        $settings = $this->config?->settings ?? [];
        $clientId = $settings['client_id'] ?? env('PAYPAL_CLIENT_ID');
        $clientSecret = $settings['client_secret'] ?? env('PAYPAL_CLIENT_SECRET');

        if (empty($clientId) || empty($clientSecret)) {
            return ['success' => false, 'error' => 'PayPal credentials not configured'];
        }

        try {
            $tokenRes = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$this->apiUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

            if (!$tokenRes->successful()) {
                return ['success' => false, 'error' => 'Failed to authenticate with PayPal'];
            }

            $accessToken = $tokenRes->json('access_token');

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'reference_id' => $invoice->invoice_number,
                    'amount' => [
                        'currency_code' => strtoupper($invoice->user->currency ?? 'USD'),
                        'value' => number_format($invoice->balance, 2, '.', ''),
                    ],
                    'custom_id' => $invoice->id,
                ]],
                'application_context' => [
                    'return_url' => route('client.invoices') . '?payment=success',
                    'cancel_url' => route('client.invoices') . '?payment=cancelled',
                ],
            ];

            $res = Http::withToken($accessToken)->post("{$this->apiUrl}/v2/checkout/orders", $orderData);
            if ($res->successful()) {
                $order = $res->json();
                $link = collect($order['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;
                return ['success' => true, 'order_id' => $order['id'] ?? null, 'redirect_url' => $link];
            }

            return ['success' => false, 'error' => 'Failed to create PayPal order'];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            $type = $payload['event_type'] ?? $payload['type'] ?? null;
            if ($type === 'CHECKOUT.ORDER.COMPLETED' || $type === 'checkout.session.completed') {
                return true;
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyPayment(string $transactionId): array
    {
        try {
            $settings = $this->config?->settings ?? [];
            $clientId = $settings['client_id'] ?? env('PAYPAL_CLIENT_ID');
            $clientSecret = $settings['client_secret'] ?? env('PAYPAL_CLIENT_SECRET');

            if (empty($clientId) || empty($clientSecret)) {
                return ['success' => false, 'message' => 'PayPal not configured'];
            }

            $tokenRes = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$this->apiUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

            if (!$tokenRes->successful()) {
                return ['success' => false, 'message' => 'Auth failed'];
            }

            $accessToken = $tokenRes->json('access_token');
            $res = Http::withToken($accessToken)->get("{$this->apiUrl}/v2/checkout/orders/{$transactionId}");

            if ($res->successful() && $res->json('status') === 'COMPLETED') {
                return ['success' => true, 'status' => 'COMPLETED'];
            }

            return ['success' => false, 'message' => 'Not completed'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function refundPayment(string $transactionId, float $amount = 0): bool
    {
        try {
            $settings = $this->config?->settings ?? [];
            $clientId = $settings['client_id'] ?? env('PAYPAL_CLIENT_ID');
            $clientSecret = $settings['client_secret'] ?? env('PAYPAL_CLIENT_SECRET');

            if (empty($clientId) || empty($clientSecret)) {
                return false;
            }

            $tokenRes = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$this->apiUrl}/v1/oauth2/token", ['grant_type' => 'client_credentials']);

            if (!$tokenRes->successful()) {
                return false;
            }

            $accessToken = $tokenRes->json('access_token');
            $res = Http::withToken($accessToken)
                ->post("{$this->apiUrl}/v2/payments/captures/{$transactionId}/refund", ['amount' => ['value' => number_format($amount, 2, '.', ''), 'currency_code' => 'USD']]);

            return $res->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
