@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconRight' => null,
    'block' => false,
    'loading' => false,
    'disabled' => false,
])

@php
    $variants = [
        'primary' => 'bg-primary text-white hover:bg-primary-900 focus-visible:outline-primary',
        'secondary' => 'bg-primary-secondary text-white hover:bg-primary-950 focus-visible:outline-primary-secondary',
        'outline' => 'border border-primary text-primary bg-transparent hover:bg-primary hover:text-white focus-visible:outline-primary',
        'ghost' => 'text-primary bg-transparent hover:bg-primary-50 focus-visible:outline-primary',
        'danger' => 'bg-primary-danger text-white hover:bg-red-800 focus-visible:outline-primary-danger',
        'muted' => 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus-visible:outline-gray-400',
        'success' => 'bg-success-600 text-white hover:bg-success-700 focus-visible:outline-success-600',
    ];

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'md' => 'px-4 py-2.5 text-sm gap-2',
        'lg' => 'px-6 py-3 text-base gap-2',
    ];

    $base = 'inline-flex items-center justify-center rounded-lg font-semibold transition-colors duration-200
             focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
             disabled:cursor-not-allowed disabled:opacity-50';

    $classes = trim(
        $base . ' ' .
        ($variants[$variant] ?? $variants['primary']) . ' ' .
        ($sizes[$size] ?? $sizes['md']) . ' ' .
        ($block ? 'w-full' : '')
    );

    $isDisabled = $disabled || $loading;
@endphp

@if ($href && ! $isDisabled)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if ($icon)
            <i class="fa-solid {{ $icon }}"></i>
        @endif
        {{ $slot }}
        @if ($iconRight)
            <i class="fa-solid {{ $iconRight }}"></i>
        @endif
    </a>
@else
    <button type="{{ $type }}" @disabled($isDisabled) {{ $attributes->merge(['class' => $classes]) }}>
        @if ($loading)
            <i class="fa-solid fa-circle-notch fa-spin"></i>
        @elseif ($icon)
            <i class="fa-solid {{ $icon }}"></i>
        @endif
        {{ $slot }}
        @if ($iconRight && ! $loading)
            <i class="fa-solid {{ $iconRight }}"></i>
        @endif
    </button>
@endif
