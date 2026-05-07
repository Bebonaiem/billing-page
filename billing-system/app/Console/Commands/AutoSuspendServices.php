<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Models\Invoice;
use App\Services\Order\OrderService;
use App\Services\Email\EmailService;
use Illuminate\Console\Command;

class AutoSuspendServices extends Command
{
    protected $signature = 'billing:auto-suspend';
    protected $description = 'Automatically suspend services with overdue invoices';

    public function handle()
    {
        $this->info('Processing auto-suspensions...');

        $suspendDays = \App\Models\Setting::get('suspend_after_days', 7, 'billing');
        $enabled = \App\Models\Setting::get('auto_suspend_enabled', true, 'billing');

        if (!$enabled) {
            $this->warn('Auto-suspend is disabled.');
            return 0;
        }

        $orderService = new OrderService();
        $emailService = new EmailService();

        // Find services with overdue invoices
        $overdueServices = Service::whereHas('user.invoices', function ($query) use ($suspendDays) {
            $query->where('status', 'unpaid')
                ->where('is_overdue', true)
                ->where('due_date', '<', now()->subDays($suspendDays));
        })->where('status', 'active')->get();

        $count = 0;
        foreach ($overdueServices as $service) {
            $orderService->suspendOrder($service->order, 'Overdue invoice - auto suspend');
            $emailService->sendSuspensionNotice($service, 'Overdue payment');
            $count++;
        }

        $this->info("Suspended {$count} services.");
        return 0;
    }
}
