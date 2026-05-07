<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_config_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // select, radio, number, text, checkbox
            $table->boolean('is_required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_config_option_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('config_option_id')->constrained('product_config_options')->onDelete('cascade');
            $table->string('label');
            $table->string('value');
            $table->decimal('price', 15, 4)->default(0); // Additional price
            $table->enum('price_type', ['fixed', 'percentage'])->default('fixed');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_config_option_values');
        Schema::dropIfExists('product_config_options');
    }
};
