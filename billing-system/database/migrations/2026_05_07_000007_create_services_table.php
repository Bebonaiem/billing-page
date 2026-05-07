<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->constrained()->onDelete('cascade');
            
            // Service identification
            $table->string('name')->nullable(); // Custom service name
            $table->string('status')->default('pending'); // pending, active, suspended, cancelled, terminated
            
            // Billing
            $table->decimal('price', 15, 4);
            $table->string('billing_cycle');
            $table->timestamp('next_invoice_date')->nullable();
            $table->timestamp('last_invoice_date')->nullable();
            $table->boolean('auto_renew')->default(true);
            
            // Pterodactyl integration
            $table->string('panel_type')->nullable()->default('pterodactyl');
            $table->string('panel_url')->nullable();
            $table->string('panel_server_id')->nullable(); // External server ID
            $table->json('panel_credentials')->nullable(); // Encrypted panel access info
            
            // Lifecycle dates
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            $table->timestamp('terminated_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            
            // Cancellation
            $table->boolean('cancellation_requested')->default(false);
            $table->timestamp('cancellation_date')->nullable();
            $table->enum('cancellation_type', ['immediate', 'end_of_term'])->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->json('metadata')->nullable(); // Additional service data
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
