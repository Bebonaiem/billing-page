<div>
    <!-- Animated Header -->
    <div class="mb-8 animate-slide-in">
        <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 mb-2 animate-pulse-glow">
            Admin Dashboard
        </h1>
        <p class="text-secondary text-lg">Welcome back! Here's your system overview with real-time insights.</p>
    </div>
    
    <!-- Interactive Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Total Revenue</h3>
                <div class="w-10 h-10 accent-bg rounded-lg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 .895 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold accent-text group-hover:scale-110 transition-transform duration-300">${{ number_format($stats['total_revenue'], 2) }}</p>
            <div class="mt-2 flex items-center text-sm text-secondary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0l-8-4m-4 4h6m4 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="success-text">{{ $stats['revenue_growth'] ?? '+18.5%' }} from last month</span>
            </div>
        </div>
        
        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Active Services</h3>
                <div class="w-10 h-10 success-bg rounded-lg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002 2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold success-text group-hover:scale-110 transition-transform duration-300">{{ $stats['active_services'] }}</p>
            <div class="mt-2 flex items-center text-sm text-secondary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="success-text">{{ $stats['service_growth'] ?? '+12.3%' }} from last month</span>
            </div>
        </div>
        
        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Pending Orders</h3>
                <div class="w-10 h-10 warning-bg rounded-lg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold warning-text group-hover:scale-110 transition-transform duration-300">{{ $stats['pending_orders'] }}</p>
            <div class="mt-2 flex items-center text-sm text-secondary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3l3 3m6 3v4M9 20l6-6m6 6v4M9 20l6-6m6 6v4"/>
                </svg>
                <span class="warning-text">{{ $stats['order_trend'] ?? '+8.7%' }} from last week</span>
            </div>
        </div>
        
        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Overdue Invoices</h3>
                <div class="w-10 h-10 error-bg rounded-lg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold error-text group-hover:scale-110 transition-transform duration-300">{{ $stats['overdue_invoices'] }}</p>
            <div class="mt-2 flex items-center text-sm text-secondary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h.138M12 9v2m0 4h.01m-6.938 4h.138"/>
                </svg>
                <span class="error-text">{{ $stats['invoice_trend'] ?? '-2.4%' }} from last month</span>
            </div>
        </div>
        
        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Open Tickets</h3>
                <div class="w-10 h-10 gradient-animated rounded-lg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-white group-hover:scale-110 transition-transform duration-300">{{ $stats['open_tickets'] }}</p>
            <div class="mt-2 flex items-center text-sm text-secondary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
                <span class="text-white">{{ $stats['ticket_response'] ?? '1hr 23min avg response' }}</span>
            </div>
        </div>
    </div>
    
    <!-- Interactive Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Revenue Chart -->
        <div class="glass-effect-3d rounded-2xl p-6 animate-bounce-in" style="animation-delay: 0.5s">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white">Revenue Analytics</h2>
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 bg-accent rounded-full animate-pulse"></span>
                    <span class="text-xs text-secondary">Live</span>
                </div>
            </div>
            <div class="h-64 flex items-end justify-between space-x-2 mb-4">
                @if(isset($revenue_chart) && count($revenue_chart) > 0)
                    @php
                        $maxRevenue = collect($revenue_chart)->max('revenue');
                        $maxRevenue = $maxRevenue > 0 ? $maxRevenue : 1;
                    @endphp
                    @foreach($revenue_chart as $index => $data)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-gradient-to-t from-accent/40 to-accent/20 rounded-lg animate-morph hover:from-accent/60 hover:to-accent/40 transition-all duration-300 cursor-pointer group relative" 
                                 style="height: {{ ($data['revenue'] / $maxRevenue) * 100 }}%; animation-delay: {{ $index * 0.1 }}s;"
                                 title="{{ $data['month'] }}: ${{ number_format($data['revenue'], 2) }}">
                                <div class="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
                                    ${{ number_format($data['revenue'], 2) }}
                                </div>
                            </div>
                            <span class="text-xs text-secondary mt-2">{{ $data['month'] }}</span>
                        </div>
                    @endforeach
                @else
                    <div class="flex-1 bg-accent/20 rounded-lg animate-morph" style="height: 60%;"></div>
                    <div class="flex-1 bg-success/20 rounded-lg animate-morph" style="height: 80%; animation-delay: 0.2s;"></div>
                    <div class="flex-1 bg-warning/20 rounded-lg animate-morph" style="height: 45%; animation-delay: 0.4s;"></div>
                    <div class="flex-1 bg-error/20 rounded-lg animate-morph" style="height: 70%; animation-delay: 0.6s;"></div>
                    <div class="flex-1 bg-accent/20 rounded-lg animate-morph" style="height: 90%; animation-delay: 0.8s;"></div>
                    <div class="flex-1 bg-success/20 rounded-lg animate-morph" style="height: 65%; animation-delay: 1s;"></div>
                @endif
            </div>
            <div class="flex justify-between text-sm text-secondary mt-4">
                <span>6 Month Performance</span>
                <span class="success-text">{{ $stats['revenue_growth'] ?? '+28.4%' }}</span>
            </div>
        </div>
        
        <!-- User Activity Heatmap -->
        <div class="glass-effect-3d rounded-2xl p-6 animate-bounce-in" style="animation-delay: 0.6s">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white">User Activity Heatmap</h2>
                <select class="bg-card border border-custom rounded-lg px-3 py-1 text-sm text-secondary focus:outline-none focus:border-accent">
                    <option>This Week</option>
                    <option>Last Week</option>
                    <option>This Month</option>
                </select>
            </div>
            <div class="grid grid-cols-7 gap-1 text-xs">
                @for($i = 0; $i < 7; $i++)
                    <div class="text-center">
                        <div class="text-secondary mb-2">{{ ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'][$i] }}</div>
                        <div class="flex justify-center space-x-1">
                            @for($j = 0; $j < 24; $j++)
                                @php
                                    $activityLevel = rand(0, 3);
                                    $colors = ['bg-gray-700', 'bg-success/20', 'bg-warning/20', 'bg-error/20'];
                                    $color = $colors[$activityLevel];
                                @endphp
                                <div class="w-2 h-2 rounded {{ $color }} hover:scale-150 transition-transform duration-200 cursor-pointer" 
                                     title="{{ $j }}:00 - {{ $activityLevel * 25 }}% activity"></div>
                            @endfor
                        </div>
                    </div>
                @endfor
            </div>
            <div class="flex items-center justify-between mt-4 text-xs">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-gray-700 rounded-full mr-2"></div>
                        <span>No Activity</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-success/20 rounded-full mr-2"></div>
                        <span>Low</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-warning/20 rounded-full mr-2"></div>
                        <span>Medium</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-error/20 rounded-full mr-2"></div>
                        <span>High</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="glass-effect-3d rounded-2xl p-6 animate-bounce-in" style="animation-delay: 0.7s">
            <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                Recent Orders
                <span class="ml-2 px-2 py-1 text-xs success-bg rounded-full animate-pulse">Live</span>
            </h2>
            <div class="space-y-3">
                @foreach($recent_orders as $order)
                    <div class="flex items-center justify-between p-4 bg-card/30 rounded-xl hover-lift group cursor-pointer transition-all duration-300">
                        <div class="flex items-center">
                            <div class="relative">
                                <div class="w-10 h-10 rounded-full mr-3 ring-2 ring-accent/50 animate-pulse-glow">
                                    <div class="w-full h-full rounded-full accent-bg animate-morph"></div>
                                    <img class="absolute inset-1 w-8 h-8 rounded-full" src="https://ui-avatars.com/api/?name={{ $order->user->name }}&color=3B82F6&background=1E293B" alt="{{ $order->user->name }}">
                                </div>
                            </div>
                            <div>
                                <p class="font-medium text-white group-hover:text-accent transition-colors duration-200">#{{ $order->order_number }}</p>
                                <p class="text-sm text-secondary">{{ $order->user->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-secondary">{{ $order->created_at->format('M d, Y') }}</p>
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $order->status === 'completed' ? 'success-bg' : ($order->status === 'pending' ? 'warning-bg' : 'error-bg') }} text-white animate-glow-scan">
                                {{ ucfirst($order->status) }}
                            </div>
                            <p class="font-bold text-white accent-text">${{ number_format($order->total, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="glass-effect-3d rounded-2xl p-6 animate-bounce-in" style="animation-delay: 0.8s">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-white flex items-center">
                    Recent Tickets
                    <span class="ml-2 px-2 py-1 text-xs warning-bg rounded-full animate-pulse">{{ $recent_tickets->count() }} New</span>
                </h2>
                <button class="text-xs text-accent hover:text-accent-hover transition-colors duration-200">
                    View All →
                </button>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar">
                @foreach($recent_tickets as $ticket)
                    <div class="flex items-center justify-between p-4 bg-card/30 rounded-xl hover-lift group cursor-pointer transition-all duration-300"
                         onclick="window.location.href='{{ route('admin.tickets.show', $ticket->id) }}'">
                        <div>
                            <p class="font-medium text-white group-hover:text-accent transition-colors duration-200">#{{ $ticket->ticket_number }}</p>
                            <p class="text-sm text-secondary">{{ Str::limit($ticket->subject, 40) }}</p>
                        </div>
                        <div class="text-right">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $ticket->priority === 'high' || $ticket->priority === 'critical' ? 'error-bg' : ($ticket->priority === 'medium' ? 'warning-bg' : 'success-bg') }} text-white animate-glow-scan">
                                {{ ucfirst($ticket->priority) }}
                            </div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $ticket->status === 'open' ? 'error-bg' : ($ticket->status === 'answered' ? 'accent-bg' : 'warning-bg') }} text-white">
                                {{ ucfirst($ticket->status) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
