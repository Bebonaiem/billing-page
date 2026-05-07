<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Generate an invoice for an order
     */
    public function generateFromOrder(Order $order): Invoice
    {
        $invoice = new Invoice([
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'status' => 'unpaid',
            'subtotal' => 0,
            'discount' => $order->discount,
            'tax' => 0,
            'total' => 0,
            'balance' => 0,
            'invoice_date' => now(),
            'due_date' => now()->addDays(14), // 14 days payment terms
        ]);
        
        $invoice->save();
        
        $subtotal = 0;
        
        foreach ($order->items as $orderItem) {
            $item = new InvoiceItem([
                'invoice_id' => $invoice->id,
                'order_id' => $order->id,
                'type' => 'service',
                'description' => $orderItem->product_name,
                'quantity' => 1,
                'unit_price' => $orderItem->price,
                'total' => $orderItem->price,
                'tax' => 0,
            ]);
            $item->save();
            
            // Setup fee item
            if ($orderItem->setup_fee > 0) {
                $setupItem = new InvoiceItem([
                    'invoice_id' => $invoice->id,
                    'order_id' => $order->id,
                    'type' => 'setup',
                    'description' => "Setup Fee - {$orderItem->product_name}",
                    'quantity' => 1,
                    'unit_price' => $orderItem->setup_fee,
                    'total' => $orderItem->setup_fee,
                    'tax' => 0,
                ]);
                $setupItem->save();
            }
            
            $subtotal += $orderItem->price + $orderItem->setup_fee;
        }
        
        $invoice->subtotal = $subtotal;
        $invoice->total = $subtotal - $invoice->discount;
        $invoice->balance = $invoice->total;
        $invoice->save();
        
        return $invoice;
    }

    /**
     * Generate a recurring invoice for a service
     */
    public function generateRecurringInvoice(Service $service): ?Invoice
    {
        if (!$service->isActive() || !$service->auto_renew) {
            return null;
        }
        
        $invoice = new Invoice([
            'user_id' => $service->user_id,
            'status' => 'unpaid',
            'subtotal' => $service->price,
            'discount' => 0,
            'tax' => 0,
            'total' => $service->price,
            'balance' => $service->price,
            'invoice_date' => now(),
            'due_date' => now()->addDays(7), // 7 days for recurring
        ]);
        
        $invoice->save();
        
        // Create invoice item
        $item = new InvoiceItem([
            'invoice_id' => $invoice->id,
            'service_id' => $service->id,
            'type' => 'service',
            'description' => "Renewal - {$service->name}",
            'quantity' => 1,
            'unit_price' => $service->price,
            'total' => $service->price,
            'tax' => 0,
            'period_start' => $service->next_invoice_date,
            'period_end' => $this->calculatePeriodEnd($service),
        ]);
        $item->save();
        
        // Update service next invoice date
        $service->last_invoice_date = $service->next_invoice_date;
        $service->next_invoice_date = $this->calculateNextInvoiceDate($service);
        $service->save();
        
        return $invoice;
    }

    /**
     * Generate invoices for all services due for renewal
     */
    public function generateRecurringInvoices(): array
    {
        $services = Service::forRenewal()->get();
        $invoices = [];
        
        foreach ($services as $service) {
            $invoice = $this->generateRecurringInvoice($service);
            if ($invoice) {
                $invoices[] = $invoice;
            }
        }
        
        return $invoices;
    }

    /**
     * Add late fee to overdue invoices
     */
    public function addLateFees(int $daysOverdue = 7): int
    {
        $invoices = Invoice::where('status', 'unpaid')
            ->where('late_fee_added', false)
            ->whereDate('due_date', '<', now()->subDays($daysOverdue))
            ->get();
        
        $count = 0;
        
        foreach ($invoices as $invoice) {
            $lateFeeAmount = \App\Models\Setting::get('late_fee_amount', 5.00, 'billing');
            
            $item = new InvoiceItem([
                'invoice_id' => $invoice->id,
                'type' => 'late_fee',
                'description' => 'Late Payment Fee',
                'quantity' => 1,
                'unit_price' => $lateFeeAmount,
                'total' => $lateFeeAmount,
                'tax' => 0,
            ]);
            $item->save();
            
            $invoice->late_fee_added = true;
            $invoice->late_fee_amount = $lateFeeAmount;
            $invoice->recalculateTotals();
            $count++;
        }
        
        return $count;
    }

    /**
     * Cancel an invoice
     */
    public function cancelInvoice(Invoice $invoice, string $reason = ''): bool
    {
        if ($invoice->status === 'paid') {
            return false;
        }
        
        $invoice->update([
            'status' => 'cancelled',
            'cancelled_date' => now(),
            'notes' => $reason ? "Cancelled: {$reason}" : $invoice->notes,
        ]);
        
        return true;
    }

    /**
     * Calculate the end of the billing period
     */
    protected function calculatePeriodEnd(Service $service): ?\DateTime
    {
        $start = $service->next_invoice_date;
        
        return match($service->billing_cycle) {
            'monthly' => $start?->clone()->addMonth(),
            'quarterly' => $start?->clone()->addMonths(3),
            'semi_annually' => $start?->clone()->addMonths(6),
            'annually' => $start?->clone()->addYear(),
            'biennially' => $start?->clone()->addYears(2),
            default => $start?->clone()->addMonth(),
        };
    }

    /**
     * Calculate the next invoice date
     */
    protected function calculateNextInvoiceDate(Service $service): ?\DateTime
    {
        $base = $service->next_invoice_date ?? now();
        
        return match($service->billing_cycle) {
            'monthly' => $base->clone()->addMonth(),
            'quarterly' => $base->clone()->addMonths(3),
            'semi_annually' => $base->clone()->addMonths(6),
            'annually' => $base->clone()->addYear(),
            'biennially' => $base->clone()->addYears(2),
            default => $base->clone()->addMonth(),
        };
    }

    /**
     * Generate PDF invoice
     */
    public function generatePdf(Invoice $invoice): string
    {
        $invoice->loadMissing(['user', 'items', 'payments', 'order']);

        $lines = $this->buildPdfLines($invoice);
        $pdf = $this->buildPdfDocument($lines);

        $path = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::disk('local')->put($path, $pdf);

        return storage_path('app/' . $path);
    }

    protected function buildPdfLines(Invoice $invoice): array
    {
        $lines = [
            'Invoice ' . $invoice->invoice_number,
            config('app.name'),
            'Date: ' . $invoice->invoice_date->format('M d, Y'),
            'Due: ' . $invoice->due_date->format('M d, Y'),
            'Status: ' . ucfirst($invoice->status),
            '',
            'Billed To: ' . $invoice->user->getFullName(),
            'Email: ' . $invoice->user->email,
            '',
            'Items:',
        ];

        foreach ($invoice->items as $item) {
            $lines[] = sprintf(
                '- %s | Qty %s | Unit $%0.2f | Total $%0.2f',
                $item->description,
                $item->quantity,
                (float) $item->unit_price,
                (float) $item->total
            );
        }

        $lines[] = '';
        $lines[] = sprintf('Subtotal: $%0.2f', (float) $invoice->subtotal);
        $lines[] = sprintf('Discount: -$%0.2f', (float) $invoice->discount);
        $lines[] = sprintf('Tax: $%0.2f', (float) $invoice->tax);

        if ((float) $invoice->late_fee_amount > 0) {
            $lines[] = sprintf('Late Fee: $%0.2f', (float) $invoice->late_fee_amount);
        }

        $lines[] = sprintf('Total Due: $%0.2f', (float) $invoice->balance);

        if ($invoice->payments->isNotEmpty()) {
            $lines[] = '';
            $lines[] = 'Payments:';

            foreach ($invoice->payments as $payment) {
                $lines[] = sprintf(
                    '- %s | $%0.2f | %s',
                    ucfirst($payment->payment_method),
                    (float) $payment->amount,
                    $payment->transaction_id ?? 'Manual payment'
                );
            }
        }

        return $lines;
    }

    protected function buildPdfDocument(array $lines): string
    {
        $contentStream = $this->buildPdfContentStream($lines);
        $objects = [];

        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 5 0 R >> >> /Contents 4 0 R >>\nendobj\n";
        $objects[] = sprintf("4 0 obj\n<< /Length %d >>\nstream\n%s\nendstream\nendobj\n", strlen($contentStream), $contentStream);
        $objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $object) {
            $offsets[] = strlen($pdf);
            $pdf .= $object;
        }

        $xrefStart = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($index = 1; $index <= count($objects); $index++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$index]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefStart}\n%%EOF";

        return $pdf;
    }

    protected function buildPdfContentStream(array $lines): string
    {
        $y = 760;
        $segments = [
            "BT",
            "/F1 12 Tf",
        ];

        foreach ($lines as $line) {
            $segments[] = sprintf("1 0 0 1 50 %d Tm", $y);
            $segments[] = '(' . $this->escapePdfText($line) . ') Tj';
            $y -= 16;
            if ($y < 50) {
                break;
            }
        }

        $segments[] = "ET";

        return implode("\n", $segments);
    }

    protected function escapePdfText(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
