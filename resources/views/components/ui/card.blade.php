@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
    'padding' => 'p-5',
    'hover' => false,
])

<div
    {{ $attributes->merge([
        'class' => 'rounded-xl border border-gray-200 bg-white shadow-card '
            . ($hover ? 'transition-shadow duration-200 hover:shadow-card-hover' : ''),
    ]) }}>

    @if ($title || isset($header))
        <div class="flex items-start justify-between gap-3 border-b border-gray-100 px-5 py-4">
            <div class="min-w-0">
                @if ($title)
                    <h2 class="flex items-center gap-2 text-base font-bold text-gray-900">
                        @if ($icon)
                            <i class="fa-solid {{ $icon }} text-primary"></i>
                        @endif
                        {{ $title }}
                    </h2>
                @endif
                @if ($subtitle)
                    <p class="mt-0.5 text-sm text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
            @isset($header)
                <div class="flex-shrink-0">{{ $header }}</div>
            @endisset
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="border-t border-gray-100 px-5 py-4">{{ $footer }}</div>
    @endisset
</div>
