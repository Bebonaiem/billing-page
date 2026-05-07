@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Back Navigation -->
        <div class="mb-6">
            <a href="{{ route('client.tickets') }}" class="accent-text hover:opacity-80 transition-opacity duration-200 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back to Tickets
            </a>
        </div>

        <!-- Ticket Header -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow mb-6">
            <div class="px-6 py-5 border-b border-custom">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-2">{{ $ticket->subject }}</h1>
                        <p class="text-secondary">
                            Ticket #{{ $ticket->id }} • Opened {{ $ticket->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $ticket->priority === 'urgent' ? 'error-bg' : ($ticket->priority === 'high' ? 'warning-bg' : ($ticket->priority === 'medium' ? 'bg-yellow-500' : 'success-bg')) }} text-white">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $ticket->status === 'open' ? 'success-bg' : ($ticket->status === 'answered' ? 'accent-bg' : 'bg-gray-500') }} text-white">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <!-- Ticket Details -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div>
                        <p class="text-sm font-medium text-secondary mb-1">Department</p>
                        <p class="text-lg">{{ ucfirst($ticket->department) }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-secondary mb-1">Last Updated</p>
                        <p class="text-lg">{{ $ticket->updated_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-secondary mb-1">Assigned To</p>
                        <p class="text-lg">{{ $ticket->assigned_to ? $ticket->assigned_to->name : 'Unassigned' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow mb-6">
            <div class="px-6 py-5 border-b border-custom">
                <h3 class="text-xl font-semibold accent-text">Conversation</h3>
            </div>
            <div class="p-6 space-y-6 max-h-96 overflow-y-auto">
                @foreach ($ticket->replies as $reply)
                    <div class="flex {{ $reply->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-2xl {{ $reply->user_id === Auth::id() ? 'accent-bg' : 'bg-card' } rounded-2xl px-4 py-3 {{ $reply->user_id === Auth::id() ? 'text-white' : '' }}">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium {{ $reply->user_id === Auth::id() ? 'text-blue-100' : 'text-white' }}">
                                    {{ $reply->user_id === Auth::id() ? 'You' : $reply->user->name }}
                                </p>
                                <p class="text-xs {{ $reply->user_id === Auth::id() ? 'text-blue-200' : 'text-tertiary' }}">
                                    {{ $reply->created_at->format('M d, Y g:i A') }}
                                </p>
                            </div>
                            <p class="{{ $reply->user_id === Auth::id() ? 'text-white' : 'text-secondary' }}">{{ $reply->message }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Reply Form -->
        <div class="glass-effect rounded-2xl border border-custom shadow-glow">
            <div class="px-6 py-5 border-b border-custom">
                <h3 class="text-xl font-semibold accent-text">Add Reply</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('client.tickets.reply', $ticket) }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label for="message" class="block text-sm font-medium text-secondary mb-2">Your Message</label>
                            <textarea name="message" id="message" rows="4" required
                                      class="w-full px-4 py-3 bg-card border border-custom rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                      placeholder="Type your reply here..."></textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="accent-bg-hover text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                Send Reply
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
