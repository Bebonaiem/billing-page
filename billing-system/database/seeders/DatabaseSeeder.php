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
        // Create default admin user
        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'status' => 'active',
        ]);

        // Billing system seeders
        $this->call([
            CurrencySeeder::class,
            PaymentGatewaySeeder::class,
            EmailTemplateSeeder::class,
            TicketDepartmentSeeder::class,
        ]);
    }
}
