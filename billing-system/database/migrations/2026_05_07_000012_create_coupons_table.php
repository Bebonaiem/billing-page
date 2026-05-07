<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            
            // Discount settings
            $table->enum('type', ['percentage', 'fixed', 'free_setup'])->default('percentage');
            $table->decimal('value', 15, 4)->default(0); // Percentage or fixed amount
            $table->decimal('max_discount', 15, 4)->nullable(); // For percentage coupons
            
            // Usage limits
            $table->integer('max_uses')->nullable(); // Total uses
            $table->integer('uses_count')->default(0);
            $table->integer('max_uses_per_user')->default(1);
            
            // Applicability
            $table->boolean('applies_to_products')->default(true);
            $table->json('product_ids')->nullable(); // Specific products
            $table->json('category_ids')->nullable(); // Specific categories
            $table->boolean('applies_to_recurring')->default(true);
            $table->boolean('applies_to_setup')->default(false);
            
            // Restrictions
            $table->decimal('min_order_amount', 15, 4)->nullable();
            $table->boolean('new_customers_only')->default(false);
            $table->json('user_ids')->nullable(); // Specific users
            
            // Validity
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('discount_amount', 15, 4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
    }
};
