<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->enum('status', ['draft', 'unpaid', 'paid', 'cancelled', 'refunded', 'collections'])->default('unpaid');
            
            // Financial
            $table->decimal('subtotal', 15, 4)->default(0);
            $table->decimal('discount', 15, 4)->default(0);
            $table->decimal('tax', 15, 4)->default(0);
            $table->decimal('credit', 15, 4)->default(0);
            $table->decimal('total', 15, 4)->default(0);
            $table->decimal('amount_paid', 15, 4)->default(0);
            $table->decimal('balance', 15, 4)->default(0);
            
            // Dates
            $table->timestamp('invoice_date');
            $table->timestamp('due_date');
            $table->timestamp('paid_date')->nullable();
            $table->timestamp('cancelled_date')->nullable();
            
            // Late fees
            $table->boolean('late_fee_added')->default(false);
            $table->decimal('late_fee_amount', 15, 4)->default(0);
            
            $table->text('notes')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
