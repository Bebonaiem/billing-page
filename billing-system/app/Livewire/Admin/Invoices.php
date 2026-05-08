<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Invoices extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status = 'all';
    public ?int $userId = null;
    public bool $showModal = false;
    public ?Invoice $editingInvoice = null;

    protected $queryString = ['search', 'status', 'userId'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewInvoice(int $invoiceId)
    {
        $this->editingInvoice = Invoice::with(['user', 'items.service', 'payments'])->findOrFail($invoiceId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingInvoice = null;
    }

    public function markAsPaid(int $invoiceId)
    {
        $invoice = Invoice::with('user', 'order')->findOrFail($invoiceId);

        if ($invoice->isPaid()) {
            session()->flash('success', 'Invoice is already paid.');
            return;
        }

        $invoice->addPayment(
            (float) $invoice->balance,
            'manual',
            'admin-manual-' . $invoice->invoice_number,
            [
                'notes' => 'Marked as paid by admin',
                'processed_by' => Auth::id(),
            ]
        );

        $this->editingInvoice = $invoice->fresh(['user', 'items.service', 'payments']);
        session()->flash('success', 'Invoice marked as paid.');
    }

    public function cancelInvoice(int $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $invoice->update([
            'status' => 'cancelled',
            'cancelled_date' => now(),
        ]);
        session()->flash('success', 'Invoice cancelled.');
    }

    public function render()
    {
        $query = Invoice::query()
            ->with(['user:id,name,email,first_name,last_name'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', "%{$this->search}%")
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('email', 'like', "%{$this->search}%")
                                     ->orWhere('name', 'like', "%{$this->search}%");
                        });
                    });
            })
            ->when($this->status !== 'all', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->userId, function ($query) {
                $query->where('user_id', $this->userId);
            });

        $invoices = $query->latest()->paginate(15);
        
        // Cache the users list to avoid repeated queries
        $users = User::select('id', 'name', 'email', 'first_name', 'last_name')
                    ->orderBy('name')
                    ->remember(now()->addHours(1))
                    ->get();

        return view('livewire.admin.invoices', [
            'invoices' => $invoices,
            'users' => $users,
        ]);
    }
}
