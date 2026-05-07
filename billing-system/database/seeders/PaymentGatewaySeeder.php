<?php

namespace Database\Seeders;

use App\Models\PaymentGateway;
use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    public function run(): void
    {
        $gateways = [
            [
                'name' => 'stripe',
                'driver' => 'stripe',
                'display_name' => 'Credit Card (Stripe)',
                'description' => 'Accept credit card payments securely through Stripe',
                'settings' => [
                    'publishable_key' => '',
                    'secret_key' => '',
                    'webhook_secret' => '',
                ],
                'is_active' => false,
                'sandbox_mode' => true,
                'supports_recurring' => true,
                'supports_refunds' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'paypal',
                'driver' => 'paypal',
                'display_name' => 'PayPal',
                'description' => 'Accept payments through PayPal',
                'settings' => [
                    'client_id' => '',
                    'client_secret' => '',
                    'webhook_id' => '',
                ],
                'is_active' => false,
                'sandbox_mode' => true,
                'supports_recurring' => true,
                'supports_refunds' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'bank_transfer',
                'driver' => 'bank_transfer',
                'display_name' => 'Bank Transfer',
                'description' => 'Accept manual bank transfers',
                'settings' => [
                    'account_name' => '',
                    'account_number' => '',
                    'bank_name' => '',
                    'routing_number' => '',
                    'instructions' => '',
                ],
                'is_active' => true,
                'sandbox_mode' => false,
                'supports_recurring' => false,
                'supports_refunds' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'account_credit',
                'driver' => 'account_credit',
                'display_name' => 'Account Credit',
                'description' => 'Pay using your account credit balance',
                'settings' => [],
                'is_active' => true,
                'sandbox_mode' => false,
                'supports_recurring' => false,
                'supports_refunds' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($gateways as $gateway) {
            PaymentGateway::create($gateway);
        }
    }
}
