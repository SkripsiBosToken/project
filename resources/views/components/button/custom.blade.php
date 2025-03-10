@props([
    'href' => '#',
    'type' => 'button', // Ubah default type menjadi button
    'defbutton' => 'px-4 py-2 bg-primary text-white rounded-lg hover:bg-white transition duration-300 inline-block'
])

@if ($type === 'submit')
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $defbutton]) }}>
        {{ $slot->isNotEmpty() ? $slot : 'Button' }}
    </button>
@else
    <a href="{{ $href }}" type="{{ $type }}" {{ $attributes->merge(['class' => $defbutton]) }}>
        {{ $slot->isNotEmpty() ? $slot : 'Button' }}
    </a>
@endif