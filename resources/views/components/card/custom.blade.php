@props([
    'href' => '#',
    'defcard' => 'flex flex-col items-center justify-center font-poppins bg-white rounded-xl shadow-md p-4',
])

<div {{ $attributes->merge(['class' => $defcard]) }}>
    {{$slot}}
</div>
