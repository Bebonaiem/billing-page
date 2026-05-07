@extends('layouts.admin')

@section('header', 'Extensions')

@section('content')
<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex space-x-4">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search extensions..." class="px-4 py-2 border rounded-lg dark:border-gray-600 dark:bg-gray-700 dark:text-white">
        </div>
        
        <button wire:click="showAvailableExtensions" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
            + Install Extension
        </button>
    </div>

    <!-- Extensions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($extensions as $extension)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">{{ $extension->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $extension->slug }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full
                        @if($extension->is_active) bg-green-100 text-green-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $extension->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $extension->description ?: 'No description' }}</p>
                
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                    <div class="flex justify-between">
                        <span>Version:</span>
                        <span>{{ $extension->version }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Author:</span>
                        <span>{{ $extension->author }}</span>
                    </div>
                        <div class="flex justify-between">
                            <span>Installed:</span>
                            <span>{{ $extension->installed_at?->format('M d, Y') ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Dependencies:</span>
                            <span>{{ count($extension->dependencies ?? []) }}</span>
                        </div>
                </div>

                <div class="flex space-x-2">
                    <button wire:click="toggleExtension({{ $extension->id }})" class="flex-1 {{ $extension->is_active ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white py-2 rounded-lg text-sm">
                        {{ $extension->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                    
                    @if(!empty($extension->settings))
                        <button wire:click="editSettings({{ $extension->id }})" class="flex-1 bg-gray-600 text-white py-2 rounded-lg hover:bg-gray-700 text-sm">Settings</button>
                    @endif
                    
                    <button wire:click="uninstallExtension({{ $extension->id }})" wire:confirm="Are you sure you want to uninstall this extension?" class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 text-sm">Uninstall</button>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No extensions installed.</p>
                <button wire:click="showAvailableExtensions" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">Browse Extensions</button>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $extensions->links() }}
    </div>

    <!-- Install Modal -->
    @if($showInstallModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Available Extensions</h3>
                        
                        @if(count($availableExtensions) > 0)
                            <div class="space-y-4">
                                @foreach($availableExtensions as $ext)
                                    <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div>
                                            <h4 class="font-medium text-gray-800 dark:text-white">{{ $ext['name'] }}</h4>
                                            <p class="text-sm text-gray-500">{{ $ext['description'] ?? 'No description' }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded">v{{ $ext['version'] ?? '1.0.0' }}</span>
                                                <span class="text-xs text-gray-500">by {{ $ext['author'] ?? 'Unknown' }}</span>
                                            </div>
                                        </div>
                                        <button 
                                            wire:click="installExtension('{{ $ext['key'] }}')" 
                                            wire:loading.attr="disabled"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
                                        >
                                            {{ $installingExtension === $ext['key'] ? 'Installing...' : 'Install' }}
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No extensions available to install.</p>
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

    <!-- Settings Modal -->
    @if($editingExtension)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ $editingExtension->name }} Settings</h3>
                        
                        <div class="space-y-4">
                            @foreach($extensionSettings as $key => $value)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $key) }}</label>
                                    <input type="text" wire:model="extensionSettings.{{ $key }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm">
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 flex flex-row-reverse">
                        <button wire:click="saveSettings" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Save Settings
                        </button>
                        <button wire:click="closeModal" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection
