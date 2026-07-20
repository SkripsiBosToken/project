@props([
    'name',
    'label' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'hint' => null,
    'required' => false,
])

@php
    $current = old($name, $selected);
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

    <select name="{{ $name }}" id="{{ $name }}" @required($required)
        {{ $attributes->merge([
            'class' => 'w-full rounded-lg border bg-white px-3 py-2.5 text-sm transition
                focus:outline-none focus:ring-2 focus:ring-primary/30 '
                . ($hasError ? 'border-primary-danger focus:border-primary-danger' : 'border-gray-300 focus:border-primary'),
        ]) }}>

        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $text)
            {{-- Mendukung list biasa (nilai = label) maupun map value => label. --}}
            @php $optionValue = is_int($value) ? $text : $value; @endphp
            <option value="{{ $optionValue }}" @selected((string) $current === (string) $optionValue)>{{ $text }}</option>
        @endforeach
    </select>

    @if ($hasError)
        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-primary-danger">
            <i class="fa-solid fa-circle-exclamation"></i>{{ $errors->first($name) }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-gray-500">{{ $hint }}</p>
    @endif
</div>
