<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Billing system seeders
        $this->call([
            CurrencySeeder::class,
            PaymentGatewaySeeder::class,
            EmailTemplateSeeder::class,
            TicketDepartmentSeeder::class,
        ]);
    }
}
