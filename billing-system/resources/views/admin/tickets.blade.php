@extends('layouts.admin')

@section('title', 'Tickets')

@section('header', 'Support Tickets Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 accent-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Total Tickets</p>
                    <p class="text-2xl font-bold accent-text">1,234</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 success-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Resolved</p>
                    <p class="text-2xl font-bold accent-text">892</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 warning-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">In Progress</p>
                    <p class="text-2xl font-bold accent-text">234</p>
                </div>
            </div>
        </div>

        <div class="glass-effect rounded-2xl p-6 border border-custom">
            <div class="flex items-center">
                <div class="w-12 h-12 error-bg rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-secondary text-sm">Open</p>
                    <p class="text-2xl font-bold accent-text">108</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="glass-effect rounded-2xl border border-custom shadow-glow">
        <div class="px-6 py-5 border-b border-custom flex items-center justify-between">
            <h3 class="text-xl font-semibold accent-text">Support Tickets</h3>
            <div class="flex space-x-3">
                <button class="bg-card/50 border border-custom text-secondary hover:text-white px-4 py-2 rounded-lg transition-all duration-200">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"/>
                    </svg>
                    Filter
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-custom">
                <thead class="bg-card/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Ticket #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Subject</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-secondary uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="accent-text hover:opacity-80">#TCK-001</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">Login Issue</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">John Doe</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-500 text-white">High</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium error-bg text-white">Open</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="#" class="accent-text hover:opacity-80">View</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
