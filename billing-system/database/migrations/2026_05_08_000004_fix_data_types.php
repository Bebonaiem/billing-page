<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix users table data types
        Schema::table('users', function (Blueprint $table) {
            // Change credit_balance from decimal to integer for cents
            $table->integer('credit_balance')->default(0)->change();
            
            // Add proper length constraints
            $table->string('phone', 20)->change();
            $table->string('company', 255)->change();
            $table->string('currency', 3)->default('USD')->change();
            $table->string('language', 10)->default('en')->change();
            $table->string('timezone', 50)->default('UTC')->change();
        });

        // Fix invoices table data types
        Schema::table('invoices', function (Blueprint $table) {
            // Ensure proper decimal precision for financial data
            $table->decimal('subtotal', 15, 4)->default(0)->change();
            $table->decimal('discount', 15, 4)->default(0)->change();
            $table->decimal('tax', 15, 4)->default(0)->change();
            $table->decimal('credit', 15, 4)->default(0)->change();
            $table->decimal('total', 15, 4)->default(0)->change();
            $table->decimal('amount_paid', 15, 4)->default(0)->change();
            $table->decimal('balance', 15, 4)->default(0)->change();
            $table->decimal('late_fee_amount', 15, 4)->default(0)->change();
            
            // Add proper string length for invoice number
            $table->string('invoice_number', 50)->change();
        });

        // Fix products table data types
        Schema::table('products', function (Blueprint $table) {
            // Ensure proper decimal precision for financial data
            $table->decimal('price', 15, 4)->default(0)->change();
            $table->decimal('setup_fee', 15, 4)->default(0)->change();
            
            // Add proper string lengths
            $table->string('name', 255)->change();
            $table->string('slug', 255)->change();
            $table->string('image', 500)->nullable()->change();
        });

        // Fix services table data types
        Schema::table('services', function (Blueprint $table) {
            // Ensure proper decimal precision for financial data
            $table->decimal('price', 15, 4)->default(0)->change();
            
            // Add proper string lengths
            $table->string('billing_cycle', 20)->change();
            $table->string('status', 20)->change();
        });

        // Fix payments table data types
        Schema::table('payments', function (Blueprint $table) {
            // Ensure proper decimal precision for financial data
            $table->decimal('amount', 15, 4)->change();
            
            // Add proper string lengths
            $table->string('payment_method', 50)->change();
            $table->string('status', 20)->change();
            $table->string('currency', 3)->change();
            $table->string('transaction_id', 255)->nullable()->change();
        });

        // Fix tickets table data types
        Schema::table('tickets', function (Blueprint $table) {
            // Add proper string lengths
            $table->string('status', 20)->change();
            $table->string('priority', 20)->change();
            $table->string('subject', 255)->change();
        });

        // Fix coupons table data types
        Schema::table('coupons', function (Blueprint $table) {
            // Ensure proper decimal precision for discount values
            $table->decimal('value', 15, 4)->default(0)->change();
            
            // Add proper string lengths
            $table->string('code', 50)->unique()->change();
            $table->string('type', 20)->change();
        });

        // Fix credit_transactions table data types
        Schema::table('credit_transactions', function (Blueprint $table) {
            // Ensure proper decimal precision for financial data
            $table->decimal('amount', 15, 4)->change();
            $table->decimal('balance_after', 15, 4)->change();
            
            // Add proper string lengths
            $table->string('type', 20)->change();
        });

        // Fix user_credits table data types
        Schema::table('user_credits', function (Blueprint $table) {
            // Use integer for cents instead of decimal
            $table->integer('balance')->default(0)->change();
        });

        // Fix login_history table data types
        Schema::table('login_history', function (Blueprint $table) {
            // Add proper string lengths
            $table->string('ip_address', 45)->change();
            $table->string('user_agent', 500)->change();
            $table->string('status', 20)->change();
        });

        // Fix settings table data types
        Schema::table('settings', function (Blueprint $table) {
            // Add proper string lengths
            $table->string('key', 100)->unique()->change();
            $table->text('value')->nullable()->change();
            $table->string('type', 20)->default('string')->change();
        });
    }

    public function down(): void
    {
        // Revert data type changes
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('credit_balance', 10, 2)->default(0)->change();
            $table->string('phone')->change();
            $table->string('company')->change();
            $table->string('currency')->default('USD')->change();
            $table->string('language')->default('en')->change();
            $table->string('timezone')->default('UTC')->change();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
            $table->decimal('tax', 10, 2)->default(0)->change();
            $table->decimal('credit', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->default(0)->change();
            $table->decimal('amount_paid', 10, 2)->default(0)->change();
            $table->decimal('balance', 10, 2)->default(0)->change();
            $table->decimal('late_fee_amount', 10, 2)->default(0)->change();
            $table->string('invoice_number')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->change();
            $table->decimal('setup_fee', 10, 2)->default(0)->change();
            $table->string('name')->change();
            $table->string('slug')->change();
            $table->string('image')->nullable()->change();
        });

        Schema::table('services', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->default(0)->change();
            $table->string('billing_cycle')->change();
            $table->string('status')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
            $table->string('payment_method')->change();
            $table->string('status')->change();
            $table->string('currency')->change();
            $table->string('transaction_id')->nullable()->change();
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('status')->change();
            $table->string('priority')->change();
            $table->string('subject')->change();
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->decimal('value', 10, 2)->default(0)->change();
            $table->string('code')->unique()->change();
            $table->string('type')->change();
        });

        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
            $table->decimal('balance_after', 10, 2)->change();
            $table->string('type')->change();
        });

        Schema::table('user_credits', function (Blueprint $table) {
            $table->decimal('balance', 10, 2)->default(0)->change();
        });

        Schema::table('login_history', function (Blueprint $table) {
            $table->string('ip_address')->change();
            $table->string('user_agent')->change();
            $table->string('status')->change();
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('key')->unique()->change();
            $table->text('value')->nullable()->change();
            $table->string('type')->default('string')->change();
        });
    }
};