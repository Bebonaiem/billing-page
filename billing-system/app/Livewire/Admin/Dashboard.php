<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use App\Models\Service;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [
            'total_revenue' => Invoice::where('status', 'paid')->sum('total'),
            'monthly_revenue' => Invoice::where('status', 'paid')
                ->whereMonth('paid_date', now()->month)
                ->whereYear('paid_date', now()->year)
                ->sum('total'),
            'active_services' => Service::where('status', 'active')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'overdue_invoices' => Invoice::where('status', 'unpaid')->whereDate('due_date', '<', now())->count(),
            'open_tickets' => Ticket::whereIn('status', ['open', 'answered', 'customer_reply'])->count(),
            'new_users' => User::whereMonth('created_at', now()->month)->count(),
        ];

        $recent_orders = Order::with('user')->latest()->take(5)->get();
        $recent_tickets = Ticket::with('user')->latest()->take(5)->get();
        $overdue_invoices = Invoice::with('user')
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', now())
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'recent_tickets' => $recent_tickets,
            'overdue_invoices' => $overdue_invoices,
        ])->layout('layouts.admin', ['header' => 'Dashboard']);
    }
}
