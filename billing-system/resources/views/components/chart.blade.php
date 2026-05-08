{{-- Chart Component --}}
@props([
    'type' => 'line',
    'data' => [],
    'options' => []
])

@php
    $chartId = 'chart_' . uniqid();
    
    $defaultOptions = [
        'responsive' => true,
        'maintainAspectRatio' => false,
        'plugins' => [
            'legend' => [
                'display' => true,
                'position' => 'top',
                'labels' => [
                    'color' => 'rgb(203, 213, 225)',
                    'font' => [
                        'size' => 12
                    ]
                ]
            ],
            'tooltip' => [
                'mode' => 'index',
                'intersect' => false,
                'backgroundColor' => 'rgba(13, 20, 36, 0.9)',
                'titleColor' => 'rgb(241, 245, 249)',
                'bodyColor' => 'rgb(203, 213, 225)',
                'borderColor' => 'rgb(14, 165, 233)',
                'borderWidth' => 1
            ]
        ],
        'scales' => [
            'x' => [
                'grid' => [
                    'color' => 'rgba(71, 85, 105, 0.2)',
                    'drawBorder' => false
                ],
                'ticks' => [
                    'color' => 'rgb(148, 163, 184)'
                ]
            ],
            'y' => [
                'grid' => [
                    'color' => 'rgba(71, 85, 105, 0.2)',
                    'drawBorder' => false
                ],
                'ticks' => [
                    'color' => 'rgb(148, 163, 184)',
                    'callback' => 'function(value) { return "$" + value.toLocaleString(); }'
                ]
            ]
        ]
    ];
    
    $finalOptions = array_merge_recursive($defaultOptions, $options);
@endphp

<div class="glass-effect-3d rounded-2xl p-6">
    @if(isset($options['title']))
        <h3 class="text-lg font-semibold text-white mb-4">{{ $options['title'] }}</h3>
    @endif
    
    <div class="relative" style="height: {{ $options['height'] ?? '300px' }}">
        <canvas id="{{ $chartId }}"></canvas>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $chartId }}');
    if (ctx) {
        new Chart(ctx, {
            type: '{{ $type }}',
            data: @json($data),
            options: @json($finalOptions)
        });
    }
});

// Helper function for deep merge
function array_merge_recursive() {
    const result = {};
    
    for (let i = 0; i < arguments.length; i++) {
        const obj = arguments[i];
        for (const key in obj) {
            if (obj.hasOwnProperty(key)) {
                if (typeof obj[key] === 'object' && !Array.isArray(obj[key])) {
                    result[key] = array_merge_recursive(result[key] || {}, obj[key]);
                } else {
                    result[key] = obj[key];
                }
            }
        }
    }
    
    return result;
}
</script>
@endpush