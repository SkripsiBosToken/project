@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'icon' => null,
    'required' => false,
])

@php
    // old() menjaga isian pengguna tetap ada setelah validasi gagal.
    $current = old($name, $value);
    $hasError = $errors->has($name);
@endphp

<div class="w-full">
    @if ($label)
        <label for="{{ $name }}" class="mb-1.5 block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-primary-danger">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if ($icon)
            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                <i class="fa-solid {{ $icon }} text-sm"></i>
            </span>
        @endif

        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ $current }}"
            @required($required)
            @if ($hasError) aria-invalid="true" aria-describedby="{{ $name }}-error" @endif
            {{ $attributes->merge([
                'class' => 'w-full rounded-lg border bg-white py-2.5 text-sm transition
                    placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30
                    disabled:cursor-not-allowed disabled:bg-gray-50 '
                    . ($icon ? 'pl-10 pr-3 ' : 'px-3 ')
                    . ($hasError ? 'border-primary-danger focus:border-primary-danger' : 'border-gray-300 focus:border-primary'),
            ]) }}>
    </div>

    @if ($hasError)
        <p id="{{ $name }}-error" class="mt-1.5 flex items-center gap-1.5 text-xs text-primary-danger">
            <i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first($name) }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-gray-500">{{ $hint }}</p>
    @endif
</div>
