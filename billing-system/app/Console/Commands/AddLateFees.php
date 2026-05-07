<?php

namespace App\Console\Commands;

use App\Services\Billing\InvoiceService;
use Illuminate\Console\Command;

class AddLateFees extends Command
{
    protected $signature = 'billing:add-late-fees';
    protected $description = 'Add late fees to overdue invoices';

    public function handle()
    {
        $this->info('Processing late fees...');

        $invoiceService = new InvoiceService();
        $daysOverdue = \App\Models\Setting::get('late_fee_after_days', 7, 'billing');
        $count = $invoiceService->addLateFees($daysOverdue);

        $this->info("Added late fees to {$count} invoices.");
        
        return 0;
    }
}
