<div>
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-white mb-4">Admin Dashboard</h1>
        <p class="text-secondary">Welcome back! Here's your system overview.</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-card rounded-lg p-6 border border-custom">
            <h3 class="text-lg font-semibold text-white mb-2">Total Revenue</h3>
            <p class="text-3xl font-bold text-green-400">${{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
        
        <div class="bg-card rounded-lg p-6 border border-custom">
            <h3 class="text-lg font-semibold text-white mb-2">Active Services</h3>
            <p class="text-3xl font-bold text-blue-400">{{ $stats['active_services'] }}</p>
        </div>
        
        <div class="bg-card rounded-lg p-6 border border-custom">
            <h3 class="text-lg font-semibold text-white mb-2">Pending Orders</h3>
            <p class="text-3xl font-bold text-yellow-400">{{ $stats['pending_orders'] }}</p>
        </div>
        
        <div class="bg-card rounded-lg p-6 border border-custom">
            <h3 class="text-lg font-semibold text-white mb-2">Open Tickets</h3>
            <p class="text-3xl font-bold text-purple-400">{{ $stats['open_tickets'] }}</p>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-card rounded-lg p-6 border border-custom">
            <h3 class="text-lg font-semibold text-white mb-4">Recent Orders</h3>
            <div class="space-y-3">
                @forelse($recent_orders as $order)
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
                @forelse($recent_tickets as $ticket)
                    <div class="flex items-center justify-between p-3 bg-card/50 rounded-lg">
                        <div>
                            <p class="text-white font-medium">#{{ $ticket->ticket_number }}</p>
                            <p class="text-sm text-secondary">{{ Str::limit($ticket->subject, 40) }}</p>
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
