<?php

namespace App\Services\Billing;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Service;
use App\Models\Order;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class InvoiceService
{
    /**
     * Generate an invoice for an order
     */
    public function generateFromOrder(Order $order): Invoice
    {
        try {
            DB::beginTransaction();
            
            // Generate unique invoice number
            $invoiceNumber = $this->generateInvoiceNumber();
            
            $invoice = new Invoice([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'status' => 'unpaid',
                'subtotal' => 0,
                'discount' => $order->discount ?? 0,
                'tax' => 0,
                'total' => 0,
                'balance' => 0,
                'invoice_date' => now(),
                'due_date' => now()->addDays(14), // 14 days payment terms
            ]);
            
            $invoice->save();
            
            $subtotal = 0;
            
            foreach ($order->items as $orderItem) {
                // Validate order item data
                if (!$orderItem->product_name || $orderItem->price < 0) {
                    Log::warning("Invalid order item data for order {$order->id}: " . json_encode($orderItem->toArray()));
                    continue;
                }
                
                $item = new InvoiceItem([
                    'invoice_id' => $invoice->id,
                    'order_id' => $order->id,
                    'type' => 'service',
                    'description' => $orderItem->product_name,
                    'quantity' => $orderItem->quantity ?? 1,
                    'unit_price' => $orderItem->price,
                    'total' => $orderItem->price * ($orderItem->quantity ?? 1),
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
                    $subtotal += $orderItem->setup_fee;
                }
                
                $subtotal += $orderItem->price * ($orderItem->quantity ?? 1);
            }
            
            // Calculate tax if enabled
            $taxRate = $this->getTaxRate($order->user);
            $tax = $subtotal * ($taxRate / 100);
            
            $invoice->subtotal = $subtotal;
            $invoice->tax = $tax;
            $invoice->total = $subtotal + $tax - $invoice->discount;
            $invoice->balance = $invoice->total;
            $invoice->save();
            
            DB::commit();
            
            Log::info("Invoice {$invoice->invoice_number} generated for order {$order->id}");
            
            return $invoice;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate invoice for order {$order->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate a recurring invoice for a service
     */
    public function generateRecurringInvoice(Service $service): ?Invoice
    {
        try {
            // Check if service is eligible for recurring billing
            if (!$service->isActive() || !$service->auto_renew) {
                return null;
            }
            
            // Check if there's already an unpaid invoice for this service
            $existingInvoice = Invoice::where('service_id', $service->id)
                ->where('status', 'unpaid')
                ->whereDate('due_date', '>=', now())
                ->first();
                
            if ($existingInvoice) {
                return null; // Don't create duplicate invoices
            }
            
            DB::beginTransaction();
            
            $invoiceNumber = $this->generateInvoiceNumber();
            
            $invoice = new Invoice([
                'user_id' => $service->user_id,
                'service_id' => $service->id,
                'invoice_number' => $invoiceNumber,
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
            
            // Calculate tax
            $taxRate = $this->getTaxRate($service->user);
            $tax = $service->price * ($taxRate / 100);
            
            $invoice->tax = $tax;
            $invoice->total = $service->price + $tax;
            $invoice->balance = $invoice->total;
            $invoice->save();
            
            // Create invoice item
            $item = new InvoiceItem([
                'invoice_id' => $invoice->id,
                'service_id' => $service->id,
                'type' => 'service',
                'description' => $service->product->name . ' - ' . ucfirst($service->billing_cycle),
                'quantity' => 1,
                'unit_price' => $service->price,
                'total' => $service->price,
                'tax' => $tax,
            ]);
            $item->save();
            
            DB::commit();
            
            Log::info("Recurring invoice {$invoice->invoice_number} generated for service {$service->id}");
            
            return $invoice;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate recurring invoice for service {$service->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $prefix = Setting::get('invoice_prefix', 'INV');
        $year = now()->format('Y');
        $month = now()->format('m');
        
        do {
            $sequence = str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $invoiceNumber = "{$prefix}-{$year}{$month}-{$sequence}";
        } while (Invoice::where('invoice_number', $invoiceNumber)->exists());
        
        return $invoiceNumber;
    }

    /**
     * Get tax rate for user
     */
    private function getTaxRate(User $user): float
    {
        // Get tax rate from settings or user's country
        $taxEnabled = Setting::get('tax_enabled', false);
        
        if (!$taxEnabled) {
            return 0;
        }
        
        // You can implement country-specific tax rates here
        $defaultTaxRate = Setting::get('default_tax_rate', 0);
        
        return (float) $defaultTaxRate;
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Invoice $invoice, float $amount = null, string $method = 'manual'): bool
    {
        try {
            DB::beginTransaction();
            
            $paymentAmount = $amount ?? $invoice->balance;
            
            if ($paymentAmount <= 0) {
                throw new Exception('Payment amount must be greater than 0');
            }
            
            // Update invoice
            $invoice->balance -= $paymentAmount;
            if ($invoice->balance <= 0) {
                $invoice->status = 'paid';
                $invoice->paid_date = now();
                $invoice->balance = 0;
            }
            $invoice->save();
            
            // Create payment record
            $payment = new \App\Models\Payment([
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'amount' => $paymentAmount,
                'method' => $method,
                'status' => 'completed',
                'transaction_id' => uniqid('pay_'),
            ]);
            $payment->save();
            
            DB::commit();
            
            Log::info("Invoice {$invoice->invoice_number} marked as paid with amount {$paymentAmount}");
            
            return true;
            
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Failed to mark invoice {$invoice->invoice_number} as paid: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add late fee to overdue invoices
     */
    public function addLateFees(): int
    {
        $lateFeeRate = (float) Setting::get('late_fee_rate', 5); // 5% default
        $gracePeriod = (int) Setting::get('late_fee_grace_period', 7); // 7 days default
        
        $overdueInvoices = Invoice::where('status', 'unpaid')
            ->whereDate('due_date', '<', now()->subDays($gracePeriod))
            ->where('late_fee_applied', false)
            ->get();
            
        $count = 0;
        
        foreach ($overdueInvoices as $invoice) {
            try {
                $lateFee = $invoice->balance * ($lateFeeRate / 100);
                
                // Add late fee as invoice item
                $item = new InvoiceItem([
                    'invoice_id' => $invoice->id,
                    'type' => 'late_fee',
                    'description' => 'Late Fee (' . $lateFeeRate . '%)',
                    'quantity' => 1,
                    'unit_price' => $lateFee,
                    'total' => $lateFee,
                    'tax' => 0,
                ]);
                $item->save();
                
                // Update invoice totals
                $invoice->total += $lateFee;
                $invoice->balance += $lateFee;
                $invoice->late_fee_applied = true;
                $invoice->save();
                
                $count++;
                
            } catch (Exception $e) {
                Log::error("Failed to add late fee to invoice {$invoice->invoice_number}: " . $e->getMessage());
            }
        }
        
        Log::info("Added late fees to {$count} overdue invoices");
        
        return $count;
    }
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
