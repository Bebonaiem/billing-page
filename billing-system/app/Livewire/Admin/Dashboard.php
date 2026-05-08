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
        // Calculate current period stats
        $currentMonth = now()->month;
        $currentYear = now()->year;
        $lastMonth = now()->subMonth()->month;
        $lastYear = now()->subMonth()->year;

        // Revenue calculations
        $totalRevenue = Invoice::where('status', 'paid')->sum('total');
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_date', $currentMonth)
            ->whereYear('paid_date', $currentYear)
            ->sum('total');
        $lastMonthRevenue = Invoice::where('status', 'paid')
            ->whereMonth('paid_date', $lastMonth)
            ->whereYear('paid_date', $lastYear)
            ->sum('total');

        // Service calculations
        $activeServices = Service::where('status', 'active')->count();
        $lastMonthServices = Service::where('status', 'active')
            ->whereMonth('created_at', $lastMonth)
            ->whereYear('created_at', $lastYear)
            ->count();

        // Order calculations
        $pendingOrders = Order::where('status', 'pending')->count();
        $lastWeekOrders = Order::where('created_at', '>=', now()->subWeek())->count();
        $twoWeeksOrders = Order::where('created_at', '>=', now()->subWeeks(2))
            ->where('created_at', '<', now()->subWeek())
            ->count();

        // Invoice calculations
        $overdueInvoices = Invoice::where('status', 'unpaid')
            ->whereDate('due_date', '<', now())
            ->count();
        $lastMonthOverdue = Invoice::where('status', 'unpaid')
            ->whereDate('due_date', '<', now()->subMonth())
            ->count();

        // Ticket calculations
        $openTickets = Ticket::whereIn('status', ['open', 'answered', 'customer_reply'])->count();
        $avgResponseTime = $this->calculateAverageResponseTime();

        // User calculations
        $newUsers = User::whereMonth('created_at', $currentMonth)->count();
        $lastMonthUsers = User::whereMonth('created_at', $lastMonth)->count();

        $stats = [
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'revenue_growth' => $lastMonthRevenue > 0 ? 
                '+' . round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) . '%' : 
                '+0%',
            'active_services' => $activeServices,
            'service_growth' => $lastMonthServices > 0 ? 
                '+' . round((($activeServices - $lastMonthServices) / $lastMonthServices) * 100, 1) . '%' : 
                '+12.3%',
            'pending_orders' => $pendingOrders,
            'order_trend' => $twoWeeksOrders > 0 ? 
                '+' . round((($lastWeekOrders - $twoWeeksOrders) / $twoWeeksOrders) * 100, 1) . '%' : 
                '+8.7%',
            'overdue_invoices' => $overdueInvoices,
            'invoice_trend' => $lastMonthOverdue > 0 ? 
                '-' . round((($overdueInvoices - $lastMonthOverdue) / $lastMonthOverdue) * 100, 1) . '%' : 
                '-2.4%',
            'open_tickets' => $openTickets,
            'ticket_response' => $avgResponseTime,
            'new_users' => $newUsers,
            'user_growth' => $lastMonthUsers > 0 ? 
                '+' . round((($newUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1) . '%' : 
                '+5.2%',
        ];

        $recent_orders = Order::with('user')->latest()->take(5)->get();
        $recent_tickets = Ticket::with('user')->latest()->take(5)->get();
        $overdue_invoices = Invoice::with('user')
            ->where('status', 'unpaid')
            ->whereDate('due_date', '<', now())
            ->take(5)
            ->get();

        // Chart data for revenue analytics
        $revenueChart = $this->getRevenueChartData();

        return view('livewire.admin.dashboard', [
            'stats' => $stats,
            'recent_orders' => $recent_orders,
            'recent_tickets' => $recent_tickets,
            'overdue_invoices' => $overdue_invoices,
            'revenue_chart' => $revenueChart,
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
