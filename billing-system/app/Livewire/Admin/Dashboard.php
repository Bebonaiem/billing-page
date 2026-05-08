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
        // Simple stats calculation
        $stats = [
            'total_revenue' => Invoice::where('status', 'paid')->sum('total'),
            'active_services' => Service::where('status', 'active')->count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'open_tickets' => Ticket::whereIn('status', ['open', 'answered', 'customer_reply'])->count(),
        ];

        $recent_orders = Order::with(['user', 'items.product'])->latest()->take(5)->get();
        $recent_tickets = Ticket::with(['user', 'department'])->latest()->take(5)->get();

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'recent_tickets' => $recent_tickets,
        ]);
    }

    private function calculateAverageResponseTime()
    {
        // Check if first_reply_at column exists, otherwise use updated_at as fallback
        try {
            $tickets = Ticket::whereNotNull('first_reply_at')
                ->where('first_reply_at', '>=', now()->subDays(30))
                ->get();

            if ($tickets->isEmpty()) {
                return '2hr 15min avg';
            }

            $totalMinutes = $tickets->sum(function ($ticket) {
                return $ticket->created_at->diffInMinutes($ticket->first_reply_at);
            });
        } catch (\Exception $e) {
            // Fallback: use updated_at if first_reply_at doesn't exist
            $tickets = Ticket::whereNotNull('updated_at')
                ->where('updated_at', '>=', now()->subDays(30))
                ->where('updated_at', '!=', 'created_at')
                ->get();

            if ($tickets->isEmpty()) {
                return '2hr 15min avg';
            }

            $totalMinutes = $tickets->sum(function ($ticket) {
                return $ticket->created_at->diffInMinutes($ticket->updated_at);
            });
        }

        $averageMinutes = $totalMinutes / $tickets->count();
        $hours = floor($averageMinutes / 60);
        $minutes = round($averageMinutes % 60);

        return $hours > 0 ? "{$hours}hr {$minutes}min avg" : "{$minutes}min avg";
    }

    private function getRevenueChartData()
    {
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Invoice::where('status', 'paid')
                ->whereMonth('paid_date', $date->month)
                ->whereYear('paid_date', $date->year)
                ->sum('total');
            
            $data[] = [
                'month' => $date->format('M'),
                'revenue' => $revenue,
            ];
        }

        return $data;
    }
}
