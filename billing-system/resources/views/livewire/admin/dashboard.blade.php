<div>
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">${{ number_format($stats['total_revenue'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Services</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats['active_services'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Overdue Invoices</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats['overdue_invoices'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Open Tickets</p>
                    <p class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $stats['open_tickets'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Orders</h3>
            </div>
            <div class="p-6">
                @if($recent_orders->count() > 0)
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_orders as $order)
                                <tr>
                                    <td class="py-2 text-sm text-gray-800 dark:text-white">{{ $order->order_number }}</td>
                                    <td class="py-2 text-sm text-gray-600 dark:text-gray-300">{{ $order->user->getFullName() }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($order->status === 'active') bg-green-100 text-green-800
                                            @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2 text-sm text-gray-800 dark:text-white">${{ number_format($order->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No recent orders</p>
                @endif
            </div>
        </div>

        <!-- Open Tickets -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Recent Tickets</h3>
            </div>
            <div class="p-6">
                @if($recent_tickets->count() > 0)
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Ticket #</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_tickets as $ticket)
                                <tr>
                                    <td class="py-2 text-sm text-gray-800 dark:text-white">{{ $ticket->ticket_number }}</td>
                                    <td class="py-2 text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($ticket->subject, 30) }}</td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($ticket->status === 'open') bg-red-100 text-red-800
                                            @elseif($ticket->status === 'answered') bg-blue-100 text-blue-800
                                            @else bg-yellow-100 text-yellow-800 @endif">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="py-2">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($ticket->priority === 'high' || $ticket->priority === 'critical') bg-red-100 text-red-800
                                            @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800 @endif">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 dark:text-gray-400">No recent tickets</p>
                @endif
            </div>
        </div>
    </div>
</div>
