<?php

namespace App\Services\Payment;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Http;

class PayPalGateway implements PaymentGatewayInterface
{
    protected PaymentGateway $config;
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
            // Get access token
            $response = Http::withBasicAuth($clientId, $clientSecret)
                ->asForm()
                ->post("{$this->apiUrl}/v1/oauth2/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if (!$response->successful()) {
                throw new \Exception($response->json('error_description') ?? 'Failed to get PayPal access token');
            }

            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'] ?? null;
            
            if (!$accessToken) {
                throw new \Exception('Failed to get PayPal access token');
            }
            
            // Create order
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
                    'return_url' => route('payment.paypal.success'),
                    'cancel_url' => route('payment.paypal.cancel'),
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
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function processCallback(array $requestData): Payment
    {
        // Handle PayPal capture/callback
        $orderId = $requestData['token'] ?? null;
        
        if (!$orderId) {
            throw new \Exception('No order ID provided');
        }
        
        // Capture the order
        $settings = $this->config?->settings ?? [];
        $clientId = $settings['client_id'] ?? env('PAYPAL_CLIENT_ID');
        $clientSecret = $settings['client_secret'] ?? env('PAYPAL_CLIENT_SECRET');
        
        // Get access token
        $tokenResponse = Http::withBasicAuth($clientId, $clientSecret)
            ->asForm()
            ->post("{$this->apiUrl}/v1/oauth2/token", [
                'grant_type' => 'client_credentials',
            ]);

        if (!$tokenResponse->successful()) {
            throw new \Exception($tokenResponse->json('error_description') ?? 'Failed to get PayPal access token');
        }

        $tokenData = $tokenResponse->json();
        $accessToken = $tokenData['access_token'] ?? null;

        if (!$accessToken) {
            throw new \Exception('Failed to get PayPal access token');
        }
        
        // Capture payment
        $captureResponse = Http::withToken($accessToken)
            ->acceptJson()
            ->post("{$this->apiUrl}/v2/checkout/orders/{$orderId}/capture");

        $captureData = $captureResponse->json();
        
        if ($captureResponse->successful() && ($captureData['status'] ?? null) === 'COMPLETED') {
            $purchaseUnit = $captureData['purchase_units'][0] ?? [];
            $capture = $purchaseUnit['payments']['captures'][0] ?? [];
            $invoiceId = $purchaseUnit['custom_id'] ?? null;
            
            $invoice = Invoice::find($invoiceId);
            
            if (!$invoice) {
                throw new \Exception('Invoice not found');
            }
            
            return $invoice->addPayment(
                (float) ($capture['amount']['value'] ?? 0),
                'paypal',
                $capture['id'] ?? $orderId,
                [
                    'gateway_id' => $this->config?->id,
                    'currency' => $capture['amount']['currency_code'] ?? 'USD',
                    'payment_email' => $captureData['payer']['email_address'] ?? null,
                    'gateway_response' => $captureData,
                ]
            );
        }
        
        throw new \Exception('Payment capture failed');
    }

    public function verifyPayment(string $transactionId): bool
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
            if (!$accessToken) {
                return false;
            }

            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->get("{$this->apiUrl}/v2/checkout/orders/{$transactionId}");

            return $response->successful() && ($response->json('status') === 'COMPLETED');
        } catch (\Exception $e) {
            return false;
        }
    }

    public function refund(Payment $payment, float $amount): bool
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
            if (!$accessToken) {
                return false;
            }

            $capturesResponse = Http::withToken($accessToken)
                ->acceptJson()
                ->get("{$this->apiUrl}/v2/payments/captures/{$payment->transaction_id}");

            if (!$capturesResponse->successful()) {
                return false;
            }

            $refundId = $capturesResponse->json('id');
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post("{$this->apiUrl}/v2/payments/captures/{$payment->transaction_id}/refund", [
                    'amount' => [
                        'value' => number_format($amount, 2, '.', ''),
                        'currency_code' => $payment->currency ?? 'USD',
                    ],
                ]);

            return $response->successful() && !empty($refundId);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getName(): string
    {
        return 'PayPal';
    }

    public function supportsRecurring(): bool
    {
        return true;
    }

    public function supportsRefunds(): bool
    {
        return true;
    }
}
