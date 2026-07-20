@php
    use App\Support\Seo;

    // Hanya produk yang punya varian aktif yang masuk ke ItemList.
    $indexable = collect($products)->filter(
        fn ($p) => $p && $p->product_variants && $p->product_variants->whereNull('deleted_at')->isNotEmpty()
    );

    $schema = [
        Seo::itemList($indexable),
        Seo::breadcrumbs([
            'Beranda' => route('home'),
            'Katalog' => route('catalogue'),
        ]),
    ];
@endphp

<x-layout.customer title="Katalog Menu Catering Malang | Kusuka Catering"
    description="Lihat {{ $indexable->count() }}+ pilihan menu catering Malang: paket prasmanan, nasi kotak, snack box, tumpeng, dan hampers. Harga mulai terjangkau, siap diantar."
    :canonical="route('catalogue')" :schema="$schema">

    <div class="my-8 md:my-12" x-data="catalogue({
        products: {{ Illuminate\Support\Js::from($products) }},
        categories: {{ Illuminate\Support\Js::from($categories) }},
    })">

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Katalog Menu</h1>
            <p class="mt-1 text-sm text-gray-500">
                Menampilkan <span class="font-semibold text-gray-700" x-text="filtered.length"></span> menu
            </p>
        </div>

        <div class="flex flex-col gap-6 lg:flex-row">

            {{-- Filter --}}
            <aside class="lg:w-64 lg:flex-shrink-0">
                <div class="lg:sticky lg:top-24">
                    <label for="cari" class="sr-only">Cari menu</label>
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                        <input id="cari" type="search" placeholder="Cari menu…" x-model.debounce.300ms="search"
                            class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                    </div>

                    <div class="mt-6">
                        <h2 class="text-xs font-bold uppercase tracking-wide text-gray-500">Kategori</h2>

                        {{-- Di mobile kategori digeser horizontal agar tidak memakan layar. --}}
                        <div class="no-scrollbar mt-3 flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:overflow-visible">
                            <button type="button" @click="activeCategory = null"
                                class="whitespace-nowrap rounded-lg px-3 py-2 text-left text-sm font-medium transition-colors"
                                :class="activeCategory === null ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-50 hover:text-primary'">
                                Semua
                            </button>

                            <template x-for="category in usableCategories" :key="category.id">
                                <button type="button" @click="toggleCategory(category.id)"
                                    class="whitespace-nowrap rounded-lg px-3 py-2 text-left text-sm font-medium transition-colors"
                                    :class="activeCategory === category.id ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-primary-50 hover:text-primary'">
                                    <span x-text="category.name"></span>
                                    <span class="ml-1 text-xs opacity-60" x-text="'(' + countIn(category.id) + ')'"></span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <button type="button" x-show="search || activeCategory" x-cloak @click="reset()"
                        class="mt-4 text-sm font-semibold text-primary hover:underline">
                        <i class="fa-solid fa-rotate-left mr-1.5 text-xs"></i>Reset filter
                    </button>
                </div>
            </aside>

            {{-- Grid produk --}}
            <div class="min-w-0 flex-1">
                <div x-show="paginated.length" class="grid grid-cols-2 gap-4 md:grid-cols-3 xl:grid-cols-4">
                    <template x-for="product in paginated" :key="product.id">
                        {{-- Slug disertakan agar tautan internal sama persis
                             dengan URL kanonik dan yang ada di sitemap. --}}
                        <a :href="'/catalogue-detail/' + product.id + '/' + slugify(product.name)"
                            class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover">

                            <div class="relative aspect-square overflow-hidden bg-gray-100">
                                <img :src="photoOf(product)" :alt="product.name" loading="lazy"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    onerror="this.src='/placeholder.jpg'">

                                <span x-show="totalStock(product) === 0" x-cloak
                                    class="absolute inset-0 flex items-center justify-center bg-black/50 text-xs font-bold uppercase tracking-wide text-white">
                                    Stok Habis
                                </span>
                            </div>

                            <div class="flex flex-1 flex-col p-3 md:p-4">
                                <h3 class="line-clamp-2 text-sm font-semibold text-gray-900 md:text-base"
                                    x-text="product.name"></h3>
                                <p class="mt-1 line-clamp-2 text-xs text-gray-500" x-text="descriptionOf(product)"></p>

                                <div class="mt-auto pt-3">
                                    <p class="text-sm font-bold text-primary" x-text="priceLabel(product)"></p>
                                    <span
                                        class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-primary opacity-0 transition-opacity group-hover:opacity-100">
                                        Lihat detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </template>
                </div>

                {{-- Empty state: sebelumnya pencarian tanpa hasil hanya menampilkan area kosong. --}}
                <div x-show="!paginated.length" x-cloak
                    class="rounded-xl border border-dashed border-gray-300 bg-white">
                    <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-primary-50">
                            <i class="fa-solid fa-magnifying-glass text-2xl text-primary-300"></i>
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-gray-900">Menu tidak ditemukan</h3>
                        <p class="mt-1.5 max-w-sm text-sm text-gray-500">
                            Coba kata kunci lain atau pilih kategori yang berbeda.
                        </p>
                        <button type="button" @click="reset()"
                            class="mt-5 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-900">
                            Tampilkan semua menu
                        </button>
                    </div>
                </div>

                {{-- Paginasi: hanya menampilkan jendela halaman di sekitar
                     halaman aktif, bukan seluruh nomor halaman. --}}
                <nav x-show="totalPages > 1" x-cloak class="mt-8 flex items-center justify-center gap-1"
                    aria-label="Paginasi">
                    <button type="button" @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-sm transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                        aria-label="Halaman sebelumnya">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>

                    <template x-for="page in pageWindow" :key="page">
                        <button type="button" @click="page !== '…' && goToPage(page)" :disabled="page === '…'"
                            class="h-9 min-w-[36px] rounded-lg border px-2 text-sm font-medium transition"
                            :class="page === currentPage
                                ? 'border-primary bg-primary text-white'
                                : 'border-gray-300 text-gray-600 hover:bg-gray-50 disabled:border-transparent disabled:hover:bg-transparent'"
                            x-text="page"></button>
                    </template>

                    <button type="button" @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-300 text-sm transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                        aria-label="Halaman berikutnya">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </nav>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function catalogue(config) {
                return {
                    // Produk tanpa varian aktif tidak pernah ditampilkan.
                    products: config.products.filter(p => activeVariants(p).length > 0),
                    categories: config.categories,
                    search: '',
                    activeCategory: null,
                    currentPage: 1,
                    perPage: 16,

                    // Kategori yang tidak punya produk aktif tidak ditampilkan
                    // sebagai filter — memilihnya hanya akan menghasilkan
                    // halaman kosong.
                    get usableCategories() {
                        return this.categories.filter(c => this.countIn(c.id) > 0);
                    },

                    countIn(categoryId) {
                        return this.products.filter(p => p.category?.id === categoryId).length;
                    },

                    get filtered() {
                        const q = this.search.trim().toLowerCase();

                        return this.products.filter(product => {
                            // Difilter berdasarkan id, bukan nama: nama kategori
                            // tidak unik sehingga beberapa kategori berbeda
                            // dengan nama sama akan saling tercampur.
                            const byCategory = !this.activeCategory || product.category?.id === this.activeCategory;
                            const bySearch = !q || product.name.toLowerCase().includes(q);
                            return byCategory && bySearch;
                        });
                    },

                    get totalPages() {
                        return Math.max(1, Math.ceil(this.filtered.length / this.perPage));
                    },

                    get paginated() {
                        // Jika filter menyusut sampai halaman aktif tidak ada,
                        // mundurkan ke halaman terakhir yang valid.
                        const page = Math.min(this.currentPage, this.totalPages);
                        return this.filtered.slice((page - 1) * this.perPage, page * this.perPage);
                    },

                    get pageWindow() {
                        const total = this.totalPages;
                        const current = Math.min(this.currentPage, total);

                        if (total <= 7) {
                            return Array.from({ length: total }, (_, i) => i + 1);
                        }

                        const pages = [1];
                        const start = Math.max(2, current - 1);
                        const end = Math.min(total - 1, current + 1);

                        if (start > 2) pages.push('…');
                        for (let i = start; i <= end; i++) pages.push(i);
                        if (end < total - 1) pages.push('…');
                        pages.push(total);

                        return pages;
                    },

                    toggleCategory(categoryId) {
                        this.activeCategory = this.activeCategory === categoryId ? null : categoryId;
                        this.currentPage = 1;
                    },

                    reset() {
                        this.search = '';
                        this.activeCategory = null;
                        this.currentPage = 1;
                    },

                    goToPage(page) {
                        if (page >= 1 && page <= this.totalPages) {
                            this.currentPage = page;
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    },

                    photoOf(product) {
                        const variant = activeVariants(product)[0];
                        return parsePhoto(variant?.photo);
                    },

                    descriptionOf(product) {
                        return activeVariants(product)[0]?.description ?? '';
                    },

                    totalStock(product) {
                        return activeVariants(product).reduce((sum, v) => sum + Number(v.stock ?? 0), 0);
                    },

                    priceLabel(product) {
                        const prices = activeVariants(product).map(v => Number(v.price));
                        if (!prices.length) return 'Harga tidak tersedia';

                        const min = Math.min(...prices);
                        const max = Math.max(...prices);
                        return min === max ? rupiah(min) : `${rupiah(min)} – ${rupiah(max)}`;
                    },
                };
            }

            // Varian yang sudah di-soft-delete tidak boleh ikut ditampilkan.
            function activeVariants(product) {
                return (product.product_variants ?? []).filter(v => !v.deleted_at);
            }

            // photo disimpan sebagai string JSON; jaga agar format rusak tidak
            // mematikan seluruh komponen Alpine.
            function parsePhoto(photo) {
                try {
                    return JSON.parse(photo)[0] ?? '/placeholder.jpg';
                } catch (e) {
                    return '/placeholder.jpg';
                }
            }

            // Meniru Str::slug() Laravel supaya URL yang dibuat di sisi klien
            // identik dengan yang dihasilkan server.
            function slugify(text) {
                return String(text)
                    .toLowerCase()
                    .normalize('NFD').replace(/[̀-ͯ]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
            }

            function rupiah(value) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency', currency: 'IDR',
                    minimumFractionDigits: 0, maximumFractionDigits: 0,
                }).format(value);
            }
        </script>
    @endpush
</x-layout.customer>
