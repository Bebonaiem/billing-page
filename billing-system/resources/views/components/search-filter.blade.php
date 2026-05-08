{{-- Search Filter Component --}}
@props([
    'filters' => [],
    'searchable' => true,
    'placeholder' => 'Search...'
])

<div class="glass-effect-3d rounded-2xl p-6 mb-6">
    <div class="flex flex-col lg:flex-row gap-4">
        @if($searchable)
            <div class="flex-1">
                <div class="relative">
                    <input 
                        type="text" 
                        placeholder="{{ $placeholder }}"
                        class="w-full pl-10 pr-4 py-3 bg-card/50 border border-custom rounded-lg text-white placeholder-secondary focus:outline-none focus:border-accent transition-colors duration-200"
                        wire:model.live="search"
                    >
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        @endif
        
        @foreach($filters as $key => $filter)
            <div class="lg:w-48">
                @if($filter['type'] === 'select')
                    <select 
                        wire:model.live="{{ $key }}"
                        class="w-full px-4 py-3 bg-card/50 border border-custom rounded-lg text-white focus:outline-none focus:border-accent transition-colors duration-200"
                    >
                        <option value="">{{ $filter['placeholder'] ?? 'All' }}</option>
                        @foreach($filter['options'] as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                @elseif($filter['type'] === 'date')
                    <input 
                        type="date" 
                        wire:model.live="{{ $key }}"
                        class="w-full px-4 py-3 bg-card/50 border border-custom rounded-lg text-white focus:outline-none focus:border-accent transition-colors duration-200"
                    >
                @elseif($filter['type'] === 'daterange')
                    <div class="flex gap-2">
                        <input 
                            type="date" 
                            wire:model.live="{{ $key }}_start"
                            placeholder="From"
                            class="flex-1 px-3 py-3 bg-card/50 border border-custom rounded-lg text-white placeholder-secondary focus:outline-none focus:border-accent transition-colors duration-200"
                        >
                        <input 
                            type="date" 
                            wire:model.live="{{ $key }}_end"
                            placeholder="To"
                            class="flex-1 px-3 py-3 bg-card/50 border border-custom rounded-lg text-white placeholder-secondary focus:outline-none focus:border-accent transition-colors duration-200"
                        >
                    </div>
                @endif
            </div>
        @endforeach
        
        <div class="flex gap-2">
            @if($searchable || count($filters) > 0)
                <button 
                    wire:click="resetFilters"
                    class="px-4 py-3 bg-card/50 border border-custom rounded-lg text-white hover:bg-card/70 hover-lift transition-all duration-200"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset
                </button>
            @endif
            
            <button 
                wire:click="export"
                class="px-4 py-3 accent-bg text-white rounded-lg hover-lift transition-all duration-200"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
            </button>
        </div>
    </div>
</div>