@props([
    'title' => 'Panel Admin',
    'heading' => null,
    'subtitle' => null,
])

@php
    // $setting dibagikan lewat view composer di AppServiceProvider.
    $menu = [
        'Utama' => [
            ['route' => 'dashboard', 'icon' => 'fa-gauge-high', 'label' => 'Dashboard'],
        ],
        'Produk & Katalog' => [
            ['route' => 'data.katalog', 'icon' => 'fa-book', 'label' => 'Daftar Produk'],
            ['route' => 'data.katalog.tambah', 'icon' => 'fa-plus', 'label' => 'Tambah Produk'],
            ['route' => 'data.kategori', 'icon' => 'fa-table-cells-large', 'label' => 'Daftar Kategori'],
            ['route' => 'data.kategori.tambah', 'icon' => 'fa-plus', 'label' => 'Tambah Kategori'],
        ],
        'Pesanan' => [
            ['route' => 'data.pesanan', 'icon' => 'fa-list-check', 'label' => 'Semua Pesanan'],
            ['route' => 'data.riwayat.pesanan', 'icon' => 'fa-clock-rotate-left', 'label' => 'Riwayat & Laporan'],
        ],
        'Pelanggan' => [
            ['route' => 'data.pelanggan', 'icon' => 'fa-users', 'label' => 'Daftar Pelanggan'],
            ['route' => 'data.rate', 'icon' => 'fa-star', 'label' => 'Rating Pelanggan'],
        ],
        'Pengaturan' => [
            ['route' => 'setting', 'icon' => 'fa-gear', 'label' => 'Umum'],
            ['route' => 'setting.special-product', 'icon' => 'fa-wand-magic-sparkles', 'label' => 'Produk Spesial'],
            ['route' => 'setting.service', 'icon' => 'fa-concierge-bell', 'label' => 'Layanan Kami'],
            ['route' => 'setting.customer', 'icon' => 'fa-handshake', 'label' => 'Customer Kita'],
            ['route' => 'setting.social-media', 'icon' => 'fa-share-nodes', 'label' => 'Sosial Media'],
            ['route' => 'setting.event', 'icon' => 'fa-calendar-days', 'label' => 'Promo Event'],
        ],
    ];
@endphp

<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} | {{ $setting['name'] ?? 'Kusuka Catering' }}</title>
    <link rel="icon" type="image/png" href="{{ $setting['logo'] ?? '' }}">
    <meta name="robots" content="noindex">

    {{-- Shell admin kini murni Tailwind + Alpine. Template lama memuat jQuery,
         Bootstrap, DataTables, Chart.js, jqvmap, jszip, dan pdfmake pada
         SETIAP halaman admin — sebagian besar tidak pernah dipakai, termasuk
         peta dunia yang diisi data contoh (sample_data). --}}
    <script src="https://kit.fontawesome.com/a47e0565cc.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>

<body class="bg-gray-50 font-poppins" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"
        class="fixed inset-0 z-30 bg-black/40 lg:hidden" aria-hidden="true"></div>

    {{-- Sidebar --}}
    <aside
        class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform duration-200 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

        <div class="flex h-16 flex-shrink-0 items-center gap-3 border-b border-gray-100 px-5">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-2.5">
                <img src="{{ $setting['logo'] ?? '' }}" alt="" class="h-9 w-9 flex-shrink-0 object-contain">
                <span class="truncate text-sm font-bold text-gray-900">{{ $setting['name'] ?? 'Kusuka' }}</span>
            </a>
            <button @click="sidebarOpen = false"
                class="ml-auto flex h-8 w-8 items-center justify-center rounded-lg text-gray-400 hover:bg-gray-100 lg:hidden"
                aria-label="Tutup menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto px-3 py-4" aria-label="Navigasi admin">
            @foreach ($menu as $group => $items)
                <p class="px-3 pb-1.5 pt-4 text-[11px] font-bold uppercase tracking-wider text-gray-400 first:pt-0">
                    {{ $group }}
                </p>
                <ul class="space-y-0.5">
                    @foreach ($items as $item)
                        @php $active = request()->routeIs($item['route']); @endphp
                        <li>
                            <a href="{{ route($item['route']) }}" @if ($active) aria-current="page" @endif
                                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors
                                    {{ $active ? 'bg-primary text-white' : 'text-gray-600 hover:bg-primary-50 hover:text-primary' }}">
                                <i class="fa-solid {{ $item['icon'] }} w-4 flex-shrink-0 text-center text-xs"></i>
                                <span class="truncate">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endforeach
        </nav>

        <div class="flex-shrink-0 border-t border-gray-100 p-3">
            <a href="{{ route('home') }}" target="_blank"
                class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-50">
                <i class="fa-solid fa-arrow-up-right-from-square w-4 text-center text-xs"></i>Lihat Situs
            </a>
        </div>
    </aside>

    {{-- Konten --}}
    <div class="lg:pl-64">

        <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-gray-200 bg-white/95 px-4 backdrop-blur md:px-6">
            <button @click="sidebarOpen = true"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 lg:hidden"
                aria-label="Buka menu">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="min-w-0 flex-1">
                <h1 class="truncate text-base font-bold text-gray-900">{{ $heading ?? $title }}</h1>
                @if ($subtitle)
                    <p class="truncate text-xs text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>

            <div class="relative flex-shrink-0" x-data="{ open: false }" @keydown.escape="open = false">
                <button @click="open = !open" :aria-expanded="open"
                    class="flex items-center gap-2 rounded-lg px-2 py-1.5 transition-colors hover:bg-gray-100">
                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                        {{ Str::upper(Str::substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </span>
                    <span class="hidden max-w-[140px] truncate text-sm font-medium text-gray-700 sm:block">
                        {{ Auth::user()->name ?? 'Admin' }}
                    </span>
                    <i class="fa-solid fa-chevron-down text-[10px] text-gray-400"></i>
                </button>

                <div x-show="open" x-cloak x-transition @click.away="open = false"
                    class="absolute right-0 mt-2 w-52 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-card-hover">
                    <div class="border-b border-gray-100 px-4 py-3">
                        <p class="truncate text-sm font-semibold">{{ Auth::user()->name ?? 'Admin' }}</p>
                        <p class="truncate text-xs text-gray-500">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                    <a href="{{ route('logout') }}"
                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-primary-danger transition-colors hover:bg-red-50">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>Keluar
                    </a>
                </div>
            </div>
        </header>

        <main class="p-4 md:p-6">
            <x-alert.flash />
            {{ $slot }}
        </main>
    </div>

    @stack('scripts')
</body>

</html>
