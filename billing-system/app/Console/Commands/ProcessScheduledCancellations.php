<?php

namespace App\Console\Commands;

use App\Models\Service;
use App\Services\Order\OrderService;
use Illuminate\Console\Command;

class ProcessScheduledCancellations extends Command
{
    protected $signature = 'billing:process-cancellations';
    protected $description = 'Process scheduled service cancellations';

    public function handle()
    {
        $this->info('Processing scheduled cancellations...');

        $orderService = new OrderService();

        // Find services scheduled for cancellation
        $services = Service::where('cancellation_requested', true)
            ->where('cancellation_type', 'end_of_term')
            ->whereNotNull('next_invoice_date')
            ->whereDate('next_invoice_date', '<=', now())
            ->where('status', '!=', 'cancelled')
            ->get();

        $count = 0;
        foreach ($services as $service) {
            $orderService->cancelOrder($service->order, 'immediate', 'Scheduled end-of-term cancellation');
            $count++;
        }

        $this->info("Processed {$count} scheduled cancellations.");
        return 0;
    }
}
