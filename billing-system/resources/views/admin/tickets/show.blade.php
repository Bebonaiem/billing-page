@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900/20 to-blue-900/20 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Ticket Details</h1>
                    <p class="text-secondary">Manage and respond to support tickets</p>
                </div>
                <a href="{{ route('admin.tickets.index') }}" class="px-4 py-2 bg-card border border-custom rounded-lg text-white hover:bg-card/80 transition-colors duration-200">
                    ← Back to Tickets
                </a>
            </div>
        </div>

        <!-- Ticket Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Ticket Info -->
                <div class="glass-effect rounded-2xl p-6 border border-custom">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-white mb-2">#{{ $ticket->ticket_number }}</h2>
                            <h3 class="text-lg text-white">{{ $ticket->subject }}</h3>
                        </div>
                        <div class="flex flex-col gap-2">
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $ticket->priority === 'high' ? 'error-bg' : ($ticket->priority === 'medium' ? 'warning-bg' : 'success-bg') }} text-white">
                                {{ ucfirst($ticket->priority) }} Priority
                            </div>
                            <div class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $ticket->status === 'open' ? 'error-bg' : ($ticket->status === 'answered' ? 'accent-bg' : 'warning-bg') }} text-white">
                                {{ ucfirst($ticket->status) }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-secondary">Department:</span>
                            <span class="text-white ml-2">{{ ucfirst($ticket->department) }}</span>
                        </div>
                        <div>
                            <span class="text-secondary">Created:</span>
                            <span class="text-white ml-2">{{ $ticket->created_at->format('M j, Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="glass-effect rounded-2xl p-6 border border-custom">
                    <h3 class="text-lg font-semibold text-white mb-4">Conversation</h3>
                    
                    <!-- Original Message -->
                    <div class="mb-6">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center">
                                <span class="text-white font-semibold">{{ substr($ticket->user->name, 0, 1) }}</span>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-medium text-white">{{ $ticket->user->name }}</span>
                                    <span class="text-xs text-secondary">{{ $ticket->created_at->format('M j, Y H:i') }}</span>
                                </div>
                                <div class="bg-card/50 rounded-lg p-4 text-white">
                                    {{ $ticket->message }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Admin Reply Form -->
                    <div class="border-t border-custom pt-6">
                        <h4 class="font-medium text-white mb-4">Reply to Ticket</h4>
                        <form class="space-y-4">
                            <div>
                                <textarea name="message" rows="4" 
                                          class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                          placeholder="Type your response..."></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="submit" class="px-6 py-2 accent-bg-hover text-white rounded-lg font-medium transition-all duration-200">
                                    Send Reply
                                </button>
                                <select name="status" class="px-4 py-2 bg-card border border-custom rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="open">Keep Open</option>
                                    <option value="answered">Mark as Answered</option>
                                    <option value="closed">Close Ticket</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Customer Info -->
                <div class="glass-effect rounded-2xl p-6 border border-custom">
                    <h3 class="text-lg font-semibold text-white mb-4">Customer Information</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-secondary text-sm">Name:</span>
                            <p class="text-white">{{ $ticket->user->name }}</p>
                        </div>
                        <div>
                            <span class="text-secondary text-sm">Email:</span>
                            <p class="text-white">{{ $ticket->user->email }}</p>
                        </div>
                        @if($ticket->user->phone)
                        <div>
                            <span class="text-secondary text-sm">Phone:</span>
                            <p class="text-white">{{ $ticket->user->phone }}</p>
                        </div>
                        @endif
                        @if($ticket->user->company)
                        <div>
                            <span class="text-secondary text-sm">Company:</span>
                            <p class="text-white">{{ $ticket->user->company }}</p>
                        </div>
                        @endif>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="glass-effect rounded-2xl p-6 border border-custom">
                    <h3 class="text-lg font-semibold text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <button class="w-full px-4 py-2 bg-card border border-custom rounded-lg text-white hover:bg-card/80 transition-colors duration-200">
                            View Customer Profile
                        </button>
                        <button class="w-full px-4 py-2 bg-card border border-custom rounded-lg text-white hover:bg-card/80 transition-colors duration-200">
                            View Customer Orders
                        </button>
                        <button class="w-full px-4 py-2 bg-card border border-custom rounded-lg text-white hover:bg-card/80 transition-colors duration-200">
                            Assign to Staff
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
