<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;

class InitApp extends Command
{
    protected $signature = 'app:init';
    protected $description = 'Initialize application settings';

    public function handle()
    {
        $this->info('Initializing BillingHub application settings...');

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

        $this->info('Application initialization complete!');
        return 0;
    }
}
