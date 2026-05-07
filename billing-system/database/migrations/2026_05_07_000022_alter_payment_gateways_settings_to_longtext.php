<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE payment_gateways MODIFY settings LONGTEXT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE payment_gateways MODIFY settings JSON NULL');
    }
};
