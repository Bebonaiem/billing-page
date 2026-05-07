@extends('layouts.client')

@section('title', 'Support Tickets')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Support Tickets</h1>
        <button wire:click="$set('showCreateModal', true)" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Create Ticket</button>
    </div>

    <!-- Filters -->
    <div class="flex space-x-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search tickets..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        
        <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="all">All Status</option>
            <option value="open">Open</option>
            <option value="answered">Answered</option>
            <option value="customer_reply">Awaiting Reply</option>
            <option value="closed">Closed</option>
        </select>
    </div>

    <!-- Tickets List -->
    <div class="space-y-4">
        @forelse($tickets as $ticket)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 {{ in_array($ticket->status, ['open', 'customer_reply']) ? 'border-l-4 border-blue-500' : '' }}">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center space-x-2 mb-1">
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $ticket->subject }}</h3>
                            <span class="text-sm text-gray-500">{{ $ticket->ticket_number }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-2">{{ $ticket->department?->name ?? 'General Support' }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($ticket->status === 'open') bg-red-100 text-red-800
                            @elseif($ticket->status === 'answered') bg-blue-100 text-blue-800
                            @elseif($ticket->status === 'customer_reply') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($ticket->priority === 'high' || $ticket->priority === 'critical') bg-red-100 text-red-800
                            @elseif($ticket->priority === 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800 @endif">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mt-4 pt-4 border-t dark:border-gray-700">
                    <div class="text-sm text-gray-500">
                        Created {{ $ticket->created_at->format('M d, Y') }}
                        @if($ticket->last_reply_at)
                            · Last reply {{ $ticket->last_reply_at->diffForHumans() }}
                        @endif
                    </div>
                    <button wire:click="viewTicket({{ $ticket->id }})" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm">
                        {{ $ticket->status === 'closed' ? 'View' : 'Reply' }}
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-lg shadow">
                <p class="text-gray-500 dark:text-gray-400 mb-4">You don't have any support tickets.</p>
                <button wire:click="$set('showCreateModal', true)" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Create Your First Ticket</button>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $tickets->links() }}
    </div>

    <!-- Create Ticket Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Create Support Ticket</h3>
                        
                        <form wire:submit.prevent="createTicket" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                                <select wire:model="departmentId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                @error('departmentId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Related Service (Optional)</label>
                                <select wire:model="serviceId" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                    <option value="">None</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name ?? $service->product->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
                                <select wire:model="priority" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                    <option value="critical">Critical</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                                <input type="text" wire:model="subject" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                @error('subject') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Message</label>
                                <textarea wire:model="message" rows="4" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm"></textarea>
                                @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Attachments</label>
                                <input type="file" wire:model="attachments" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-white hover:file:bg-blue-700">
                                <p class="mt-1 text-xs text-gray-500">Up to 10MB each.</p>
                            </div>
                        </form>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex flex-row-reverse">
                        <button wire:click="createTicket" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Create Ticket
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- View/Reply Modal -->
    @if($showViewModal && $viewingTicket)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full" style="max-height: 90vh; overflow-y: auto;">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <!-- Ticket Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $viewingTicket->subject }}</h3>
                                <p class="text-sm text-gray-500">{{ $viewingTicket->ticket_number }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($viewingTicket->status === 'open') bg-red-100 text-red-800
                                @elseif($viewingTicket->status === 'answered') bg-blue-100 text-blue-800
                                @elseif($viewingTicket->status === 'customer_reply') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $viewingTicket->status)) }}
                            </span>
                        </div>

                        <!-- Replies -->
                        <div class="space-y-4 max-h-96 overflow-y-auto mb-4">
                            @foreach($viewingTicket->replies as $reply)
                                <div class="{{ $reply->is_staff_reply ? 'bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-400' : 'bg-gray-50 dark:bg-gray-700' }} p-4 rounded">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center">
                                            <span class="font-medium text-sm">{{ $reply->user->getFullName() }}</span>
                                            @if($reply->is_staff_reply)
                                                <span class="ml-2 px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded">Staff</span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-gray-500">{{ $reply->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $reply->message }}</p>

                                    @if($reply->attachments->count() > 0)
                                        <div class="mt-3 space-y-1">
                                            @foreach($reply->attachments as $attachment)
                                                <a href="{{ $attachment->getUrl() }}" target="_blank" class="block text-sm text-blue-600 hover:text-blue-800">
                                                    {{ $attachment->original_filename }}
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Reply Form -->
                        @if($viewingTicket->status !== 'closed')
                            <div class="border-t dark:border-gray-700 pt-4">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Post Reply</h4>
                                <textarea wire:model="replyMessage" rows="3" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm" placeholder="Type your reply..."></textarea>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reply Attachments</label>
                                    <input type="file" wire:model="replyAttachments" multiple class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-white hover:file:bg-blue-700">
                                </div>
                                <div class="mt-3 flex justify-end">
                                    <button wire:click="postReply" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Post Reply</button>
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
