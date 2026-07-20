@php
    use App\Support\Seo;

    // Judul mengedepankan kata kunci utama + lokasi, di bawah ~60 karakter
    // agar tidak terpotong di hasil pencarian.
    $schema = Seo::breadcrumbs(['Beranda' => route('home')]);
@endphp

<x-layout.customer title="Catering Malang Terpercaya | Kusuka Catering"
    description="Kusuka Catering melayani catering Malang untuk pernikahan, acara kantor, snack box, hampers, dan nasi tumpeng. Menu fresh, harga transparan, diantar tepat waktu."
    :canonical="route('home')" :schema="$schema">

    {{-- Hero --}}
    <section class="grid items-center gap-8 py-10 md:grid-cols-2 md:py-16">
        <div class="animate-fade-in-up space-y-5 md:space-y-7">
            <span class="inline-flex items-center gap-2 rounded-full bg-primary-50 px-3 py-1.5 text-xs font-semibold text-primary">
                <i class="fa-solid fa-location-dot"></i> Melayani area Malang &amp; sekitarnya
            </span>

            <h1 class="text-3xl font-extrabold leading-tight text-gray-900 md:text-5xl">
                Catering Malang<br>
                Pesan <span class="text-primary">Makanan Lezat &amp; Fresh</span>
            </h1>

            <p class="max-w-lg text-base leading-relaxed text-gray-600 md:text-lg">
                Pesan catering Malang dengan makanan lezat, fresh, dan siap diantar tepat waktu ke lokasi Anda.
            </p>

            <div class="flex flex-wrap gap-3">
                <x-ui.button href="{{ route('catalogue') }}" size="lg" iconRight="fa-arrow-right">
                    Lihat Menu
                </x-ui.button>
                <x-ui.button href="{{ route('contact') }}" size="lg" variant="outline" icon="fa-phone">
                    Hubungi Kami
                </x-ui.button>
            </div>

            {{-- Nilai jual utama --}}
            <div class="grid grid-cols-3 gap-4 border-t border-gray-200 pt-6">
                @foreach ([['fa-leaf', 'Bahan Fresh'], ['fa-truck-fast', 'Antar Tepat Waktu'], ['fa-shield-halved', 'Pembayaran Aman']] as [$icon, $label])
                    <div class="text-center md:text-left">
                        <i class="fa-solid {{ $icon }} text-lg text-primary"></i>
                        <p class="mt-1.5 text-xs font-medium text-gray-600 md:text-sm">{{ $label }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="hidden items-center justify-end gap-4 md:flex">
            <img src="/assets/images/image-1.png" alt="Hidangan catering Kusuka" class="w-80 rounded-2xl shadow-card-hover">
            <img src="/assets/images/image-2.png" alt="Buah segar" class="h-56 w-56 rounded-2xl object-cover shadow-card">
        </div>
    </section>

    <div class="my-12 md:my-20">
        <x-banner.event />
    </div>

    {{-- Menu unggulan --}}
    <section class="my-12 md:my-20">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 md:text-3xl">Menu Spesial Kami</h2>
                <p class="mt-1 text-sm text-gray-500">Pilihan favorit pelanggan Kusuka Catering.</p>
            </div>
            <a href="{{ route('catalogue') }}"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary hover:underline">
                Lihat semua <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        @php
            $featured = collect($products)->filter(
                fn ($p) => $p && $p->product_variants && $p->product_variants->whereNull('deleted_at')->isNotEmpty()
            );
        @endphp

        @if ($featured->isEmpty())
            <x-ui.empty icon="fa-utensils" title="Menu belum tersedia"
                message="Menu spesial akan segera kami tampilkan di sini."
                class="rounded-xl border border-dashed border-gray-300 bg-white" />
        @else
            <div class="grid grid-cols-2 gap-4 md:grid-cols-4 md:gap-6">
                @foreach ($featured as $product)
                    @php
                        $variants = $product->product_variants->whereNull('deleted_at')->values();
                        $prices = $variants->pluck('price');
                        $photo = json_decode($variants[0]->photo, true)[0] ?? '/placeholder.jpg';
                        $stock = $variants->sum('stock');
                    @endphp

                    <a href="{{ route('catalogue-detail', ['id' => $product->id, 'slug' => Str::slug($product->name)]) }}"
                        class="group flex flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-card transition-all duration-200 hover:-translate-y-0.5 hover:shadow-card-hover">

                        <div class="relative aspect-square overflow-hidden bg-gray-100">
                            <img src="{{ $photo }}" alt="{{ $product->name }}" loading="lazy"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                            @if ($stock <= 0)
                                <span class="absolute inset-0 flex items-center justify-center bg-black/50 text-xs font-bold uppercase tracking-wide text-white">
                                    Stok Habis
                                </span>
                            @endif
                        </div>

                        <div class="flex flex-1 flex-col p-3 md:p-4">
                            <h3 class="line-clamp-2 text-sm font-semibold text-gray-900 md:text-base">{{ $product->name }}</h3>
                            <p class="mt-1 line-clamp-2 text-xs text-gray-500">{{ $variants[0]->description }}</p>
                            <p class="mt-auto pt-3 text-sm font-bold text-primary">
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
        @endif
    </section>

    {{-- Portofolio --}}
    @if (! empty($our_customers))
        <section class="my-12 md:my-20">
            <h2 class="mb-2 text-center text-2xl font-bold text-gray-900 md:text-3xl">Telah Dipercaya Oleh</h2>
            <p class="mb-8 text-center text-sm text-gray-500">Klien yang pernah bekerja sama dengan kami.</p>

            <div class="flex flex-wrap items-center justify-center gap-6 rounded-xl border border-gray-200 bg-white p-8 md:gap-10">
                @foreach ($our_customers as $our_customer)
                    <a href="{{ $our_customer['href'] }}" target="_blank" rel="noopener"
                        class="opacity-60 transition-opacity hover:opacity-100">
                        <img src="{{ $our_customer['logo'] }}" alt="Logo klien" loading="lazy"
                            class="h-10 object-contain md:h-14">
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Cakupan layanan --}}
    <section class="my-12 md:my-20">
        <h2 class="mb-2 text-center text-2xl font-bold text-gray-900 md:text-3xl">Area Layanan</h2>
        <p class="mb-8 text-center text-sm text-gray-500">Wilayah yang kami jangkau untuk pengantaran.</p>

        <div class="overflow-hidden rounded-xl border border-gray-200">
            <x-map.custom :coverageArea="json_decode($setting['our_coverage'], true)" />
        </div>
    </section>

    {{-- Ajakan --}}
    <section class="my-12 overflow-hidden rounded-2xl bg-primary px-6 py-12 text-center md:my-20 md:px-12">
        <h2 class="text-2xl font-bold text-white md:text-3xl">Siap memesan untuk acara Anda?</h2>
        <p class="mx-auto mt-2 max-w-xl text-sm text-white/80 md:text-base">
            Pilih menu favorit, tentukan jumlah porsi, dan biarkan kami yang mengurus sisanya.
        </p>
        <a href="{{ route('catalogue') }}"
            class="mt-6 inline-flex items-center gap-2 rounded-lg bg-white px-6 py-3 text-sm font-semibold text-primary transition hover:bg-gray-100">
            Mulai Pesan <i class="fa-solid fa-arrow-right text-xs"></i>
        </a>
    </section>

</x-layout.customer>
