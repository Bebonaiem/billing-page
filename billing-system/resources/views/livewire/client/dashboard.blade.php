<div>
    <!-- Animated Header -->
    <div class="mb-8 animate-slide-in">
        <h1 class="text-4xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 mb-2">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="text-secondary text-lg">Here's your account overview and recent activity.</p>
    </div>

    <!-- Interactive Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer">
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
                <span class="success-text">{{ $stats['suspended_services'] ?? 0 }} suspended</span>
            </div>
        </div>

        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer" style="animation-delay: 0.1s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Unpaid Invoices</h3>
                <div class="w-10 h-10 warning-bg rounded-lg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold warning-text group-hover:scale-110 transition-transform duration-300">{{ $stats['unpaid_invoices'] }}</p>
            <div class="mt-2 flex items-center text-sm text-secondary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3l3 3m6 3v4M9 20l6-6m6 6v4M9 20l6-6m6 6v4"/>
                </svg>
                <span class="error-text">{{ $stats['overdue_invoices'] }} overdue</span>
            </div>
        </div>

        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer" style="animation-delay: 0.2s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Credit Balance</h3>
                <div class="w-10 h-10 accent-bg rounded-lg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 .895 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold accent-text group-hover:scale-110 transition-transform duration-300">${{ number_format($stats['credit_balance'], 2) }}</p>
            <div class="mt-2 flex items-center text-sm text-secondary">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0l-8-4m-4 4h6m4 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                <span class="accent-text">${{ number_format($stats['monthly_spending'], 2) }} this month</span>
            </div>
        </div>

        <div class="glass-effect-3d rounded-2xl p-6 hover-lift animate-bounce-in group cursor-pointer" style="animation-delay: 0.3s">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-white">Support Tickets</h3>
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
                <span class="text-white">{{ $stats['ticket_response'] ?? 'Fast response' }}</span>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Services Section -->
        <div class="lg:col-span-2 glass-effect-3d rounded-2xl p-6 animate-bounce-in" style="animation-delay: 0.4s">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-white">Your Services</h2>
                <a href="{{ route('client.services') }}" class="text-sm text-accent hover:text-accent-hover transition-colors duration-200">
                    View All →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($services as $service)
                    <div class="p-4 bg-card/30 rounded-xl hover-lift group cursor-pointer transition-all duration-300"
                         onclick="window.location.href='{{ route('client.services.show', $service->id) }}'">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-lg accent-bg flex items-center justify-center mr-4 group-hover:animate-pulse">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002 2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-white group-hover:text-accent transition-colors duration-200">
                                        {{ $service->product->name }}
                                    </h3>
                                    <p class="text-sm text-secondary">
                                        {{ $service->billing_cycle }} • ${{ number_format($service->price, 2) }}/{{ $service->billing_cycle }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    {{ $service->status === 'active' ? 'success-bg' : ($service->status === 'suspended' ? 'warning-bg' : 'error-bg') }} text-white">
                                    {{ ucfirst($service->status) }}
                                </div>
                                <p class="text-xs text-secondary mt-1">
                                    Due: {{ $service->next_due_date ? $service->next_due_date->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-card/50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                        </div>
                        <p class="text-secondary mb-4">No services yet</p>
                        <a href="{{ route('order') }}" class="inline-flex items-center px-4 py-2 accent-bg text-white rounded-lg hover-lift">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Order Service
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="glass-effect-3d rounded-2xl p-6 animate-bounce-in" style="animation-delay: 0.5s">
            <h2 class="text-xl font-bold text-white mb-6">Recent Activity</h2>
            <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                @forelse($recent_activity ?? [] as $activity)
                    <div class="flex items-start p-3 bg-card/30 rounded-lg hover-lift transition-all duration-300">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center mr-3 flex-shrink-0
                            {{ $activity['type'] === 'invoice' ? 'warning-bg' : ($activity['type'] === 'ticket' ? 'accent-bg' : 'success-bg') }}">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                @if($activity['type'] === 'invoice')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                                @elseif($activity['type'] === 'ticket')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002 2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                @endif
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-white truncate">{{ $activity['title'] }}</p>
                            <p class="text-xs text-secondary">{{ $activity['description'] }}</p>
                            <p class="text-xs text-tertiary mt-1">{{ $activity['created_at']->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-card/50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3l3 3m6 3v4M9 20l6-6m6 6v4M9 20l6-6m6 6v4"/>
                            </svg>
                        </div>
                        <p class="text-secondary text-sm">No recent activity</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-effect-3d rounded-2xl p-6 animate-bounce-in" style="animation-delay: 0.6s">
        <h2 class="text-xl font-bold text-white mb-6">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="{{ route('order') }}" class="p-4 bg-card/30 rounded-xl hover-lift group transition-all duration-300 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full accent-bg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-white group-hover:text-accent transition-colors duration-200">Order Service</p>
            </a>

            <a href="{{ route('client.invoices') }}" class="p-4 bg-card/30 rounded-xl hover-lift group transition-all duration-300 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full warning-bg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-white group-hover:text-warning transition-colors duration-200">Pay Invoices</p>
            </a>

            <a href="{{ route('client.tickets.create') }}" class="p-4 bg-card/30 rounded-xl hover-lift group transition-all duration-300 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full gradient-animated flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-white group-hover:text-accent transition-colors duration-200">Get Support</p>
            </a>

            <a href="{{ route('client.profile') }}" class="p-4 bg-card/30 rounded-xl hover-lift group transition-all duration-300 text-center">
                <div class="w-12 h-12 mx-auto mb-3 rounded-full success-bg flex items-center justify-center group-hover:animate-pulse">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-white group-hover:text-success transition-colors duration-200">Update Profile</p>
            </a>
        </div>
    </div>
</div>