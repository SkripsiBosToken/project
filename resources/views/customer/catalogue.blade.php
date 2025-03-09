<x-layout.customer>
    <div class="my-10 md:my-14">
        <div class="flex flex-col md:flex-row gap-6">
            <x-sidebar.customer />
            <main class="w-full md:w-3/4">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($products as $product)
                        @if ($product->product_variants->isNotEmpty())
                            @php
                                $prices = collect($product->product_variants)->pluck('price');
                                $minPrice = $prices->min();
                                $maxPrice = $prices->max();
                            @endphp
                            <x-card.product title="{{ $product->name }}" img="{{ $product->img }}"
                                description="{{ $product->product_variants[0]->description }}" :price="$prices->count() === 1 ? $minPrice : [$minPrice, $maxPrice]"
                                href="{{ route('catalogue-detail', ['id' => $product->id]) }}" />
                        @endif
                    @endforeach
                </div>
            </main>
        </div>
    </div>
</x-layout.customer>
