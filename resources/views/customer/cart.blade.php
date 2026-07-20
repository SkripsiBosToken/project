<x-layout.customer title="Keranjang | Kusuka Catering" :noindex="true">

    <div class="my-8 md:my-12" x-data="cartPage({
        items: {{ Illuminate\Support\Js::from($cart['cart_items']) }},
    })">

        <h1 class="mb-6 text-2xl font-bold text-gray-900 md:text-3xl">Keranjang Belanja</h1>

        {{-- Keranjang kosong --}}
        <template x-if="!items.length">
            <div class="rounded-xl border border-gray-200 bg-white">
                <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-50">
                        <i class="fa-solid fa-cart-shopping text-2xl text-primary-300"></i>
                    </div>
                    <h2 class="mt-4 text-base font-semibold text-gray-900">Keranjang Anda masih kosong</h2>
                    <p class="mt-1.5 max-w-sm text-sm text-gray-500">
                        Yuk jelajahi menu kami dan temukan hidangan favorit Anda.
                    </p>
                    <a href="{{ route('catalogue') }}"
                        class="mt-5 inline-flex items-center gap-2 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-900">
                        Lihat Katalog <i class="fa-solid fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </template>

        <template x-if="items.length">
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Daftar item --}}
                <div class="space-y-3 lg:col-span-2">
                    <template x-for="item in items" :key="item.id">
                        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-card transition"
                            :class="isUnavailable(item) && 'opacity-60'">

                            <div class="flex gap-4">
                                <img :src="photoOf(item)" :alt="nameOf(item)"
                                    class="h-20 w-20 flex-shrink-0 rounded-lg border border-gray-100 object-cover"
                                    onerror="this.src='/placeholder.jpg'">

                                <div class="flex min-w-0 flex-1 flex-col">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <h3 class="truncate text-sm font-semibold text-gray-900 md:text-base"
                                                x-text="nameOf(item)"></h3>
                                            <p class="mt-0.5 text-sm text-primary"
                                                x-text="rupiah(item.product_variant.price)"></p>
                                        </div>

                                        <a :href="'/cart/delete/' + item.id"
                                            @click="return confirm('Hapus item ini dari keranjang?')"
                                            class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-primary-danger"
                                            aria-label="Hapus item">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </a>
                                    </div>

                                    <template x-if="item.product_variant.deleted_at">
                                        <p class="mt-1 text-xs font-medium text-primary-danger">
                                            <i class="fa-solid fa-circle-exclamation mr-1"></i>Produk sudah tidak tersedia
                                        </p>
                                    </template>

                                    <template x-if="!item.product_variant.deleted_at && outOfStock(item)">
                                        <p class="mt-1 text-xs font-medium text-primary-danger">
                                            <i class="fa-solid fa-circle-exclamation mr-1"></i>Stok habis
                                        </p>
                                    </template>

                                    <div class="mt-auto flex items-end justify-between gap-3 pt-3">
                                        <div class="flex items-center rounded-lg border border-gray-300">
                                            <button type="button" @click="decrease(item)"
                                                :disabled="isUnavailable(item) || item.qty <= 1"
                                                class="flex h-8 w-8 items-center justify-center text-primary transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                                aria-label="Kurangi">−</button>
                                            <span class="w-10 text-center text-sm font-semibold" x-text="item.qty"></span>
                                            <button type="button" @click="increase(item)"
                                                :disabled="isUnavailable(item) || item.qty >= Number(item.product_variant.stock)"
                                                class="flex h-8 w-8 items-center justify-center text-primary transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                                aria-label="Tambah">+</button>
                                        </div>

                                        <p class="text-sm font-bold text-gray-900"
                                            x-text="rupiah(item.product_variant.price * item.qty)"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Ringkasan --}}
                <aside class="lg:col-span-1">
                    <form method="POST" action="{{ route('checkout') }}" class="lg:sticky lg:top-24">
                        @csrf
                        <input type="hidden" name="type" value="buy-cart">
                        <input type="hidden" name="items" :value="serializedItems">

                        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-card">
                            <h2 class="text-base font-bold text-gray-900">Ringkasan</h2>

                            <ul class="mt-4 space-y-2 text-sm">
                                <template x-for="item in availableItems" :key="item.id">
                                    <li class="flex justify-between gap-3">
                                        <span class="truncate text-gray-500"
                                            x-text="nameOf(item) + ' × ' + item.qty"></span>
                                        <span class="whitespace-nowrap text-gray-900"
                                            x-text="rupiah(item.product_variant.price * item.qty)"></span>
                                    </li>
                                </template>
                            </ul>

                            <template x-if="unavailableCount > 0">
                                <p class="mt-3 rounded-lg bg-warning-50 p-2.5 text-xs text-warning-700">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    <span x-text="unavailableCount"></span> item tidak tersedia dan tidak dihitung.
                                </p>
                            </template>

                            <div class="mt-4 flex justify-between border-t border-gray-100 pt-4 text-lg font-bold">
                                <span>Total</span>
                                <span class="text-primary" x-text="rupiah(subtotal)"></span>
                            </div>

                            <p class="mt-1 text-xs text-gray-400">Ongkos kirim dihitung di halaman berikutnya.</p>

                            <button type="submit" :disabled="!availableItems.length"
                                class="mt-5 flex w-full items-center justify-center gap-2 rounded-lg bg-primary py-3 text-sm font-semibold text-white transition hover:bg-primary-900 disabled:cursor-not-allowed disabled:opacity-50">
                                Lanjut ke Checkout <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>

                            <a href="{{ route('catalogue') }}"
                                class="mt-3 block text-center text-sm font-semibold text-primary hover:underline">
                                Lanjut belanja
                            </a>
                        </div>
                    </form>
                </aside>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            function cartPage(config) {
                return {
                    items: config.items,

                    // Item yang produknya dihapus atau stoknya habis tidak
                    // boleh ikut dihitung maupun dikirim ke checkout.
                    isUnavailable(item) {
                        return !!item.product_variant.deleted_at || this.outOfStock(item);
                    },

                    outOfStock(item) {
                        return Number(item.product_variant.stock) <= 0;
                    },

                    get availableItems() {
                        return this.items.filter(item => !this.isUnavailable(item));
                    },

                    get unavailableCount() {
                        return this.items.length - this.availableItems.length;
                    },

                    get subtotal() {
                        return this.availableItems.reduce(
                            (sum, item) => sum + item.product_variant.price * item.qty, 0
                        );
                    },

                    get serializedItems() {
                        return JSON.stringify(this.availableItems.map(item => ({
                            product_variant_id: item.product_variant.id,
                            qty: item.qty,
                        })));
                    },

                    increase(item) {
                        if (item.qty < Number(item.product_variant.stock)) item.qty++;
                    },

                    decrease(item) {
                        if (item.qty > 1) item.qty--;
                    },

                    nameOf(item) {
                        return `${item.product_variant.product?.name ?? 'Produk'} — ${item.product_variant.name_type}`;
                    },

                    photoOf(item) {
                        try {
                            return JSON.parse(item.product_variant.photo)[0] ?? '/placeholder.jpg';
                        } catch (e) {
                            return '/placeholder.jpg';
                        }
                    },

                    rupiah(value) {
                        return new Intl.NumberFormat('id-ID', {
                            style: 'currency', currency: 'IDR',
                            minimumFractionDigits: 0, maximumFractionDigits: 0,
                        }).format(value);
                    },
                };
            }
        </script>
    @endpush
</x-layout.customer>
