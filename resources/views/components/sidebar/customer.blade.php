@php
    $categories = app(\App\Http\Controllers\GuestController::class)->getCategories();
@endphp

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<aside class="w-full md:w-1/4 rounded-lg font-poppins">
    <h2 class="text-lg md:text-2xl font-bold text-primary">Products</h2>
    <div class="mt-4">
        <input type="text" placeholder="Search" class="w-full p-2 border rounded-lg focus:ring focus:ring-primary">
    </div>
    <h3 class="mt-6 text-sm md:text-lg font-semibold text-primary-gray">Jenis</h3>

    <div class="mt-2 flex flex-col gap-2 md:gap-8" x-data="{ active: null }">
        @foreach ($categories as $category)
            <button class="flex items-center gap-2 px-2 py-2 md:py-3 rounded-lg md:rounded-2xl text-primary"
                :class="active === '{{ $category->name }}' ? 'border border-primary font-bold text-lg' : 'text-primary-gray'"
                @click="active = {{ $category->name }}">
                <span>🍽️ {{ $category->name }}</span>
            </button>
        @endforeach
    </div>
</aside>
