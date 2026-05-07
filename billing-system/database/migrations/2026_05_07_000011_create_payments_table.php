<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('gateway_id')->nullable()->constrained('payment_gateways')->onDelete('set null');
            
            $table->string('transaction_id')->nullable(); // External transaction ID
            $table->string('payment_method'); // credit_card, paypal, bank_transfer, etc.
            $table->decimal('amount', 15, 4);
            $table->string('currency', 3)->default('USD');
            
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'partially_refunded', 'cancelled'])->default('pending');
            $table->decimal('refunded_amount', 15, 4)->default(0);
            
            // Card/Payment details (masked)
            $table->string('last_four', 4)->nullable();
            $table->string('card_type')->nullable(); // visa, mastercard, etc.
            $table->string('payment_email')->nullable(); // For PayPal
            
            $table->text('gateway_response')->nullable(); // Raw gateway response
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
