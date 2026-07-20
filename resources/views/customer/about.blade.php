@php
    use App\Support\Seo;

    // Daftar layanan kini dikelola dari admin (Pengaturan > Layanan Kami),
    // bukan lagi di-hardcode di view ini.
    $services = json_decode($setting->our_service ?? '[]', true) ?: [];

    // Tata letak mosaik pada grid 6 kolom.
    //
    // Pola otomatis membuat lebar kartu berselang-seling (4-2, 2-4, 3-3)
    // sehingga tampilannya acak tapi tiap baris tetap terisi penuh, berapa
    // pun jumlah layanannya. Admin boleh menimpa lebar per kartu.
    $autoPattern = [
        'md:col-span-4', 'md:col-span-2',
        'md:col-span-2', 'md:col-span-4',
        'md:col-span-3', 'md:col-span-3',
    ];

    $spanFor = [
        'kecil' => 'md:col-span-2',
        'sedang' => 'md:col-span-3',
        'besar' => 'md:col-span-4',
        'penuh' => 'md:col-span-6',
    ];

    $schema = Seo::breadcrumbs([
        'Beranda' => route('home'),
        'Tentang Kami' => route('about'),
    ]);
@endphp

<x-layout.customer title="Tentang Kami | Kusuka Catering Malang"
    description="Kenali Kusuka Catering: visi, misi, dan layanan catering Malang untuk acara pernikahan, kantor, snack box, hampers, hingga nasi tumpeng."
    :canonical="route('about')" :schema="$schema">

    {{-- Visi & Misi --}}
    <section class="my-10 md:my-16">
        <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Tentang Kusuka Catering</h1>
        <p class="mt-1 text-sm text-gray-500">Mengenal lebih dekat siapa kami dan apa yang kami perjuangkan.</p>

        <div class="mt-8 space-y-8">
            @foreach ([['Visi', $setting->visi ?? '', '/assets/images/image-4.png', false], ['Misi', $setting->misi ?? '', '/assets/images/image-5.png', true]] as [$title, $body, $image, $reverse])
                <div class="grid items-center gap-6 overflow-hidden rounded-xl border border-gray-200 bg-white md:grid-cols-2">
                    <div class="{{ $reverse ? 'md:order-2' : '' }}">
                        <img src="{{ $image }}" alt="{{ $title }}" loading="lazy"
                            class="h-56 w-full object-cover md:h-80">
                    </div>
                    <div class="p-6 {{ $reverse ? 'md:order-1' : '' }}">
                        <h2 class="text-xl font-bold text-primary md:text-2xl">{{ $title }}</h2>
                        <p class="mt-3 whitespace-pre-line leading-relaxed text-gray-600">
                            {{ $body ?: 'Belum diisi.' }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Layanan --}}
    <section class="my-12 md:my-20">
        <div class="mb-8 max-w-xl">
            <h2 class="text-2xl font-bold text-gray-900 md:text-3xl">Layanan Kami</h2>
            <p class="mt-2 text-sm text-gray-500 md:text-base">
                Dari acara kantor hingga pernikahan, kami siap menyiapkan hidangan sesuai kebutuhan Anda.
            </p>
            <x-ui.button href="{{ route('catalogue') }}" class="mt-5" iconRight="fa-arrow-right">
                Lihat Menu
            </x-ui.button>
        </div>

        @if ($services)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-6">
                @foreach ($services as $index => $service)
                    @php
                        $size = $service['size'] ?? 'otomatis';
                        // Lebar mengikuti pola mosaik kecuali admin memilih
                        // ukuran tertentu untuk kartu ini.
                        $span = $spanFor[$size] ?? $autoPattern[$index % count($autoPattern)];
                        $label = $service['label'] ?? '';
                        $image = $service['image'] ?? '/placeholder.jpg';
                    @endphp

                    <div class="group relative overflow-hidden rounded-xl {{ $span }}">
                        <img src="{{ $image }}" alt="{{ $label }}" loading="lazy"
                            class="h-44 w-full object-cover transition-transform duration-300 group-hover:scale-105 md:h-56 lg:h-64"
                            onerror="this.src='/placeholder.jpg'">
                        {{-- Gradien memastikan teks putih tetap terbaca di atas
                             foto terang; sebelumnya caption sering tak terlihat. --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>
                        <p class="absolute bottom-0 left-0 w-full px-4 py-3 text-sm font-bold text-white md:text-base">
                            {{ $label }}
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <x-ui.empty icon="fa-concierge-bell" title="Belum ada layanan"
                message="Daftar layanan akan segera kami tampilkan di sini."
                class="rounded-xl border border-dashed border-gray-300 bg-white" />
        @endif
    </section>

    {{-- Ulasan pelanggan --}}
    <section class="my-12 md:my-20">
        <h2 class="mb-2 text-center text-2xl font-bold text-gray-900 md:text-3xl">Kata Pelanggan Kami</h2>
        <p class="mb-8 text-center text-sm text-gray-500">Ulasan asli dari pelanggan yang telah memesan.</p>

        @if (collect($rates)->isEmpty())
            <x-ui.empty icon="fa-comment-dots" title="Belum ada ulasan"
                message="Jadilah yang pertama memberi ulasan setelah pesanan Anda selesai."
                class="rounded-xl border border-dashed border-gray-300 bg-white" />
        @else
            <div class="grid gap-5 md:grid-cols-3">
                @foreach ($rates as $rate)
                    <figure class="flex h-full flex-col rounded-xl border border-gray-200 bg-white p-5 shadow-card">
                        <div class="flex items-center gap-3">
                            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-primary text-sm font-bold text-white">
                                {{ Str::upper(Str::substr($rate->user->name ?? '?', 0, 1)) }}
                            </span>
                            <div class="min-w-0">
                                <figcaption class="truncate text-sm font-bold text-gray-900">
                                    {{ $rate->user->name ?? 'Pelanggan' }}
                                </figcaption>
                                <p class="text-xs text-gray-500">{{ $rate->user->role->name ?? 'Customer' }}</p>
                            </div>
                        </div>

                        <div class="mt-3 flex gap-0.5" aria-label="Nilai {{ $rate->rate }} dari 5">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star text-xs {{ $i <= $rate->rate ? 'text-amber-400' : 'text-gray-200' }}"></i>
                            @endfor
                        </div>

                        <blockquote class="mt-3 line-clamp-4 text-sm leading-relaxed text-gray-600">
                            &ldquo;{{ $rate->message }}&rdquo;
                        </blockquote>
                    </figure>
                @endforeach
            </div>
        @endif
    </section>

</x-layout.customer>
