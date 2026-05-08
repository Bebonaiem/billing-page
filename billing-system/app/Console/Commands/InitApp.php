<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\Currency;
use App\Models\PaymentGateway;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InitApp extends Command
{
    protected $signature = 'app:init {--sample : Create sample data}';
    protected $description = 'Initialize application settings and create sample data';

    public function handle()
    {
        $this->info('Initializing BillingHub application...');

        $this->initializeSettings();
        
        if ($this->option('sample')) {
            $this->createSampleData();
        }

        $this->info('Application initialization complete!');
        return 0;
    }

    private function initializeSettings()
    {
        $this->info('Setting up application settings...');

        $settings = [
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
            'app_timezone' => config('app.timezone'),
            'currency' => 'USD',
            'invoice_prefix' => 'INV-',
            'quote_prefix' => 'QUOTE-',
            'ticket_prefix' => 'TKT-',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
            $this->line("✓ Set {$key}");
        }
    }

    private function createSampleData()
    {
        $this->info('Creating sample data...');

        $this->createCurrencies();
        $this->createPaymentGateways();
        $this->createCategories();
        $this->createProducts();
        $this->createUsers();
        $this->createOrders();
        $this->createServices();
        $this->createInvoices();
        $this->createTickets();
    }

    private function createCurrencies()
    {
        $this->line('Creating currencies...');
        Currency::firstOrCreate(['code' => 'USD'], [
            'name' => 'US Dollar',
            'symbol' => '$',
            'rate' => 1.0,
            'is_default' => true,
        ]);

        Currency::firstOrCreate(['code' => 'EUR'], [
            'name' => 'Euro',
            'symbol' => '€',
            'rate' => 0.85,
            'is_default' => false,
        ]);
    }

    private function createPaymentGateways()
    {
        $this->line('Creating payment gateways...');
        PaymentGateway::firstOrCreate(['driver' => 'stripe'], [
            'name' => 'Stripe',
            'is_active' => true,
            'settings' => json_encode(['publishable_key' => '', 'secret_key' => '']),
        ]);

        PaymentGateway::firstOrCreate(['driver' => 'paypal'], [
            'name' => 'PayPal',
            'is_active' => true,
            'settings' => json_encode(['client_id' => '', 'client_secret' => '', 'sandbox' => true]),
        ]);
    }

    private function createCategories()
    {
        $this->line('Creating categories...');
        Category::firstOrCreate(['name' => 'Web Hosting'], [
            'slug' => 'web-hosting',
            'description' => 'Shared and dedicated hosting solutions',
            'icon' => 'server',
            'sort_order' => 1,
        ]);

        Category::firstOrCreate(['name' => 'VPS Hosting'], [
            'slug' => 'vps-hosting',
            'description' => 'Virtual Private Server hosting',
            'icon' => 'cloud',
            'sort_order' => 2,
        ]);

        Category::firstOrCreate(['name' => 'Domains'], [
            'slug' => 'domains',
            'description' => 'Domain registration services',
            'icon' => 'globe',
            'sort_order' => 3,
        ]);
    }

    private function createProducts()
    {
        $this->line('Creating products...');
        
        $webHosting = Category::where('name', 'Web Hosting')->first();
        $vpsHosting = Category::where('name', 'VPS Hosting')->first();
        $domains = Category::where('name', 'Domains')->first();

        // Web Hosting Products
        Product::firstOrCreate(['name' => 'Starter Hosting'], [
            'category_id' => $webHosting->id,
            'slug' => 'starter-hosting',
            'description' => 'Perfect for small websites and blogs',
            'price' => 9.99,
            'setup_fee' => 0,
            'billing_cycle' => 'monthly',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        Product::firstOrCreate(['name' => 'Business Hosting'], [
            'category_id' => $webHosting->id,
            'slug' => 'business-hosting',
            'description' => 'Ideal for growing businesses',
            'price' => 19.99,
            'setup_fee' => 0,
            'billing_cycle' => 'monthly',
            'is_visible' => true,
            'sort_order' => 2,
        ]);

        // VPS Products
        Product::firstOrCreate(['name' => 'VPS Basic'], [
            'category_id' => $vpsHosting->id,
            'slug' => 'vps-basic',
            'description' => 'Entry-level VPS with 2GB RAM',
            'price' => 29.99,
            'setup_fee' => 0,
            'billing_cycle' => 'monthly',
            'is_visible' => true,
            'sort_order' => 1,
        ]);

        // Domain Products
        Product::firstOrCreate(['name' => '.com Domain'], [
            'category_id' => $domains->id,
            'slug' => 'com-domain',
            'description' => 'Popular .com domain registration',
            'price' => 14.99,
            'setup_fee' => 0,
            'billing_cycle' => 'yearly',
            'is_visible' => true,
            'sort_order' => 1,
        ]);
    }

    private function createUsers()
    {
        $this->line('Creating users...');
        
        // Admin user
        $admin = User::firstOrCreate(['email' => 'admin@example.com'], [
            'name' => 'Admin User',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'status' => 'active',
            'language' => 'en',
            'timezone' => 'UTC',
            'currency' => 'USD',
        ]);

        // Regular users
        $users = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'first_name' => 'John', 'last_name' => 'Doe'],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'first_name' => 'Jane', 'last_name' => 'Smith'],
            ['name' => 'Bob Johnson', 'email' => 'bob@example.com', 'first_name' => 'Bob', 'last_name' => 'Johnson'],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(['email' => $userData['email']], [
                'name' => $userData['name'],
                'first_name' => $userData['first_name'],
                'last_name' => $userData['last_name'],
                'password' => Hash::make('password'),
                'is_admin' => false,
                'status' => 'active',
                'language' => 'en',
                'timezone' => 'UTC',
                'currency' => 'USD',
                'company' => 'Sample Company',
                'phone' => '+1-555-0123',
            ]);
        }
    }

    private function createOrders()
    {
        $this->line('Creating orders...');
        
        $users = User::where('is_admin', false)->get();
        $products = Product::where('is_visible', true)->get();

        foreach ($users as $user) {
            foreach (range(1, 2) as $i) {
                $product = $products->random();
                
                Order::firstOrCreate([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'status' => 'completed',
                ], [
                    'order_number' => 'ORD-' . Str::random(8),
                    'total' => $product->price,
                    'currency' => 'USD',
                    'created_at' => now()->subDays(rand(1, 30)),
                ]);
            }
        }
    }

    private function createServices()
    {
        $this->line('Creating services...');
        
        $orders = Order::where('status', 'completed')->get();
        $statuses = ['active', 'suspended', 'active'];

        foreach ($orders as $order) {
            Service::firstOrCreate(['order_id' => $order->id], [
                'user_id' => $order->user_id,
                'product_id' => $order->product_id,
                'status' => $statuses[array_rand($statuses)],
                'due_date' => now()->addDays(rand(10, 30)),
                'created_at' => $order->created_at,
            ]);
        }
    }

    private function createInvoices()
    {
        $this->line('Creating invoices...');
        
        $services = Service::all();
        $statuses = ['paid', 'unpaid', 'paid', 'overdue'];

        foreach ($services as $service) {
            $status = $statuses[array_rand($statuses)];
            $dueDate = $status === 'overdue' ? now()->subDays(5) : now()->addDays(rand(5, 20));
            
            Invoice::firstOrCreate(['service_id' => $service->id], [
                'user_id' => $service->user_id,
                'invoice_number' => 'INV-' . Str::random(8),
                'status' => $status,
                'total' => $service->product->price,
                'currency' => 'USD',
                'due_date' => $dueDate,
                'paid_date' => $status === 'paid' ? now()->subDays(rand(1, 10)) : null,
                'created_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    private function createTickets()
    {
        $this->line('Creating tickets...');
        
        $users = User::where('is_admin', false)->get();
        $departments = ['billing', 'technical', 'sales', 'general'];
        $priorities = ['low', 'medium', 'high'];
        $statuses = ['open', 'answered', 'closed', 'open'];

        foreach ($users as $user) {
            foreach (range(1, 2) as $i) {
                Ticket::firstOrCreate([
                    'user_id' => $user->id,
                    'subject' => 'Sample Ticket #' . $i,
                ], [
                    'ticket_number' => 'TKT-' . Str::random(8),
                    'department' => $departments[array_rand($departments)],
                    'priority' => $priorities[array_rand($priorities)],
                    'status' => $statuses[array_rand($statuses)],
                    'message' => 'This is a sample support ticket message.',
                    'created_at' => now()->subDays(rand(1, 20)),
                ]);
            }
        }
    }
}
