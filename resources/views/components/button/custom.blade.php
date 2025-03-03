@props([
    'href' => '#',
    'defbutton' => 'px-4 py-2 bg-primary text-white rounded-lg hover:bg-white transition duration-300 inline-block',
])

<a href="{{$href}}" {{ $attributes->merge(['class' => $defbutton]) }}>
    {{ $slot->isNotEmpty() ? $slot : 'Button' }}
</a>