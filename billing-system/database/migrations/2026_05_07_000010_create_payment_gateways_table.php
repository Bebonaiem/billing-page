<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('driver'); // stripe, paypal, bank_transfer, etc.
            $table->string('display_name');
            $table->text('description')->nullable();
            $table->json('settings')->nullable(); // Encrypted gateway credentials
            $table->boolean('is_active')->default(true);
            $table->boolean('sandbox_mode')->default(true);
            $table->boolean('supports_recurring')->default(false);
            $table->boolean('supports_refunds')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_gateways');
    }
};
