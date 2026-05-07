<?php

namespace App\Livewire\Client;

use App\Models\Invoice;
use App\Models\PaymentGateway;
use App\Services\Payment\BankTransferGateway;
use App\Services\Payment\StripeGateway;
use App\Services\Payment\PayPalGateway;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Invoices extends Component
{
    use WithPagination;

    public string $status = 'all';
    public bool $showPaymentModal = false;
    public ?Invoice $payingInvoice = null;
    public ?int $selectedGateway = null;
    public ?array $paymentNotice = null;

    protected $queryString = ['status'];

    public function mount()
    {
        $this->payingInvoice = null;
    }

    public function payInvoice(int $invoiceId)
    {
        $this->payingInvoice = Invoice::where('user_id', Auth::id())->findOrFail($invoiceId);
        
        // Get default payment gateway
        $gateway = PaymentGateway::where('is_active', true)->orderBy('sort_order')->first();
        $this->selectedGateway = $gateway?->id;
        
        $this->showPaymentModal = true;
    }

    public function processPayment()
    {
        if (!$this->payingInvoice || !$this->selectedGateway) {
            return;
        }

        $gateway = PaymentGateway::find($this->selectedGateway);
        
        if ($gateway->driver === 'account_credit') {
            $user = Auth::user();

            if (!$user) {
                return;
            }

            $credit = $user->credit;
            
            if ($credit && $credit->canAfford($this->payingInvoice->balance)) {
                $credit->deductCredit($this->payingInvoice->balance, "Invoice #{$this->payingInvoice->invoice_number}", $this->payingInvoice);
                $this->payingInvoice->addPayment($this->payingInvoice->balance, 'account_credit');
                
                session()->flash('success', 'Payment successful!');
                $this->closePaymentModal();
                return;
            } else {
                session()->flash('error', 'Insufficient account credit.');
                return;
            }
        }

        // For external payment gateways, redirect to payment
        $driver = match($gateway->driver) {
            'stripe' => new StripeGateway(),
            'paypal' => new PayPalGateway(),
            'bank_transfer' => new BankTransferGateway(),
            default => null,
        };

        if ($driver) {
            $result = $driver->initializePayment($this->payingInvoice);
            
            if ($result['success'] && isset($result['redirect_url'])) {
                return redirect()->away($result['redirect_url']);
            }

            if ($result['success']) {
                $this->paymentNotice = [
                    'title' => $gateway->display_name,
                    'message' => $result['instructions'] ?? 'Your payment request has been created.',
                    'reference' => $result['reference'] ?? $this->payingInvoice->invoice_number,
                ];

                session()->flash('success', 'Payment instructions created. Follow the instructions to complete your payment.');
                return;
            }
        }
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->payingInvoice = null;
        $this->selectedGateway = null;
        $this->paymentNotice = null;
    }

    public function render()
    {
        $query = Invoice::query()
            ->where('user_id', Auth::id())
            ->with('items')
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            });

        $invoices = $query->latest()->paginate(10);
        $gateways = PaymentGateway::where('is_active', true)->orderBy('sort_order')->get();

        // Calculate totals
        $totalUnpaid = Invoice::where('user_id', Auth::id())
            ->where('status', 'unpaid')
            ->sum('balance');
        
        $totalOverdue = Invoice::where('user_id', Auth::id())
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', now())
            ->sum('balance');

        return view('livewire.client.invoices', [
            'invoices' => $invoices,
            'gateways' => $gateways,
            'totalUnpaid' => $totalUnpaid,
            'totalOverdue' => $totalOverdue,
        ]);
    }
}
