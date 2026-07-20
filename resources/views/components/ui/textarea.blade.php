@props([
    'name',
    'label' => null,
    'value' => null,
    'rows' => 4,
    'hint' => null,
    'required' => false,
])

@php
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

    <textarea name="{{ $name }}" id="{{ $name }}" rows="{{ $rows }}" @required($required)
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border bg-white px-3 py-2.5 text-sm transition
                placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-primary/30 '
                . ($hasError ? 'border-primary-danger focus:border-primary-danger' : 'border-gray-300 focus:border-primary'),
        ]) }}>{{ $current }}</textarea>

    @if ($hasError)
        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-primary-danger">
            <i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first($name) }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-gray-500">{{ $hint }}</p>
    @endif
</div>
