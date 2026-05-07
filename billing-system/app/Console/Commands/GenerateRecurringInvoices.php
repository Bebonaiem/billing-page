<?php

namespace App\Console\Commands;

use App\Services\Billing\InvoiceService;
use Illuminate\Console\Command;

class GenerateRecurringInvoices extends Command
{
    protected $signature = 'billing:generate-invoices';
    protected $description = 'Generate recurring invoices for services due for renewal';

    public function handle()
    {
        $this->info('Generating recurring invoices...');

        $invoiceService = new InvoiceService();
        $invoices = $invoiceService->generateRecurringInvoices();

        $count = count($invoices);
        $this->info("Generated {$count} invoices.");

        // Send email notifications
        $emailService = new \App\Services\Email\EmailService();
        foreach ($invoices as $invoice) {
            $emailService->queueTemplate('invoice', $invoice->user, [
                'name' => $invoice->user->getFullName(),
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date->format('M d, Y'),
                'due_date' => $invoice->due_date->format('M d, Y'),
                'total' => number_format($invoice->total, 2),
                'balance' => number_format($invoice->balance, 2),
                'invoice_url' => route('client.invoices.print', $invoice),
            ]);
        }

        $this->info('Invoice generation complete.');
        return 0;
    }
}
