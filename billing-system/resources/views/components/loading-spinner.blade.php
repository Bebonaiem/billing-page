{{-- Loading Spinner Component --}}
@props([
    'size' => 'md',
    'color' => 'accent',
    'text' => null
])

@php
    $sizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6', 
        'lg' => 'w-8 h-8',
        'xl' => 'w-12 h-12'
    ];

    $colorClasses = [
        'accent' => 'border-accent',
        'white' => 'border-white',
        'success' => 'border-success',
        'warning' => 'border-warning',
        'error' => 'border-error'
    ];
@endphp

<div class="flex flex-col items-center justify-center p-8">
    <div class="relative">
        <div class="{{ $sizeClasses[$size] }} {{ $colorClasses[$color] }} border-2 border-t-transparent rounded-full animate-spin"></div>
        <div class="absolute inset-0 {{ $sizeClasses[$size] }} {{ $colorClasses[$color] }} border-2 border-t-transparent rounded-full animate-spin" style="animation-delay: 0.15s; animation-direction: reverse;"></div>
    </div>
    @if($text)
        <p class="mt-4 text-sm {{ $color === 'white' ? 'text-white' : 'text-secondary' }}">{{ $text }}</p>
    @endif
</div>