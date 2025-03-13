@props([
    'title' => 'Lorem Ipsum',
    'img' => '/assets/images/image-3.png',
    'description' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.',
    'price' => 123000,
    'href' => '#',
    'defcard' => 'border border-primary rounded-xl md:rounded-2xl p-2 md:p-4 shadow-md h-full font-poppins',
])

@php
    if (is_array($price)) {
        $minPrice = min($price);
        $maxPrice = max($price);
        $formattedPrice =
            $minPrice == $maxPrice
                ? 'Rp' . number_format($minPrice, 0, ',', '.')
                : 'Rp' . number_format($minPrice, 0, ',', '.') . ' - Rp' . number_format($maxPrice, 0, ',', '.');
    } else {
        $formattedPrice = 'Rp' . number_format($price, 0, ',', '.');
    }
@endphp

<div {{ $attributes->merge(['class' => $defcard]) }}>
    <h3 class="text-sm md:text-xl font-semibold text-primary text-center">{{ $title }}</h3>
    <div class="flex justify-center py-4">
        <img src="{{ $img }}" class="w-20 md:w-36 h-20 md:h-36 object-cover rounded-md">
    </div>
    <hr class="border-primary py-2">
    <p class="text-xs md:text-sm text-primary-gray line-clamp-2">
        {{ $description }}
    </p>
    <div class="flex justify-between items-center mt-4">
        <p class="text-xs md:text-sm font-bold text-primary">
            {{ $formattedPrice }}
        </p>
        <x-button.custom class="p-0.5 text-sm md:text-sm" href="{{ $href }}">X</x-button.custom>
    </div>
</div>