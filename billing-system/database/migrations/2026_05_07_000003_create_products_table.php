<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('image')->nullable();
            $table->json('gallery')->nullable();
            
            // Product type
            $table->enum('type', ['game_server', 'web_hosting', 'vps', 'dedicated', 'domain', 'custom'])->default('game_server');
            
            // Pricing
            $table->decimal('price', 15, 4)->default(0);
            $table->enum('billing_cycle', ['hourly', 'daily', 'weekly', 'monthly', 'quarterly', 'semi_annually', 'annually', 'biennially', 'one_time'])->default('monthly');
            $table->decimal('setup_fee', 15, 4)->default(0);
            $table->boolean('has_trial')->default(false);
            $table->integer('trial_days')->default(0);
            
            // Stock & Availability
            $table->boolean('stock_enabled')->default(false);
            $table->integer('stock_quantity')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->boolean('require_domain')->default(false);
            
            // Integration settings
            $table->json('integration_settings')->nullable(); // Pterodactyl eggs, nests, etc.
            $table->json('config_options')->nullable(); // Default configurable options
            
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
