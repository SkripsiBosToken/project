@props([
    'label' => '',
    'value' => '0',
    'icon' => 'fa-chart-simple',
    'tone' => 'primary',
    'hint' => null,
])

@php
    $tones = [
        'primary' => 'bg-primary-50 text-primary',
        'success' => 'bg-success-50 text-success-600',
        'warning' => 'bg-warning-50 text-warning-600',
        'info' => 'bg-info-50 text-info-600',
    ];
@endphp

<div
    {{ $attributes->merge([
        'class' => 'rounded-xl border border-gray-200 bg-white p-5 shadow-card transition-shadow hover:shadow-card-hover',
    ]) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="truncate text-sm text-gray-500">{{ $label }}</p>
            <p class="mt-1 text-2xl font-bold tabular-nums text-gray-900">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 text-xs text-gray-400">{{ $hint }}</p>
            @endif
        </div>
        <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg {{ $tones[$tone] ?? $tones['primary'] }}">
            <i class="fa-solid {{ $icon }}"></i>
        </div>
    </div>
</div>
