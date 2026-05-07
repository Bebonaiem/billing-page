@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="mb-6">
            <a href="{{ route('client.tickets') }}" class="text-blue-600 hover:text-blue-800">&larr; Back to Tickets</a>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900">{{ $ticket->subject }}</h1>
                        <p class="mt-1 text-sm text-gray-500">
                            Ticket #{{ $ticket->id }} • Opened {{ $ticket->created_at->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $ticket->priority === 'urgent' ? 'red' : ($ticket->priority === 'high' ? 'orange' : ($ticket->priority === 'medium' ? 'yellow' : 'green')) }}-100 text-{{ $ticket->priority === 'urgent' ? 'red' : ($ticket->priority === 'high' ? 'orange' : ($ticket->priority === 'medium' ? 'yellow' : 'green')) }}-800">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $ticket->status === 'open' ? 'green' : ($ticket->status === 'answered' ? 'blue' : 'gray') }}-100 text-{{ $ticket->status === 'open' ? 'green' : ($ticket->status === 'answered' ? 'blue' : 'gray') }}-800">
                            {{ ucfirst($ticket->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <!-- Ticket Details -->
                <div class="mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-gray-700">Department</p>
                            <p class="text-gray-900">{{ ucfirst($ticket->department) }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Last Updated</p>
                            <p class="text-gray-900">{{ $ticket->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Assigned To</p>
                            <p class="text-gray-900">{{ $ticket->assigned_to ? $ticket->assigned_to->name : 'Unassigned' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="space-y-6">
                    @foreach ($ticket->replies as $reply)
                        <div class="flex {{ $reply->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-2xl {{ $reply->user_id === Auth::id() ? 'bg-blue-100' : 'bg-gray-100' } rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $reply->user_id === Auth::id() ? 'You' : $reply->user->name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $reply->created_at->format('M d, Y g:i A') }}
                                    </p>
                                </div>
                                <p class="text-gray-700">{{ $reply->message }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Reply Form -->
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add Reply</h3>
                    <form method="POST" action="{{ route('client.tickets.reply', $ticket) }}">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700">Your Message</label>
                                <textarea name="message" id="message" rows="4" required
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                          placeholder="Type your reply here..."></textarea>
                                @error('message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Send Reply
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
