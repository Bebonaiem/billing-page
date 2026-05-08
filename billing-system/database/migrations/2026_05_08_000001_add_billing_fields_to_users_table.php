<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Billing and profile fields
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('company')->nullable()->after('phone');
            $table->text('address_line1')->nullable()->after('company');
            $table->text('address_line2')->nullable()->after('address_line1');
            $table->string('city')->nullable()->after('address_line2');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country', 2)->default('US')->after('postal_code');
            
            // Account settings
            $table->enum('status', ['active', 'suspended', 'banned'])->default('active')->after('country');
            $table->string('language', 5)->default('en')->after('status');
            $table->string('timezone')->default('UTC')->after('language');
            $table->string('currency', 3)->default('USD')->after('timezone');
            $table->boolean('marketing_emails')->default(true)->after('currency');
            $table->boolean('is_admin')->default(false)->after('marketing_emails');
            $table->timestamp('last_login_at')->nullable()->after('updated_at');
            
            // 2FA and security
            $table->string('two_factor_secret')->nullable()->after('last_login_at');
            $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
            
            // Billing fields
            $table->decimal('credit_balance', 10, 2)->default(0)->after('two_factor_enabled');
            $table->string('stripe_customer_id')->nullable()->after('credit_balance');
            $table->string('paypal_customer_id')->nullable()->after('stripe_customer_id');
            
            // Indexes for performance
            $table->index(['status', 'created_at']);
            $table->index('email');
            $table->index('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name', 
                'phone',
                'company',
                'address_line1',
                'address_line2',
                'city',
                'state',
                'postal_code',
                'country',
                'status',
                'language',
                'timezone',
                'currency',
                'marketing_emails',
                'is_admin',
                'last_login_at',
                'two_factor_secret',
                'two_factor_enabled',
                'credit_balance',
                'stripe_customer_id',
                'paypal_customer_id'
            ]);
            
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex('email');
            $table->dropIndex('is_admin');
        });
    }
};