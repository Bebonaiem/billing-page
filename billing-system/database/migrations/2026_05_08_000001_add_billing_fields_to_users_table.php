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
            // Billing and profile fields - only add if they don't exist
            if (!Schema::hasColumn('users', 'first_name')) {
                $table->string('first_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name')->nullable()->after('first_name');
            }
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'company')) {
                $table->string('company')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'address_line1')) {
                $table->text('address_line1')->nullable()->after('company');
            }
            if (!Schema::hasColumn('users', 'address_line2')) {
                $table->text('address_line2')->nullable()->after('address_line1');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('address_line2');
            }
            if (!Schema::hasColumn('users', 'state')) {
                $table->string('state')->nullable()->after('city');
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable()->after('state');
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country', 2)->default('US')->after('postal_code');
            }
            
            // Account settings - only add if they don't exist
            if (!Schema::hasColumn('users', 'status')) {
                $table->enum('status', ['active', 'suspended', 'banned'])->default('active')->after('country');
            }
            if (!Schema::hasColumn('users', 'language')) {
                $table->string('language', 5)->default('en')->after('status');
            }
            if (!Schema::hasColumn('users', 'timezone')) {
                $table->string('timezone')->default('UTC')->after('language');
            }
            if (!Schema::hasColumn('users', 'currency')) {
                $table->string('currency', 3)->default('USD')->after('timezone');
            }
            if (!Schema::hasColumn('users', 'marketing_emails')) {
                $table->boolean('marketing_emails')->default(true)->after('currency');
            }
            if (!Schema::hasColumn('users', 'is_admin')) {
                $table->boolean('is_admin')->default(false)->after('marketing_emails');
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('updated_at');
            }
            
            // 2FA and security - only add if they don't exist
            if (!Schema::hasColumn('users', 'two_factor_secret')) {
                $table->string('two_factor_secret')->nullable()->after('last_login_at');
            }
            if (!Schema::hasColumn('users', 'two_factor_enabled')) {
                $table->boolean('two_factor_enabled')->default(false)->after('two_factor_secret');
            }
            
            // Billing fields - only add if they don't exist
            if (!Schema::hasColumn('users', 'credit_balance')) {
                $table->decimal('credit_balance', 10, 2)->default(0)->after('two_factor_enabled');
            }
            if (!Schema::hasColumn('users', 'stripe_customer_id')) {
                $table->string('stripe_customer_id')->nullable()->after('credit_balance');
            }
            if (!Schema::hasColumn('users', 'paypal_customer_id')) {
                $table->string('paypal_customer_id')->nullable()->after('stripe_customer_id');
            }
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
        });
    }
};
