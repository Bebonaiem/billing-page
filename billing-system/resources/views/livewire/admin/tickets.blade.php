@extends('layouts.admin')

@section('header', 'Support Tickets')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search tickets..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            
            <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="all">All Status</option>
                <option value="open">Open</option>
                <option value="answered">Answered</option>
                <option value="customer_reply">Customer Reply</option>
                <option value="closed">Closed</option>
            </select>
            
            <select wire:model="departmentId" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Departments</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                @endforeach
            </select>
        </div>
        
        <div class="text-sm text-gray-600 dark:text-gray-400">
            <span class="mr-4">Open: {{ $tickets->where('status', 'open')->count() }}</span>
            <span>Answered: {{ $tickets->where('status', 'answered')->count() }}</span>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ticket #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subject</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Last Reply</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($tickets as $ticket)
                    <tr class="{{ in_array($ticket->status, ['open', 'customer_reply']) ? 'bg-yellow-50 dark:bg-yellow-900/10' : '' }}">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">
                            {{ $ticket->ticket_number }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-800 dark:text-white">
                            {{ \Illuminate\Support\Str::limit($ticket->subject, 40) }}
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800 dark:text-white">{{ $ticket->user->getFullName() }}</p>
                            <p class="text-xs text-gray-500">{{ $ticket->user->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $ticket->department?->name ?? 'General' }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($ticket->status === 'open') bg-red-100 text-red-800
                                @elseif($ticket->status === 'answered') bg-blue-100 text-blue-800
                                @elseif($ticket->status === 'customer_reply') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($ticket->priority === 'critical') bg-red-100 text-red-800
                                @elseif($ticket->priority === 'high') bg-orange-100 text-orange-800
                                @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($ticket->priority) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $ticket->last_reply_at?->diffForHumans() ?? $ticket->created_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button wire:click="viewTicket({{ $ticket->id }})" class="text-blue-600 hover:text-blue-800">
                                {{ $ticket->status === 'closed' ? 'View' : 'Reply' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No tickets found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $tickets->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal && $editingTicket)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full" style="max-height: 90vh; overflow-y: auto;">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <!-- Ticket Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $editingTicket->subject }}</h3>
                                <p class="text-sm text-gray-500">{{ $editingTicket->ticket_number }}</p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full
                                    @if($editingTicket->status === 'open') bg-red-100 text-red-800
                                    @elseif($editingTicket->status === 'answered') bg-blue-100 text-blue-800
                                    @elseif($editingTicket->status === 'customer_reply') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $editingTicket->status)) }}
                                </span>
                                @if($editingTicket->assigned)
                                    <span class="text-xs text-gray-500">Assigned to: {{ $editingTicket->assigned->getFullName() }}</span>
                                @else
                                    <button wire:click="assignToMe" class="text-xs text-blue-600 hover:text-blue-800">Assign to me</button>
                                @endif
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded mb-4">
                            <div class="flex justify-between">
                                <div>
                                    <p class="text-sm font-medium">{{ $editingTicket->user->getFullName() }}</p>
                                    <p class="text-xs text-gray-500">{{ $editingTicket->user->email }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-gray-500">Department: {{ $editingTicket->department?->name ?? 'General' }}</p>
                                    <p class="text-xs text-gray-500">Priority: {{ ucfirst($editingTicket->priority) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Replies -->
                        <div class="space-y-4 max-h-96 overflow-y-auto mb-4">
                            @foreach($editingTicket->replies as $reply)
                                <div class="{{ $reply->is_internal_note ? 'bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400' : 'bg-gray-50 dark:bg-gray-700' }} p-4 rounded">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center">
                                            <span class="font-medium text-sm">{{ $reply->user->getFullName() }}</span>
                                            @if($reply->is_staff_reply)
                                                <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">Staff</span>
                                            @endif
                                            @if($reply->is_internal_note)
                                                <span class="ml-2 px-2 py-0.5 text-xs bg-yellow-100 text-yellow-800 rounded">Internal</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $reply->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $reply->message }}</p>
                                </div>
                            @endforeach
                        </div>

                        @if($editingTicket->replies->flatMap->attachments->count() > 0)
                            <div class="border-t dark:border-gray-700 pt-4 mb-4">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Attachments</h4>
                                <div class="space-y-1">
                                    @foreach($editingTicket->replies as $reply)
                                        @foreach($reply->attachments as $attachment)
                                            <a href="{{ $attachment->getUrl() }}" target="_blank" class="block text-sm text-blue-600 hover:text-blue-800">
                                                {{ $attachment->original_filename }}
                                            </a>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Reply Form -->
                        @if($editingTicket->status !== 'closed')
                            <div class="border-t dark:border-gray-700 pt-4">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Post Reply</h4>
                                <textarea wire:model="replyMessage" rows="4" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" placeholder="Type your reply..."></textarea>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reply Attachments</label>
                                    <input type="file" wire:model="replyAttachments" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-white hover:file:bg-blue-700">
                                </div>
                                
                                <div class="flex justify-between items-center mt-3">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="isInternalNote" class="rounded border-gray-300 text-blue-600 shadow-sm">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Internal note (customer won't see)</span>
                                    </label>
                                    
                                    <div class="flex space-x-2">
                                        <button wire:click="postReply" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Post Reply</button>
                                        <button wire:click="closeTicket" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Close Ticket</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6">
                        <button wire:click="closeModal" type="button" class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
