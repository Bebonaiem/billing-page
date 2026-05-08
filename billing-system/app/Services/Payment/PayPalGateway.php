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
        $this->apiUrl = $this->sandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

}

    public function initializePayment(Invoice $invoice, array $paymentData = []): array
    {
        $settings = $this->config?->settings ?? [];
        $clientId = $settings['client_id'] ?? env('PAYPAL_CLIENT_ID');
        $clientSecret = $settings['client_secret'] ?? env('PAYPAL_CLIENT_SECRET');

        if (empty($clientId) || empty($clientSecret)) {
            return [
                'success' => false,
                'error' => 'PayPal credentials are not configured.',
            ];
        }

        try {
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$this->apiUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$response->successful()) {
                throw new \Exception($response->json('error_description') ?? 'Failed to get PayPal access token');
            }

            $accessToken = $response->json('access_token');

            $orderData = [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'reference_id' => $invoice->invoice_number,
                        'description' => "Invoice #{$invoice->invoice_number}",
                        'amount' => [
                            'currency_code' => strtoupper($invoice->user->currency ?? 'USD'),
                            'value' => number_format($invoice->balance, 2, '.', ''),
                        ],
                        'custom_id' => $invoice->id,
                    ],
                ],
                'application_context' => [
                    'return_url' => route('client.invoices') . '?payment=success',
                    'cancel_url' => route('client.invoices') . '?payment=cancelled',
                ],
            ];

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post("{$this->apiUrl}/v2/checkout/orders", $orderData);

            $orderResponse = $response->json();

            if ($response->successful() && isset($orderResponse['id'])) {
                $approvalLink = collect($orderResponse['links'] ?? [])
                    ->firstWhere('rel', 'approve')['href'] ?? null;

                return [
                    'success' => true,
                    'order_id' => $orderResponse['id'],
                    'redirect_url' => $approvalLink,
                ];
            }

            throw new \Exception('Failed to create PayPal order');
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function handleWebhook(array $payload): bool
    {
        try {
            $eventType = $payload['event_type'] ?? null;

            if ($eventType === 'CHECKOUT.ORDER.COMPLETED') {
                $orderId = $payload['resource']['id'] ?? null;
                return !empty($orderId);
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyPayment(string $transactionId): array
    {
        $settings = $this->config?->settings ?? [];
        $clientId = $settings['client_id'] ?? env('PAYPAL_CLIENT_ID');
        $clientSecret = $settings['client_secret'] ?? env('PAYPAL_CLIENT_SECRET');

        if (empty($clientId) || empty($clientSecret)) {
            return ['success' => false, 'message' => 'PayPal not configured'];
        }

        try {
            $tokenResponse = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$this->apiUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$tokenResponse->successful()) {
                return ['success' => false, 'message' => 'Failed to authenticate with PayPal'];
            }

            $accessToken = $tokenResponse->json('access_token');
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->get("{$this->apiUrl}/v2/checkout/orders/{$transactionId}");

            if ($response->successful() && ($response->json('status') === 'COMPLETED')) {
                return ['success' => true, 'status' => 'COMPLETED'];
            }

            return ['success' => false, 'message' => 'Payment verification failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function refundPayment(string $transactionId, float $amount = 0): bool
    {
        $settings = $this->config?->settings ?? [];
        $clientId = $settings['client_id'] ?? env('PAYPAL_CLIENT_ID');
        $clientSecret = $settings['client_secret'] ?? env('PAYPAL_CLIENT_SECRET');

        if (empty($clientId) || empty($clientSecret)) {
            return false;
        }

        try {
            $tokenResponse = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$this->apiUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$tokenResponse->successful()) {
                return false;
            }

            $accessToken = $tokenResponse->json('access_token');

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post("{$this->apiUrl}/v2/payments/captures/{$transactionId}/refund", [
                    'amount' => [
                        'value' => number_format($amount, 2, '.', ''),
                        'currency_code' => 'USD',
                    ],
                ]);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

