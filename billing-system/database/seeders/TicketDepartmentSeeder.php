<?php

namespace Database\Seeders;

use App\Models\TicketDepartment;
use Illuminate\Database\Seeder;

class TicketDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'General Support',
                'email' => 'support@example.com',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Billing',
                'email' => 'billing@example.com',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sales',
                'email' => 'sales@example.com',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Technical',
                'email' => 'technical@example.com',
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($departments as $department) {
            TicketDepartment::create($department);
        }
    }
}
