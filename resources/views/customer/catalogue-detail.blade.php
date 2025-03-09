<x-layout.customer>
    <div class="my-10 md:my-14">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6" x-data="{
            selectedVariant: {{ json_encode($product['product_variants'][0]) }},
            quantity: 1,
            updateVariant(variant) {
                this.selectedVariant = variant;
                this.quantity = 1;
                console.log('Selected Variant:', this.selectedVariant);
            },
            get subtotal() {
                return this.quantity * this.selectedVariant.price;
            }
        }" x-init="console.log('Initial Selected Variant:', selectedVariant)">

            <div class="flex justify-center items-start col-span-5">
                <x-image.image-selector :images="json_decode($product['product_variants'][0]->photo)" />
            </div>

            <div class="col-span-4">
                <div class="font-poppins">
                    <h2 class="text-2xl md:text-4xl font-extrabold text-primary">{{ $product->name }}</h2>
                    <p class="mt-2 md:mt-4 text-lg md:text-xl font-semibold text-primary"
                        x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(selectedVariant.price)">
                    </p>


                    <div class="mt-4 flex gap-2 flex-wrap">
                        @foreach ($product['product_variants'] as $product_variant)
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
                        <div class="flex items-center border border-gray-300 rounded-md">
                            <button @click="if (quantity > 1) quantity--"
                                class="px-3 py-1 text-gray-700 border-r border-gray-300">−</button>
                            <span class="px-4 py-1 text-lg font-semibold" x-text="quantity"></span>
                            <button @click="if (quantity < selectedVariant.stock) quantity++"
                                class="px-3 py-1 text-primary border-l border-primary-gray">+</button>
                        </div>
                        <p class="text-gray-700 text-lg">Stok: <span class="font-bold"
                                x-text="selectedVariant.stock"></span></p>
                    </div>

                    <div class="mt-3">
                        <p class="text-gray-500 text-md md:text-lg">Subtotal</p>
                        <p class="font-bold text-gray-900 text-lg md:text-xl" x-text="'Rp' + subtotal.toLocaleString()">
                        </p>
                    </div>

                    <div class="mt-4 flex flex-col gap-2">
                        <button class="px-4 py-2 border rounded-md text-primary text-center text-md">
                            Tambah Keranjang
                        </button>
                        <button class="px-4 py-2 bg-primary text-white rounded-md text-center text-md">
                            Beli Sekarang
                        </button>
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
            <x-card.product />
            <x-card.product />
            <x-card.product />
            <x-card.product />
        </div>
    </div>
</x-layout.customer>
