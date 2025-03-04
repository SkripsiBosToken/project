@props([
    'name' => 'Dummy',
    'role' => 'Customer',
    'rate' => 5,
    'image' => '',
    'review' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
                            been the industry s standard dummy text ever since the 1500s, when an unknown printer took a
                            galley of type and scrambled it to make a type specimen book.',
])

<div class="bg-white shadow-lg rounded-lg p-6 font-poppins">
    <div class="flex items-center space-x-4">
        <div>
            <h3 class="text-sm md:text-lg font-bold">{{ $name }}</h3>
            <p class="text-primary-gray text-xs md:text-sm">{{ $role }}</p>
        </div>
        <div class="ml-auto text-yellow-400 flex space-x-1">
            @for ($i = 0; $i < $rate; $i++)
                <span>★</span>
            @endfor
        </div>
    </div>
    <p class="mt-4 line-clamp-3 text-sm md:text-lg">
        "{{ $review }}"
    </p>
</div>
