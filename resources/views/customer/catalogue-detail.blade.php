<x-layout.customer>
    <div class="my-10 md:my-14">
        <div class="flex flex-col md:grid md:grid-cols-12 gap-4">
            <div class="flex flex-wrap justify-center items-center col-span-5">
                <x-image.image-selector :images="['/assets/images/image-2.png', '/assets/images/image-1.png', '/assets/images/image-3.png']" />
            </div>

            <div class="flex flex-wrap justify-center col-span-4">
                <div class="flex-1 font-poppins">
                    <h2 class="text-2xl md:text-4xl font-extrabold text-primary">Nama Makanan</h2>
                    <p class="mt-2 md:mt-4 text-lg md:text-xl font-semibold text-primary">Rp123.000</p>

                    <div class="mt-4 flex gap-2" x-data="{ activeSize: '30ML' }">
                        <button @click="activeSize = '30ML'"
                            class="px-2 md:px-4 py-1 md:py-2 border rounded-md text-sm md:text-lg transition"
                            :class="activeSize === '30ML' ? 'bg-primary text-white border-primary' :
                                'border-primary text-primary'">
                            30 ML
                        </button>
                        <button @click="activeSize = '60ML'"
                            class="px-2 md:px-4 py-1 md:py-2 border rounded-md text-sm md:text-lg transition"
                            :class="activeSize === '60ML' ? 'bg-primary text-white border-primary' :
                                'border-primary text-primary'">
                            60 ML
                        </button>
                        <button @click="activeSize = '90ML'"
                            class="px-2 md:px-4 py-1 md:py-2 border rounded-md text-sm md:text-lg transition"
                            :class="activeSize === '90ML' ? 'bg-primary text-white border-primary' :
                                'border-primary text-primary'">
                            90 ML
                        </button>
                    </div>

                    <p class="mt-2 md:mt-4 text-sm md:text-lg leading-relaxed">
                        Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
                    </p>
                </div>
            </div>
            <div class="col-span-3">
                <div x-data="{ quantity: 1, stock: 16, price: 123000 }" class="p-4 bg-transparent md:bg-white rounded-lg md:rounded-xl shadow-md font-poppins">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center border border-gray-300 rounded-md">
                            <button @click="if (quantity > 1) quantity--"
                                class="px-2 md:px-3 py-1 text-gray-700 border-r border-gray-300">−</button>
                            <span class="px-3 md:px-4 py-1 text-sm md:text-lg font-semibold" x-text="quantity"></span>
                            <button @click="if (quantity < stock) quantity++"
                                class="px-2 md:px-3 py-1 text-primary border-l border-primary-gray">+</button>
                        </div>
                        <p class="text-gray-700 text-sm md:text-lg">Stok Total: <span class="font-bold" x-text="stock"></span></p>
                    </div>

                    <div class="mt-3">
                        <p class="text-gray-500 text-md md:text-lg">Subtotal</p>
                        <p class="font-bold text-gray-900 text-lg md:text-xl" x-text="`Rp${(quantity * price).toLocaleString()}`">
                        </p>
                    </div>

                    <div class="mt-4 flex flex-col gap-2">
                        <button
                            class="px-4 py-2 border rounded-md text-primary text-center flex items-center gap-2 text-sm md:text-md">
                            Tambah Keranjang 🛒
                        </button>
                        <button
                            class="px-4 py-2 bg-primary text-white rounded-md text-center flex items-center gap-2 text-sm md:text-md">
                            Beli Sekarang 🛒
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-10 mt-36 md:mt-0">
        <h2 class="text-xl md:text-3xl font-bold text-center text-primary font-poppins mb-4 md:mb-8">Recommended For You
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-x-4 md:gap-x-14 gap-y-4 md:gap-y-0">

            <x-card.product />
            <x-card.product />
            <x-card.product />
            <x-card.product />

        </div>
    </div>
</x-layout.customer>
