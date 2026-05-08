<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite (used in tests) doesn't support MySQL's "MODIFY" syntax.
        // The production target is MySQL/MariaDB where this is valid.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE payment_gateways MODIFY settings LONGTEXT NULL');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE payment_gateways MODIFY settings JSON NULL');
    }
};
