@php
    use App\Support\Seo;

    $variants = $product['product_variants']->whereNull('deleted_at')->values();
    $firstVariant = $variants->first();

    // URL kanonik selalu versi berslug, agar tautan tanpa slug tidak
    // dianggap halaman terpisah oleh mesin pencari.
    $slug = Str::slug($product->name);
    $canonical = route('catalogue-detail', ['id' => $product->id, 'slug' => $slug]);

    $prices = $variants->pluck('price')->filter();
    $photo = json_decode($firstVariant->photo ?? '[]', true)[0] ?? null;

    // Deskripsi meta. Deskripsi produk dipakai hanya bila cukup panjang untuk
    // informatif di hasil pencarian; snippet yang terlalu pendek justru
    // menurunkan kualitas tampilan dan rasio klik. Kalau pendek, dilengkapi
    // dengan konteks harga + lokasi yang mengandung kata kunci.
    $rawDescription = trim(strip_tags($firstVariant->description ?? ''));
    $priceLabel = 'Rp' . number_format((int) $prices->min(), 0, ',', '.');
    $fallback = 'Pesan ' . $product->name . ' dari Kusuka Catering Malang mulai '
        . $priceLabel . '. Menu fresh, harga transparan, diantar tepat waktu ke lokasi acara Anda.';

    $metaDescription = Str::length($rawDescription) >= 50
        ? Str::limit($rawDescription, 155)
        : Str::limit(trim($rawDescription . ' ' . $fallback), 155);

    // Rating agregat khusus produk ini (bukan rata-rata seluruh situs).
    $ratingStats = app(\App\Actions\RateAction::class)->statsForProduct($product->id);

    $schema = [
        Seo::product($product, $variants, $ratingStats['average'], $ratingStats['count']),
        Seo::breadcrumbs([
            'Beranda' => route('home'),
            'Katalog' => route('catalogue'),
            $product->name => $canonical,
        ]),
    ];
@endphp

<x-layout.customer title="{{ $product->name }} — Catering Malang | Kusuka Catering"
    :description="$metaDescription" :canonical="$canonical" :image="$photo" ogType="product"
    :schema="$schema">

    <div class="my-6 md:my-10">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center gap-2 text-sm text-gray-500" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="transition-colors hover:text-primary">Beranda</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('catalogue') }}" class="transition-colors hover:text-primary">Katalog</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="truncate font-medium text-gray-900">{{ $product->name }}</span>
        </nav>

        @if (! $firstVariant)
            <x-ui.empty icon="fa-box-open" title="Menu belum tersedia"
                message="Varian untuk menu ini sedang tidak tersedia. Silakan lihat menu lainnya."
                actionLabel="Kembali ke katalog" actionHref="{{ route('catalogue') }}"
                class="rounded-xl border border-gray-200 bg-white" />
        @else
            <div class="grid gap-8 lg:grid-cols-12" x-data="productDetail({
                variants: {{ Illuminate\Support\Js::from($variants) }},
            })">

                {{-- Galeri --}}
                <div class="lg:col-span-5">
                    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                        <div class="aspect-square bg-gray-100">
                            <img :src="currentImage" :alt="variant.name_type" class="h-full w-full object-cover"
                                onerror="this.src='/placeholder.jpg'">
                        </div>
                    </div>

                    <div x-show="images.length > 1" class="no-scrollbar mt-3 flex gap-2 overflow-x-auto">
                        <template x-for="(image, index) in images" :key="index">
                            <button type="button" @click="imageIndex = index"
                                class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg border-2 transition"
                                :class="imageIndex === index ? 'border-primary' : 'border-transparent opacity-60 hover:opacity-100'">
                                <img :src="image" alt="" class="h-full w-full object-cover"
                                    onerror="this.src='/placeholder.jpg'">
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Informasi --}}
                <div class="lg:col-span-4">
                    <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">{{ $product->name }}</h1>

                    @if ($product->category)
                        <span class="mt-2 inline-block rounded-full bg-primary-50 px-3 py-1 text-xs font-semibold text-primary">
                            {{ $product->category->name }}
                        </span>
                    @endif

                    <p class="mt-4 text-3xl font-bold text-primary" x-text="rupiah(variant.price)"></p>

                    <div class="mt-6">
                        <h2 class="text-sm font-semibold text-gray-700">Pilih Varian</h2>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <template x-for="option in variants" :key="option.id">
                                <button type="button" @click="selectVariant(option)"
                                    class="rounded-lg border px-4 py-2 text-sm font-medium transition"
                                    :class="[
                                        variant.id === option.id
                                            ? 'border-primary bg-primary text-white'
                                            : 'border-gray-300 text-gray-700 hover:border-primary hover:text-primary',
                                        Number(option.stock) === 0 ? 'opacity-50' : ''
                                    ]">
                                    <span x-text="option.name_type"></span>
                                    <span x-show="Number(option.stock) === 0" class="ml-1 text-[10px]">(habis)</span>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-sm font-semibold text-gray-700">Deskripsi</h2>
                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-gray-600"
                            x-text="variant.description"></p>
                    </div>
                </div>

                {{-- Panel pembelian --}}
                <div class="lg:col-span-3">
                    <div class="lg:sticky lg:top-24">
                        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-card">

                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Stok</span>
                                <span class="font-semibold" :class="outOfStock ? 'text-primary-danger' : 'text-gray-900'"
                                    x-text="outOfStock ? 'Habis' : variant.stock + ' tersedia'"></span>
                            </div>

                            <div class="mt-4">
                                <label class="text-sm text-gray-500">Jumlah</label>
                                <div class="mt-1.5 flex items-center rounded-lg border border-gray-300">
                                    <button type="button" @click="decrease()" :disabled="quantity <= 1 || outOfStock"
                                        class="flex h-10 w-10 items-center justify-center text-primary transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        aria-label="Kurangi jumlah">−</button>
                                    <input type="number" x-model.number="quantity" @change="clampQuantity()"
                                        :max="variant.stock" min="1" :disabled="outOfStock"
                                        class="w-full [appearance:textfield] border-0 text-center text-sm font-semibold focus:outline-none focus:ring-0 disabled:bg-transparent [&::-webkit-inner-spin-button]:appearance-none">
                                    <button type="button" @click="increase()"
                                        :disabled="quantity >= Number(variant.stock) || outOfStock"
                                        class="flex h-10 w-10 items-center justify-center text-primary transition hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
                                        aria-label="Tambah jumlah">+</button>
                                </div>
                            </div>

                            <div class="mt-4 flex items-baseline justify-between border-t border-gray-100 pt-4">
                                <span class="text-sm text-gray-500">Subtotal</span>
                                <span class="text-lg font-bold text-primary" x-text="rupiah(subtotal)"></span>
                            </div>

                            {{-- Tombol dinonaktifkan saat stok habis; sebelumnya
                                 produk habis tetap bisa dimasukkan ke keranjang. --}}
                            <div class="mt-5 space-y-2">
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_variant_id" :value="variant.id">
                                    <input type="hidden" name="qty" :value="quantity">
                                    <button type="submit" :disabled="outOfStock"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg border border-primary px-4 py-2.5 text-sm font-semibold text-primary transition hover:bg-primary hover:text-white disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-transparent disabled:hover:text-primary">
                                        <i class="fa-solid fa-cart-plus"></i>Tambah Keranjang
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('checkout') }}">
                                    @csrf
                                    <input type="hidden" name="type" value="buy-directly">
                                    <input type="hidden" name="product_variant_id" :value="variant.id">
                                    <input type="hidden" name="qty" :value="quantity">
                                    <button type="submit" :disabled="outOfStock"
                                        class="flex w-full items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-900 disabled:cursor-not-allowed disabled:opacity-50">
                                        <i class="fa-solid fa-bolt"></i>Beli Sekarang
                                    </button>
                                </form>
                            </div>

                            <p class="mt-4 flex items-center justify-center gap-1.5 text-xs text-gray-400">
                                <i class="fa-solid fa-shield-halved"></i>Pembayaran aman via Midtrans
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Rekomendasi --}}
        @php
            $recommended = collect($products)
                ->filter(fn ($p) => $p && $p->id !== $product->id
                    && $p->product_variants
                    && $p->product_variants->whereNull('deleted_at')->isNotEmpty())
                ->take(4);
        @endphp

        @if ($recommended->isNotEmpty())
            <section class="mt-16">
                <h2 class="mb-6 text-xl font-bold text-gray-900 md:text-2xl">Rekomendasi Lainnya</h2>

                <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
                    @foreach ($recommended as $item)
                        @php
                            $itemVariants = $item->product_variants->whereNull('deleted_at')->values();
                            $prices = $itemVariants->pluck('price');
                            $photo = json_decode($itemVariants[0]->photo, true)[0] ?? '/placeholder.jpg';
                        @endphp

                        <a href="{{ route('catalogue-detail', ['id' => $item->id, 'slug' => Str::slug($item->name)]) }}"
                            class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover">
                            <div class="aspect-square overflow-hidden bg-gray-100">
                                <img src="{{ $photo }}" alt="{{ $item->name }}" loading="lazy"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            </div>
                            <div class="flex flex-1 flex-col p-3 md:p-4">
                                <h3 class="line-clamp-2 text-sm font-semibold text-gray-900">{{ $item->name }}</h3>
                                <p class="mt-auto pt-2 text-sm font-bold text-primary">
                                    @if ($prices->min() === $prices->max())
                                        Rp {{ number_format($prices->min(), 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($prices->min(), 0, ',', '.') }} –
                                        Rp {{ number_format($prices->max(), 0, ',', '.') }}
                                    @endif
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    @push('scripts')
        <script>
            function productDetail(config) {
                return {
                    variants: config.variants,
                    variant: config.variants[0],
                    quantity: 1,
                    imageIndex: 0,

                    get images() {
                        try {
                            const parsed = JSON.parse(this.variant.photo);
                            return Array.isArray(parsed) && parsed.length ? parsed : ['/placeholder.jpg'];
                        } catch (e) {
                            return ['/placeholder.jpg'];
                        }
                    },

                    get currentImage() {
                        return this.images[this.imageIndex] ?? '/placeholder.jpg';
                    },

                    get outOfStock() {
                        return Number(this.variant.stock) <= 0;
                    },

                    get subtotal() {
                        return this.quantity * Number(this.variant.price);
                    },

                    selectVariant(option) {
                        this.variant = option;
                        this.quantity = Number(option.stock) > 0 ? 1 : 0;
                        this.imageIndex = 0;
                    },

                    increase() {
                        if (this.quantity < Number(this.variant.stock)) this.quantity++;
                    },

                    decrease() {
                        if (this.quantity > 1) this.quantity--;
                    },

                    // Input angka bisa diketik manual, jadi nilainya dijaga
                    // tetap di antara 1 dan stok yang tersedia.
                    clampQuantity() {
                        const max = Number(this.variant.stock);
                        let value = parseInt(this.quantity, 10);

                        if (isNaN(value) || value < 1) value = 1;
                        if (value > max) value = max;

                        this.quantity = value;
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
