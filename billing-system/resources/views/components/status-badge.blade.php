{{-- Status Badge Component --}}
@props([
    'status',
    'size' => 'md',
    'animated' => false
])

@php
    $statusConfig = [
        'active' => ['bg' => 'success-bg', 'text' => 'text-white', 'icon' => 'M5 13l4 4L19 7'],
        'pending' => ['bg' => 'warning-bg', 'text' => 'text-white', 'icon' => 'M12 8v4l3 3m6-3l3 3m6 3v4M9 20l6-6m6 6v4M9 20l6-6m6 6v4'],
        'suspended' => ['bg' => 'warning-bg', 'text' => 'text-white', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 0018.364 5.636m-9 9h6m-6 0v6'],
        'cancelled' => ['bg' => 'error-bg', 'text' => 'text-white', 'icon' => 'M6 18L18 6M6 6l12 12'],
        'completed' => ['bg' => 'success-bg', 'text' => 'text-white', 'icon' => 'M5 13l4 4L19 7'],
        'unpaid' => ['bg' => 'warning-bg', 'text' => 'text-white', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        'paid' => ['bg' => 'success-bg', 'text' => 'text-white', 'icon' => 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z'],
        'open' => ['bg' => 'accent-bg', 'text' => 'text-white', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
        'answered' => ['bg' => 'info-bg', 'text' => 'text-white', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
        'customer_reply' => ['bg' => 'warning-bg', 'text' => 'text-white', 'icon' => 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z'],
        'closed' => ['bg' => 'error-bg', 'text' => 'text-white', 'icon' => 'M6 18L18 6M6 6l12 12'],
        'banned' => ['bg' => 'error-bg', 'text' => 'text-white', 'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 0018.364 5.636m-9 9h6m-6 0v6'],
    ];

    $config = $statusConfig[strtolower($status)] ?? $statusConfig['pending'];
    
    $sizeClasses = [
        'sm' => 'px-2 py-1 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-2 text-base'
    ];
@endphp

<div class="inline-flex items-center gap-2 {{ $config['bg'] }} {{ $config['text'] }} {{ $sizeClasses[$size] }} rounded-full font-medium {{ $animated ? 'animate-pulse' : '' }} status-badge">
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}"></path>
    </svg>
    <span>{{ ucfirst($status) }}</span>
</div>