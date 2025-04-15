<x-layout.customer>
    <div class="my-10 md:my-14">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6" x-data="{
            selectedVariant: {{ json_encode($product['product_variants']->whereNull('deleted_at')->first()) }},
            quantity: 1,
            selectedImageIndex: 0,
            updateVariant(variant) {
                this.selectedVariant = variant;
                this.quantity = 1;
                this.selectedImageIndex = 0;
            },
            updateImageIndex(index) {
                this.selectedImageIndex = index;
            },
            get subtotal() {
                return this.quantity * this.selectedVariant.price;
            }
        }">

            <div class="flex justify-center items-start col-span-5">
                <div class="flex flex-col">
                    <div class="w-full">
                        <img :src="JSON.parse(selectedVariant.photo)[selectedImageIndex]" alt="Product Image"
                            class="w-full h-96 object-cover rounded-xl">
                    </div>
                    <div class="flex mt-2 space-x-2">
                        <template x-for="(image, index) in JSON.parse(selectedVariant.photo)">
                            <button @click="updateImageIndex(index)"
                                :class="{ 'opacity-50': selectedImageIndex !== index }"
                                class="w-16 h-16 rounded-md overflow-hidden">
                                <img :src="image" :alt="'Product Image ' + index"
                                    class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            <div class="col-span-4">
                <div class="font-poppins">
                    <h2 class="text-2xl md:text-4xl font-extrabold text-primary">{{ $product->name }}</h2>
                    <p class="mt-2 md:mt-4 text-lg md:text-xl font-semibold text-primary"
                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(selectedVariant.price)">
                    </p>

                    <div class="mt-4 flex gap-2 flex-wrap">
                        @foreach ($product['product_variants']->whereNull('deleted_at') as $product_variant)
                            <button @click="updateVariant({{ json_encode($product_variant) }})"
                                class="px-3 py-2 border rounded-md text-sm md:text-lg transition"
                                :class="selectedVariant.name_type === '{{ $product_variant->name_type }}' ?
                                    'bg-primary text-white border-primary' :
                                    'border-primary text-primary'">
                                {{ $product_variant->name_type }}
                            </button>
                        @endforeach
                    </div>

                    <p class="mt-4 text-sm md:text-lg leading-relaxed" x-text="selectedVariant.description"></p>
                </div>
            </div>
            <div class="col-span-3">
                <div class="p-4 bg-white rounded-xl shadow-md font-poppins">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center border border-primary-gray rounded-md">
                            <button @click="if (quantity > 1) quantity--"
                                class="px-3 py-1 text-primary border-r border-primary-gray">−</button>
                            <span class="px-4 py-1 text-lg font-semibold" x-text="quantity"></span>
                            <button @click="if (quantity < selectedVariant.stock) quantity++"
                                class="px-3 py-1 text-primary border-l border-primary-gray">+</button>
                        </div>
                        <p class="text-primary-gray text-lg">Stok: <span class="font-bold"
                                x-text="selectedVariant.stock"></span></p>
                    </div>

                    <div class="mt-3">
                        <p class="text-primary-gray text-md md:text-lg">Subtotal</p>
                        <p class="font-bold text-lg md:text-xl" x-text="'Rp' + subtotal.toLocaleString()">
                        </p>
                    </div>

                    <div class="mt-4 flex flex-col gap-2">
                        <x-form.custom action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_variant_id" :value="selectedVariant.id">
                            <input type="hidden" name="cart_id" :value="">
                            <input type="number" name="qty" :value="quantity" class="hidden">
                            <button type="submit"
                                class="w-full px-4 py-2 border rounded-md text-primary text-center text-md">
                                <i class="fa-solid fa-plus mr-2"></i> Tambah Keranjang
                            </button>
                        </x-form.custom>

                        <x-form.custom action="{{ route('checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="type" value="buy-directly">
                            <input type="hidden" name="product_variant_id" :value="selectedVariant.id">
                            <input type="number" name="qty" :value="quantity" class="hidden">
                            <button type="submit"
                                class="w-full px-4 py-2 bg-primary text-white rounded-md text-center text-md">
                                Beli Sekarang
                            </button>
                        </x-form.custom>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-10 mt-36 md:mt-0">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-8">
            Recommended For You
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-10">
            @foreach ($products as $product)
                @if ($product && $product->product_variants && $product->product_variants->whereNull('deleted_at')->isNotEmpty())
                    @php
                        $variants = $product->product_variants->whereNull('deleted_at')->values();
                        $prices = $variants->pluck('price');
                        $minPrice = $prices->min();
                        $maxPrice = $prices->max();
                    @endphp

                    <x-card.product title="{{ $product->name }}"
                        img="{{ json_decode($variants[0]->photo, true)[0] ?? '/placeholder.jpg' }}"
                        description="{{ $variants[0]->description }}" :price="$prices->count() === 1 ? $minPrice : [$minPrice, $maxPrice]"
                        href="{{ route('catalogue-detail', ['id' => $product->id, 'slug' => Str::slug($product->name)]) }}" />
                @endif
            @endforeach

        </div>
    </div>
</x-layout.customer>
