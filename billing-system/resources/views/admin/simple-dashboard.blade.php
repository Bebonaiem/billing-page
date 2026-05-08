@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900/20 to-blue-900/20 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-4">Admin Dashboard</h1>
            <p class="text-secondary">Welcome back! Here's your system overview.</p>
        </div>

        <!-- Simple Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-card rounded-lg p-6 border border-custom">
                <h3 class="text-lg font-semibold text-white mb-2">Total Revenue</h3>
                <p class="text-3xl font-bold text-green-400">${{ number_format(\App\Models\Invoice::where('status', 'paid')->sum('total'), 2) }}</p>
            </div>
            
            <div class="bg-card rounded-lg p-6 border border-custom">
                <h3 class="text-lg font-semibold text-white mb-2">Active Services</h3>
                <p class="text-3xl font-bold text-blue-400">{{ \App\Models\Service::where('status', 'active')->count() }}</p>
            </div>
            
            <div class="bg-card rounded-lg p-6 border border-custom">
                <h3 class="text-lg font-semibold text-white mb-2">Pending Orders</h3>
                <p class="text-3xl font-bold text-yellow-400">{{ \App\Models\Order::where('status', 'pending')->count() }}</p>
            </div>
            
            <div class="bg-card rounded-lg p-6 border border-custom">
                <h3 class="text-lg font-semibold text-white mb-2">Open Tickets</h3>
                <p class="text-3xl font-bold text-purple-400">{{ \App\Models\Ticket::whereIn('status', ['open', 'answered'])->count() }}</p>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-card rounded-lg p-6 border border-custom">
                <h3 class="text-lg font-semibold text-white mb-4">Recent Orders</h3>
                <div class="space-y-3">
                    @php
                        $recentOrders = \App\Models\Order::with('user')->latest()->take(5)->get();
                    @endphp
                    @forelse($recentOrders as $order)
                        <div class="flex items-center justify-between p-3 bg-card/50 rounded-lg">
                            <div>
                                <p class="text-white font-medium">{{ $order->order_number }}</p>
                                <p class="text-sm text-secondary">{{ $order->user->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-500/20 text-blue-400">
                                {{ $order->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-secondary">No recent orders</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-card rounded-lg p-6 border border-custom">
                <h3 class="text-lg font-semibold text-white mb-4">Recent Tickets</h3>
                <div class="space-y-3">
                    @php
                        $recentTickets = \App\Models\Ticket::with('user')->latest()->take(5)->get();
                    @endphp
                    @forelse($recentTickets as $ticket)
                        <div class="flex items-center justify-between p-3 bg-card/50 rounded-lg">
                            <div>
                                <p class="text-white font-medium">#{{ $ticket->ticket_number }}</p>
                                <p class="text-sm text-secondary">{{ $ticket->subject }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-500/20 text-purple-400">
                                {{ $ticket->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-secondary">No recent tickets</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
