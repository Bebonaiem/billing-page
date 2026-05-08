{{-- Data Table Component --}}
@props([
    'data',
    'columns',
    'actions' => [],
    'searchable' => false,
    'sortable' => false,
    'pagination' => null,
    'loading' => false
])

<div class="glass-effect-3d rounded-2xl overflow-hidden">
    <!-- Table Header -->
    @if($searchable || $actions)
        <div class="p-4 border-b border-custom flex items-center justify-between">
            @if($searchable)
                <div class="relative flex-1 max-w-md">
                    <input 
                        type="text" 
                        placeholder="Search..."
                        class="w-full pl-10 pr-4 py-2 bg-card/50 border border-custom rounded-lg text-white placeholder-secondary focus:outline-none focus:border-accent transition-colors duration-200"
                        wire:model.live="search"
                    >
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            @endif
            
            @if($actions)
                <div class="flex items-center gap-2">
                    @foreach($actions as $action)
                        <button 
                            {{ $action['attributes'] ?? [] }}
                            class="px-3 py-2 bg-card/50 border border-custom rounded-lg text-white hover:bg-card/70 hover-lift transition-all duration-200 text-sm"
                        >
                            @if(isset($action['icon']))
                                <svg class="w-4 h-4 {{ isset($action['text']) ? 'inline mr-2' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"></path>
                                </svg>
                            @endif
                            {{ $action['text'] ?? '' }}
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Loading State -->
    @if($loading)
        <div class="p-8">
            <x-loading-spinner size="lg" text="Loading data..." />
        </div>
    @else
        <!-- Table Content -->
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full">
                <thead class="bg-card/30 border-b border-custom">
                    <tr>
                        @foreach($columns as $key => $column)
                            <th class="px-6 py-4 text-left text-xs font-medium text-secondary uppercase tracking-wider">
                                @if($sortable && isset($column['sortable']) && $column['sortable'])
                                    <button 
                                        wire:click="sortBy('{{ $key }}')"
                                        class="flex items-center gap-1 hover:text-accent transition-colors duration-200"
                                    >
                                        {{ $column['label'] }}
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                                        </svg>
                                    </button>
                                @else
                                    {{ $column['label'] }}
                                @endif
                            </th>
                        @endforeach
                        @if(!empty($actions))
                            <th class="px-6 py-4 text-right text-xs font-medium text-secondary uppercase tracking-wider">
                                Actions
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-custom">
                    @forelse($data as $item)
                        <tr class="hover:bg-card/20 transition-colors duration-200">
                            @foreach($columns as $key => $column)
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if(isset($column['component']))
                                        <div>
                                            {{ $column['component']($item) }}
                                        </div>
                                    @elseif(isset($column['format']))
                                        <div class="{{ $column['class'] ?? 'text-white' }}">
                                            {{ $column['format']($item->{$key}) }}
                                        </div>
                                    @else
                                        <div class="text-white">
                                            {{ $item->{$key} }}
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            
                            @if(!empty($actions))
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @foreach($actions as $action)
                                            @if(isset($action['show']) && !$action['show']($item))
                                                @continue
                                            @endif
                                            
                                            <button 
                                                wire:click="{{ $action['action'] }}({{ $item->id }})"
                                                class="p-2 {{ $action['class'] ?? 'bg-card/50 hover:bg-card/70' }} rounded-lg transition-all duration-200 group"
                                                title="{{ $action['title'] ?? '' }}"
                                            >
                                                <svg class="w-4 h-4 {{ $action['textClass'] ?? 'text-secondary group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}"></path>
                                                </svg>
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + (empty($actions) ? 0 : 1) }}" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-card/50 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <p class="text-secondary">No data available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pagination)
            <div class="p-4 border-t border-custom flex items-center justify-between">
                <div class="text-sm text-secondary">
                    Showing 
                    <span class="font-medium text-white">{{ $pagination->firstItem() }}</span> 
                    to 
                    <span class="font-medium text-white">{{ $pagination->lastItem() }}</span> 
                    of 
                    <span class="font-medium text-white">{{ $pagination->total() }}</span> 
                    results
                </div>
                
                <div class="flex items-center gap-2">
                    <button 
                        wire:click="previousPage"
                        wire:disabled="{{ $pagination->onFirstPage() }}"
                        class="p-2 bg-card/50 border border-custom rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-card/70 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <span class="px-3 py-1 bg-card/50 border border-custom rounded-lg text-sm text-white">
                        {{ $pagination->currentPage() }}
                    </span>
                    
                    <button 
                        wire:click="nextPage"
                        wire:disabled="{{ $pagination->hasMorePages() }}"
                        class="p-2 bg-card/50 border border-custom rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-card/70 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    @endif
</div>