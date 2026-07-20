@props([
    'title' => 'Catering Malang | Pesan Makanan Lezat, Hangat & Fresh',
    'description' => 'Pesan catering di Malang dengan makanan berkualitas, fresh, dan siap diantar ke lokasi Anda. Cek menu spesial kami!',
    'wide' => false,
    // Halaman privat (keranjang, checkout, profil) tidak boleh diindeks.
    'noindex' => false,
    // URL kanonik; default ke path saat ini tanpa query string agar
    // parameter seperti ?slug= tidak menghasilkan URL duplikat.
    'canonical' => null,
    'image' => null,
    'ogType' => 'website',
    // Data terstruktur tambahan khusus halaman (array atau list of array).
    'schema' => null,
])

@php
    use App\Support\Seo;

    // $setting dibagikan lewat view composer di AppServiceProvider,
    // menggantikan pemanggilan GuestController::setting() dari dalam view.
    $phone = $setting['phone_number'] ?? '';
    $whatsapp = preg_replace('/^0/', '62', $phone);

    $canonicalUrl = $canonical ?: url()->current();
    $ogImage = $image ?: ($setting['logo'] ?? null);
    if ($ogImage && ! Str::startsWith($ogImage, ['http://', 'https://'])) {
        $ogImage = url($ogImage);
    }

    // Schema global (bisnis + situs) selalu disertakan, lalu digabung dengan
    // schema khusus halaman bila ada.
    $schemas = [Seo::localBusiness($setting), Seo::website($setting)];

    if ($schema) {
        // Menerima satu array schema maupun list berisi beberapa schema.
        $schemas = array_merge($schemas, isset($schema['@type']) ? [$schema] : $schema);
    }

    $schemas = array_values(array_filter($schemas));
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ $setting['logo'] ?? '' }}">

    <meta name="description" content="{{ $description }}">
    <meta name="author" content="{{ $setting['name'] ?? 'Kusuka Catering' }}">

    {{-- Kanonik mencegah satu halaman terhitung sebagai beberapa URL berbeda. --}}
    <link rel="canonical" href="{{ $canonicalUrl }}">

    @if ($noindex)
        <meta name="robots" content="noindex, nofollow">
    @else
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1">
    @endif

    {{-- Open Graph agar tautan yang dibagikan tampil rapi. --}}
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ $setting['name'] ?? 'Kusuka Catering' }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if ($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    @if ($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
    @endif

    <meta name="theme-color" content="#860000">

    {{-- Data terstruktur schema.org untuk rich result di hasil pencarian. --}}
    @foreach ($schemas as $item)
        <script type="application/ld+json">{!! json_encode($item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endforeach

    {{-- Preconnect ke origin pihak ketiga mempercepat pemuatan (LCP). --}}
    <link rel="preconnect" href="https://kit.fontawesome.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

    <script src="https://kit.fontawesome.com/a47e0565cc.js" crossorigin="anonymous"></script>
    {{-- Plugin collapse harus dimuat sebelum core Alpine (defer menjaga urutan). --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="flex min-h-screen flex-col font-poppins">
    {{-- Lewati navigasi: bantuan aksesibilitas untuk pengguna keyboard. --}}
    <a href="#konten"
        class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-[60] focus:rounded-lg focus:bg-primary focus:px-4 focus:py-2 focus:text-white">
        Lewati ke konten utama
    </a>

    {{-- Header sengaja di luar container: latarnya harus selebar layar.
         Pembatas lebar kontennya ada di dalam komponen header itu sendiri. --}}
    <x-header.customer />

    <main id="konten" class="flex-1">
        <div class="{{ $wide ? 'w-full' : 'container mx-auto px-4 md:px-0' }}">
            <div class="mt-4">
                <x-alert.flash />
            </div>
            {{ $slot }}
        </div>
    </main>

    @if ($phone)
        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="Hubungi kami via WhatsApp"
            class="fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-2xl text-white shadow-card-hover transition-transform duration-200 hover:scale-110">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    @endif

    <x-footer.custom />

    @stack('scripts')
</body>

</html>
