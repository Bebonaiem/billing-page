<?php

namespace App\Livewire\Client;

use App\Models\Service;
use App\Models\Invoice;
use App\Models\Ticket;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $user = auth()->user();

        $stats = [
            'active_services' => $user->getActiveServicesCount(),
            'unpaid_invoices' => $user->getUnpaidInvoicesCount(),
            'overdue_invoices' => $user->getOverdueInvoicesCount(),
            'open_tickets' => $user->getOpenTicketsCount(),
            'credit_balance' => $user->getCreditBalance(),
        ];

        $services = $user->services()
            ->with('product')
            ->whereIn('status', ['active', 'suspended'])
            ->latest()
            ->take(5)
            ->get();

        $invoices = $user->invoices()
            ->where('status', 'unpaid')
            ->latest()
            ->take(5)
            ->get();

        $tickets = $user->tickets()
            ->whereIn('status', ['open', 'answered', 'customer_reply'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.client.dashboard', [
            'stats' => $stats,
            'services' => $services,
            'invoices' => $invoices,
            'tickets' => $tickets,
        ])->layout('layouts.client');
    }
}
