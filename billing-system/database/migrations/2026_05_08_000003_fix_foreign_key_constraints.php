<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix services table foreign keys
        Schema::table('services', function (Blueprint $table) {
            // Drop existing foreign key if it exists
            $table->dropForeign(['product_id']);
            // Add proper foreign key with cascade
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('cascade');
        });

        // Fix invoice_items table foreign keys
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'invoice_id')) {
                $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('invoice_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            }
        });

        // Fix order_items table foreign keys
        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'order_id')) {
                $table->foreignId('order_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('order_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            }
        });

        // Fix ticket_attachments table foreign keys
        Schema::table('ticket_attachments', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_attachments', 'ticket_id')) {
                $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('ticket_attachments', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
        });

        // Fix ticket_replies table foreign keys
        Schema::table('ticket_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('ticket_replies', 'ticket_id')) {
                $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('ticket_replies', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
        });

        // Fix coupon_usages table foreign keys
        Schema::table('coupon_usages', function (Blueprint $table) {
            if (!Schema::hasColumn('coupon_usages', 'coupon_id')) {
                $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('coupon_usages', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('coupon_usages', 'invoice_id')) {
                $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            }
        });

        // Fix user_notes table foreign keys
        Schema::table('user_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('user_notes', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('user_notes', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            }
        });

        // Fix product_config_option_values table foreign keys
        Schema::table('product_config_option_values', function (Blueprint $table) {
            if (!Schema::hasColumn('product_config_option_values', 'option_id')) {
                $table->foreignId('option_id')->constrained('product_config_options')->onDelete('cascade');
            }
        });

        // Fix pterodactyl_eggs table foreign keys
        Schema::table('pterodactyl_eggs', function (Blueprint $table) {
            if (!Schema::hasColumn('pterodactyl_eggs', 'node_id')) {
                $table->foreignId('node_id')->nullable()->constrained('pterodactyl_nodes')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        // Revert foreign key changes
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')
                  ->references('id')
                  ->on('products')
                  ->onDelete('set null');
        });

        // Note: We don't drop the added columns in down() as they might contain data
        // In a real migration, you might want to handle this differently
    }
};