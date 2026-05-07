<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'USD',
                'name' => 'US Dollar',
                'symbol' => '$',
                'format' => '{symbol}{value}',
                'decimal_places' => 2,
                'exchange_rate' => 1.0,
                'is_default' => true,
                'is_active' => true,
            ],
            [
                'code' => 'EUR',
                'name' => 'Euro',
                'symbol' => '€',
                'format' => '{symbol}{value}',
                'decimal_places' => 2,
                'exchange_rate' => 0.85,
                'is_default' => false,
                'is_active' => true,
            ],
            [
                'code' => 'GBP',
                'name' => 'British Pound',
                'symbol' => '£',
                'format' => '{symbol}{value}',
                'decimal_places' => 2,
                'exchange_rate' => 0.73,
                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
