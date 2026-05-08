<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add indexes to frequently queried columns
        Schema::table('users', function (Blueprint $table) {
            $table->index(['email'], 'users_email_index');
            $table->index(['status'], 'users_status_index');
            $table->index(['is_admin'], 'users_is_admin_index');
            $table->index(['created_at'], 'users_created_at_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'invoices_user_status_index');
            $table->index(['status'], 'invoices_status_index');
            $table->index(['due_date'], 'invoices_due_date_index');
            $table->index(['invoice_date'], 'invoices_invoice_date_index');
            $table->index(['created_at'], 'invoices_created_at_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'orders_user_status_index');
            $table->index(['status'], 'orders_status_index');
            $table->index(['created_at'], 'orders_created_at_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'services_user_status_index');
            $table->index(['status'], 'services_status_index');
            $table->index(['product_id'], 'services_product_id_index');
            $table->index(['billing_cycle'], 'services_billing_cycle_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['invoice_id'], 'payments_invoice_id_index');
            $table->index(['user_id'], 'payments_user_id_index');
            $table->index(['status'], 'payments_status_index');
            $table->index(['payment_method'], 'payments_payment_method_index');
            $table->index(['created_at'], 'payments_created_at_index');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'tickets_user_status_index');
            $table->index(['status'], 'tickets_status_index');
            $table->index(['department_id'], 'tickets_department_id_index');
            $table->index(['created_at'], 'tickets_created_at_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['category_id'], 'products_category_id_index');
            $table->index(['is_visible'], 'products_is_visible_index');
            $table->index(['type'], 'products_type_index');
            $table->index(['sort_order'], 'products_sort_order_index');
        });

        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->index(['user_id'], 'credit_transactions_user_id_index');
            $table->index(['type'], 'credit_transactions_type_index');
            $table->index(['created_at'], 'credit_transactions_created_at_index');
        });

        Schema::table('user_credits', function (Blueprint $table) {
            $table->index(['user_id'], 'user_credits_user_id_index');
        });

        Schema::table('login_history', function (Blueprint $table) {
            $table->index(['user_id'], 'login_history_user_id_index');
            $table->index(['ip_address'], 'login_history_ip_address_index');
            $table->index(['created_at'], 'login_history_created_at_index');
        });
    }

    public function down(): void
    {
        // Drop indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_index');
            $table->dropIndex('users_status_index');
            $table->dropIndex('users_is_admin_index');
            $table->dropIndex('users_created_at_index');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('invoices_user_status_index');
            $table->dropIndex('invoices_status_index');
            $table->dropIndex('invoices_due_date_index');
            $table->dropIndex('invoices_invoice_date_index');
            $table->dropIndex('invoices_created_at_index');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_user_status_index');
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_created_at_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_user_status_index');
            $table->dropIndex('services_status_index');
            $table->dropIndex('services_product_id_index');
            $table->dropIndex('services_billing_cycle_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_invoice_id_index');
            $table->dropIndex('payments_user_id_index');
            $table->dropIndex('payments_status_index');
            $table->dropIndex('payments_payment_method_index');
            $table->dropIndex('payments_created_at_index');
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_user_status_index');
            $table->dropIndex('tickets_status_index');
            $table->dropIndex('tickets_department_id_index');
            $table->dropIndex('tickets_created_at_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_category_id_index');
            $table->dropIndex('products_is_visible_index');
            $table->dropIndex('products_type_index');
            $table->dropIndex('products_sort_order_index');
        });

        Schema::table('credit_transactions', function (Blueprint $table) {
            $table->dropIndex('credit_transactions_user_id_index');
            $table->dropIndex('credit_transactions_type_index');
            $table->dropIndex('credit_transactions_created_at_index');
        });

        Schema::table('user_credits', function (Blueprint $table) {
            $table->dropIndex('user_credits_user_id_index');
        });

        Schema::table('login_history', function (Blueprint $table) {
            $table->dropIndex('login_history_user_id_index');
            $table->dropIndex('login_history_ip_address_index');
            $table->dropIndex('login_history_created_at_index');
        });
    }
};