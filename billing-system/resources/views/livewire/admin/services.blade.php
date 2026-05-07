@extends('layouts.admin')

@section('header', 'Services')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search services..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            
            <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="pending">Pending</option>
                <option value="suspended">Suspended</option>
                <option value="terminated">Terminated</option>
                <option value="cancelled">Cancelled</option>
            </select>
            
            <select wire:model="userId" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                <option value="">All Customers</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->getFullName() }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Services Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Billing</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Next Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($services as $service)
                    <tr>
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $service->name ?? $service->product->name }}</p>
                                <p class="text-xs text-gray-500">{{ $service->panel_type }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-800 dark:text-white">{{ $service->user->getFullName() }}</p>
                            <p class="text-xs text-gray-500">{{ $service->user->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $service->product->name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            ${{ number_format($service->price, 2) }} / {{ $service->billing_cycle }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($service->status === 'active') bg-green-100 text-green-800
                                @elseif($service->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($service->status === 'suspended') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($service->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $service->next_invoice_date?->format('M d, Y') ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <button wire:click="viewService({{ $service->id }})" class="text-blue-600 hover:text-blue-800 mr-2">View</button>

                            @if($service->panel_type === 'pterodactyl' && !$service->panel_server_id)
                                <button wire:click="confirmAction({{ $service->id }}, 'provision')" class="text-indigo-600 hover:text-indigo-800 mr-2">Provision</button>
                            @endif

                            @if($service->panel_type === 'pterodactyl' && $service->panel_server_id)
                                <button wire:click="confirmAction({{ $service->id }}, 'reinstall')" class="text-purple-600 hover:text-purple-800 mr-2">Reinstall</button>
                            @endif
                            
                            @if($service->status === 'active')
                                <button wire:click="confirmAction({{ $service->id }}, 'suspend')" class="text-yellow-600 hover:text-yellow-800 mr-2">Suspend</button>
                            @endif
                            
                            @if($service->status === 'suspended')
                                <button wire:click="confirmAction({{ $service->id }}, 'unsuspend')" class="text-green-600 hover:text-green-800 mr-2">Unsuspend</button>
                            @endif
                            
                            @if(in_array($service->status, ['active', 'suspended']))
                                <button wire:click="confirmAction({{ $service->id }}, 'terminate')" class="text-red-600 hover:text-red-800">Terminate</button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No services found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $services->links() }}
        </div>
    </div>

    <!-- Modal -->
    @if($showModal && $editingService)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        @if($modalAction === 'view')
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Service Details</h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-500">Service Name</p>
                                    <p class="font-medium">{{ $editingService->name ?? $editingService->product->name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status</p>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        @if($editingService->status === 'active') bg-green-100 text-green-800
                                        @elseif($editingService->status === 'suspended') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($editingService->status) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Customer</p>
                                    <p class="font-medium">{{ $editingService->user->getFullName() }}</p>
                                    <p class="text-sm text-gray-500">{{ $editingService->user->email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Panel Type</p>
                                    <p class="font-medium">{{ ucfirst($editingService->panel_type) }}</p>
                                    @if($editingService->panel_server_id)
                                        <p class="text-sm text-gray-500">Server ID: {{ $editingService->panel_server_id }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-500">Price</p>
                                    <p class="font-medium">${{ number_format($editingService->price, 2) }} / {{ $editingService->billing_cycle }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Next Invoice</p>
                                    <p class="font-medium">{{ $editingService->next_invoice_date?->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                            </div>

                            @if(!empty($serverStatus))
                                <div class="border-t dark:border-gray-700 pt-4 mt-4">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Server Status</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                            <p class="text-xs text-gray-500">Current State</p>
                                            <p class="font-medium">{{ $serverStatus['state'] ?? 'Unknown' }}</p>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                            <p class="text-xs text-gray-500">Is Suspended</p>
                                            <p class="font-medium">{{ ($serverStatus['is_suspended'] ?? false) ? 'Yes' : 'No' }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($editingService->panel_server_id)
                                <div class="mt-4">
                                    <a href="https://panel.example.com/server/{{ $editingService->panel_server_id }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                        Open in Pterodactyl Panel →
                                    </a>
                                </div>
                            @elseif($modalAction === 'view' && !empty($provisionPreview))
                                <div class="border-t dark:border-gray-700 pt-4 mt-4">
                                    <h4 class="font-medium text-gray-900 dark:text-white mb-2">Provisioning Preview</h4>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500">Egg ID</p>
                                            <p class="font-medium">{{ $provisionPreview['egg_id'] ?? 'Not configured' }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Memory</p>
                                            <p class="font-medium">{{ $provisionPreview['memory'] ?? 'N/A' }} MB</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">Disk</p>
                                            <p class="font-medium">{{ $provisionPreview['disk'] ?? 'N/A' }} MB</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500">CPU</p>
                                            <p class="font-medium">{{ $provisionPreview['cpu'] ?? 'N/A' }}%</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                {{ ucfirst($modalAction) }} Service
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Are you sure you want to {{ $modalAction }} service <strong>{{ $editingService->name ?? $editingService->product->name }}</strong>?
                            </p>
                            
                            @if($modalAction === 'terminate')
                                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-4">
                                    <p class="text-sm text-red-700">
                                        <strong>Warning:</strong> This will permanently delete the server and all associated data. This action cannot be undone.
                                    </p>
                                </div>
                            @elseif($modalAction === 'provision')
                                <div class="bg-indigo-50 border border-indigo-200 rounded-md p-4 mb-4">
                                    <p class="text-sm text-indigo-700">
                                        This will create a new Pterodactyl server using the product's integration settings.
                                    </p>
                                </div>
                            @elseif($modalAction === 'reinstall')
                                <div class="bg-purple-50 border border-purple-200 rounded-md p-4 mb-4">
                                    <p class="text-sm text-purple-700">
                                        This will trigger a server reinstall on the connected Pterodactyl panel.
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        @if($modalAction !== 'view')
                            <button wire:click="executeAction" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $modalAction === 'terminate' ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700' }} text-base font-medium text-white focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                {{ ucfirst($modalAction) }}
                            </button>
                        @endif
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
