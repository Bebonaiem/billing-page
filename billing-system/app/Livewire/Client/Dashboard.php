<?php

namespace App\Livewire\Client;

use App\Models\Service;
use App\Models\Invoice;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Order;
use App\Models\CreditTransaction;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();

        // Calculate comprehensive stats using helper methods
        $activeServices = $user->getActiveServicesCount();
        $suspendedServices = $user->services()->where('status', 'suspended')->count();
        $unpaidInvoices = $user->getUnpaidInvoicesCount();
        $overdueInvoices = $user->getOverdueInvoicesCount();
        $openTickets = $user->getOpenTicketsCount();
        $creditBalance = $user->getCreditBalance();

        // Calculate monthly spending
        $monthlySpending = $user->invoices()
            ->where('status', 'paid')
            ->whereMonth('paid_date', now()->month)
            ->whereYear('paid_date', now()->year)
            ->sum('total');

        // Calculate next due date
        $nextDueDate = $user->invoices()
            ->where('status', 'unpaid')
            ->whereDate('due_date', '>=', now())
            ->orderBy('due_date', 'asc')
            ->first()?->due_date;

        $stats = [
            'active_services' => $activeServices,
            'suspended_services' => $suspendedServices,
            'unpaid_invoices' => $unpaidInvoices,
            'overdue_invoices' => $overdueInvoices,
            'open_tickets' => $openTickets,
            'credit_balance' => $creditBalance,
            'monthly_spending' => $monthlySpending,
            'next_due_date' => $nextDueDate,
        ];

        $services = $user->services()
            ->with(['product', 'invoices'])
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
            ->with(['department', 'replies'])
            ->whereIn('status', ['open', 'answered', 'customer_reply'])
            ->latest()
            ->take(5)
            ->get();

        // Get recent activity
        $recentActivity = $this->getRecentActivity($user);

        return view('livewire.client.dashboard', [
            'stats' => $stats,
            'services' => $services,
            'invoices' => $invoices,
            'tickets' => $tickets,
            'recent_activity' => $recentActivity,
        ]);
    }

    private function getRecentActivity($user)
    {
        $activities = collect();

        // Recent invoices
        $user->invoices()
            ->with('items')
            ->latest()
            ->take(3)
            ->get()
            ->each(function ($invoice) use ($activities) {
                $activities->push([
                    'type' => 'invoice',
                    'title' => 'Invoice #' . $invoice->id,
                    'description' => 'Amount: $' . number_format($invoice->total, 2),
                    'status' => $invoice->status,
                    'created_at' => $invoice->created_at,
                ]);
            });

        // Recent ticket replies
        $user->tickets()
            ->with(['replies' => function ($query) {
                $query->latest()->take(1);
            }])
            ->whereHas('replies')
            ->latest()
            ->take(3)
            ->get()
            ->each(function ($ticket) use ($activities) {
                $lastReply = $ticket->replies->first();
                if ($lastReply && !$lastReply->is_admin) {
                    $activities->push([
                        'type' => 'ticket',
                        'title' => 'Ticket #' . $ticket->id,
                        'description' => 'New reply received',
                        'status' => $ticket->status,
                        'created_at' => $lastReply->created_at,
                    ]);
                }
            });

        // Service status changes
        $user->services()
            ->latest()
            ->take(3)
            ->get()
            ->each(function ($service) use ($activities) {
                $activities->push([
                    'type' => 'service',
                    'title' => $service->product->name,
                    'description' => 'Service ' . ucfirst($service->status),
                    'status' => $service->status,
                    'created_at' => $service->updated_at,
                ]);
            });

        return $activities->sortByDesc('created_at')->take(5)->values();
    }
}
