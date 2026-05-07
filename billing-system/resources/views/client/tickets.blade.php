@extends('layouts.client')

@section('title', 'Support Tickets')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-blue-400 to-purple-400">
                Support Tickets
            </h1>
            <p class="text-secondary text-lg">Track and manage your support requests</p>
        </div>
        <a href="{{ route('client.tickets.create') }}" class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            New Ticket
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Total Tickets</p>
                    <p class="text-2xl font-bold accent-text">24</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 success-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Resolved</p>
                    <p class="text-2xl font-bold accent-text">18</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 warning-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">In Progress</p>
                    <p class="text-2xl font-bold accent-text">4</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 error-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Open</p>
                    <p class="text-2xl font-bold accent-text">2</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets List -->
    <div class="glass-effect rounded-2xl border border-custom shadow-glow">
        <div class="px-6 py-5 border-b border-custom">
            <h3 class="text-xl font-semibold accent-text">Recent Tickets</h3>
        </div>
        <div class="divide-y divide-custom">
            <!-- Ticket 1 -->
            <div class="px-6 py-4 hover:bg-card/30 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <a href="#" class="text-lg font-medium accent-text hover:opacity-80 mr-3">#TCK-001</a>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium error-bg text-white">Open</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white ml-2">High</span>
                        </div>
                        <h4 class="text-white font-medium mb-1">Unable to access my account</h4>
                        <p class="text-secondary text-sm mb-2">I'm having trouble logging into my account after the recent update...</p>
                        <div class="flex items-center text-sm text-secondary">
                            <span class="mr-4">Created: Jan 25, 2024</span>
                            <span class="mr-4">Department: Technical</span>
                            <span>Last reply: 2 hours ago</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="#" class="accent-bg-hover text-white px-4 py-2 rounded-lg transition-all duration-200">
                            View
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ticket 2 -->
            <div class="px-6 py-4 hover:bg-card/30 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <a href="#" class="text-lg font-medium accent-text hover:opacity-80 mr-3">#TCK-002</a>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium warning-bg text-white">In Progress</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-500 text-white ml-2">Medium</span>
                        </div>
                        <h4 class="text-white font-medium mb-1">Billing inquiry</h4>
                        <p class="text-secondary text-sm mb-2">I have a question about my recent invoice and the charges...</p>
                        <div class="flex items-center text-sm text-secondary">
                            <span class="mr-4">Created: Jan 24, 2024</span>
                            <span class="mr-4">Department: Billing</span>
                            <span>Last reply: 5 hours ago</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="#" class="accent-bg-hover text-white px-4 py-2 rounded-lg transition-all duration-200">
                            View
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ticket 3 -->
            <div class="px-6 py-4 hover:bg-card/30 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <a href="#" class="text-lg font-medium accent-text hover:opacity-80 mr-3">#TCK-003</a>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium accent-bg text-white">Answered</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500 text-white ml-2">Low</span>
                        </div>
                        <h4 class="text-white font-medium mb-1">Service upgrade question</h4>
                        <p class="text-secondary text-sm mb-2">I'd like to upgrade my hosting plan to the premium tier...</p>
                        <div class="flex items-center text-sm text-secondary">
                            <span class="mr-4">Created: Jan 23, 2024</span>
                            <span class="mr-4">Department: Sales</span>
                            <span>Last reply: 1 day ago</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="#" class="accent-bg-hover text-white px-4 py-2 rounded-lg transition-all duration-200">
                            View
                        </a>
                    </div>
                </div>
            </div>

            <!-- Ticket 4 -->
            <div class="px-6 py-4 hover:bg-card/30 transition-colors duration-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <a href="#" class="text-lg font-medium accent-text hover:opacity-80 mr-3">#TCK-004</a>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium success-bg text-white">Resolved</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500 text-white ml-2">Low</span>
                        </div>
                        <h4 class="text-white font-medium mb-1">Password reset request</h4>
                        <p class="text-secondary text-sm mb-2">I need help resetting my password for the client area...</p>
                        <div class="flex items-center text-sm text-secondary">
                            <span class="mr-4">Created: Jan 22, 2024</span>
                            <span class="mr-4">Department: Technical</span>
                            <span>Resolved: Jan 22, 2024</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <a href="#" class="accent-bg-hover text-white px-4 py-2 rounded-lg transition-all duration-200">
                            View
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State (if no tickets) -->
    <div class="glass-effect rounded-2xl border border-custom p-12 text-center hidden">
        <div class="w-20 h-20 accent-bg rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
            </svg>
        </div>
        <h3 class="text-xl font-semibold accent-text mb-2">No support tickets yet</h3>
        <p class="text-secondary mb-6">Create your first support ticket to get help from our team.</p>
        <a href="{{ route('client.tickets.create') }}" class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
            Create Ticket
        </a>
    </div>
</div>
@endsection
