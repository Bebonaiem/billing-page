<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">My Services</h1>
        <a href="{{ route('order') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Order New Service</a>
    </div>

    <!-- Filters -->
    <div class="flex space-x-4">
        <input type="text" wire:model.debounce.300ms="search" placeholder="Search services..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        
        <select wire:model="status" class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="pending">Pending</option>
            <option value="suspended">Suspended</option>
        </select>
    </div>

    <!-- Services Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($services as $service)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $service->name ?? $service->product->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $service->product->name }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($service->status === 'active') bg-green-100 text-green-800
                        @elseif($service->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($service->status === 'suspended') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($service->status) }}
                    </span>
                </div>
                
                <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <div class="flex justify-between">
                        <span>Price:</span>
                        <span class="font-medium">${{ number_format($service->price, 2) }} / {{ $service->billing_cycle }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Next Invoice:</span>
                        <span class="font-medium">{{ $service->next_invoice_date?->format('M d, Y') ?? 'N/A' }}</span>
                    </div>
                    @if($service->panel_server_id)
                        <div class="flex justify-between">
                            <span>Panel:</span>
                            <span class="font-medium">{{ ucfirst($service->panel_type) }}</span>
                        </div>
                    @endif
                </div>

                @if($service->cancellation_requested)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3 mb-4">
                        <p class="text-sm text-yellow-700">
                            Cancellation requested. Service will terminate on {{ $service->cancellation_date?->format('M d, Y') }}.
                        </p>
                    </div>
                @endif
                
                <div class="flex space-x-2">
                    <button wire:click="viewService({{ $service->id }})" class="flex-1 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 text-sm">Manage</button>
                    
                    @if($service->status === 'active' && !$service->cancellation_requested)
                        <button wire:click="requestCancellation({{ $service->id }})" wire:confirm="Are you sure you want to cancel this service?" class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 text-sm">Cancel</button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 mb-4">You don't have any services yet.</p>
                <a href="{{ route('order') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Browse Products</a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $services->links() }}
    </div>

    <!-- Service Details Modal -->
    @if($showModal && $viewingService)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ $viewingService->name ?? $viewingService->product->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $viewingService->product->name }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full
                                @if($viewingService->status === 'active') bg-green-100 text-green-800
                                @elseif($viewingService->status === 'suspended') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($viewingService->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                <p class="text-xs text-gray-500">Price</p>
                                <p class="font-medium">${{ number_format($viewingService->price, 2) }} / {{ $viewingService->billing_cycle }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                <p class="text-xs text-gray-500">Next Invoice</p>
                                <p class="font-medium">{{ $viewingService->next_invoice_date?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                <p class="text-xs text-gray-500">Activated</p>
                                <p class="font-medium">{{ $viewingService->activated_at?->format('M d, Y') ?? 'Pending' }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                <p class="text-xs text-gray-500">Panel</p>
                                <p class="font-medium">{{ ucfirst($viewingService->panel_type) }}</p>
                            </div>
                        </div>

                        @if(!empty($serverStatus))
                            <div class="border-t dark:border-gray-700 pt-4 mb-4">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Server Status</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                        <p class="text-xs text-gray-500">Current State</p>
                                        <p class="font-medium">{{ ucfirst($serverStatus['state'] ?? 'Unknown') }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                        <p class="text-xs text-gray-500">Suspended</p>
                                        <p class="font-medium">{{ ($serverStatus['is_suspended'] ?? false) ? 'Yes' : 'No' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($viewingService->panel_server_id)
                            <div class="border-t dark:border-gray-700 pt-4">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">Panel Access</h4>
                                <a href="https://panel.example.com/server/{{ $viewingService->panel_server_id }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                                    Open Game Panel
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                </a>
                            </div>
                        @endif

                        @if($viewingService->cancellation_requested)
                            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mt-4">
                                <p class="text-sm text-yellow-700">
                                    <strong>Cancellation Scheduled:</strong> This service will be terminated on {{ $viewingService->cancellation_date?->format('M d, Y') }}.
                                </p>
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex justify-end">
                        <button wire:click="closeModal" type="button" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
