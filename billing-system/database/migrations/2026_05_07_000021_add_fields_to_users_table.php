<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Profile fields
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            
            // Address
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country', 2)->nullable();
            
            // Account status
            $table->enum('status', ['active', 'suspended', 'banned', 'inactive'])->default('active')->after('email_verified_at');
            $table->text('status_reason')->nullable();
            $table->foreignId('status_changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('status_changed_at')->nullable();
            
            // 2FA
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            
            // Preferences
            $table->string('language', 5)->default('en');
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->nullable();
            
            // Security
            $table->string('api_token', 80)->unique()->nullable()->after('rememberToken');
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            
            // Marketing
            $table->boolean('marketing_emails')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name', 'last_name', 'phone', 'company',
                'address_line1', 'address_line2', 'city', 'state', 'postal_code', 'country',
                'status', 'status_reason', 'status_changed_by', 'status_changed_at',
                'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at',
                'language', 'timezone', 'currency',
                'api_token', 'last_login_at', 'last_login_ip',
                'marketing_emails'
            ]);
        });
    }
};
