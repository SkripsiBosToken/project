@php
    // $setting dibagikan lewat view composer di AppServiceProvider.
    $links = [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'Tentang Kami', 'route' => 'about'],
        ['label' => 'Katalog', 'route' => 'catalogue'],
        ['label' => 'Kontak', 'route' => 'contact'],
    ];

    // Jumlah item keranjang untuk badge di ikon.
    $cartCount = 0;
    if (Auth::check()) {
        $cartCount = \App\Models\Cart_Item::whereHas(
            'cart',
            fn ($q) => $q->where('user_id', Auth::id())
        )->sum('qty');
    }
@endphp

{{-- Latar putih dipasang pada <header> yang selebar layar, sementara isinya
     dibatasi container di dalam — pola yang sama dengan footer. Sebelumnya
     latar ini berada DI DALAM container, sehingga sisi kiri dan kanan navbar
     tampak bolong memperlihatkan warna body. --}}
<header x-data="{ mobileOpen: false, scrolled: false }" @scroll.window="scrolled = window.scrollY > 8"
    class="sticky top-0 z-50 w-full border-b border-gray-100 transition-colors duration-200"
    :class="scrolled ? 'bg-white/95 shadow-card backdrop-blur' : 'bg-white'">

    <div class="container mx-auto px-4 md:px-0">

    <nav class="flex h-20 items-center justify-between gap-4" aria-label="Navigasi utama">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex flex-shrink-0 items-center" aria-label="Beranda Kusuka Catering">
            <img src="{{ $setting['logo'] ?? '' }}" alt="{{ $setting['name'] ?? 'Kusuka Catering' }}"
                class="h-14 w-auto object-contain md:h-16">
        </a>

        {{-- Menu desktop --}}
        <div class="hidden items-center gap-8 md:flex">
            @foreach ($links as $link)
                @php $active = request()->routeIs($link['route']); @endphp
                <a href="{{ route($link['route']) }}" @if ($active) aria-current="page" @endif
                    class="relative py-1 text-sm font-semibold transition-colors lg:text-base
                        {{ $active ? 'text-primary' : 'text-gray-600 hover:text-primary' }}">
                    {{ $link['label'] }}
                    @if ($active)
                        <span class="absolute -bottom-0.5 left-0 h-0.5 w-full rounded-full bg-primary"></span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Aksi kanan --}}
        <div class="flex items-center gap-2 md:gap-4">
            @auth
                {{-- Keranjang dengan badge jumlah --}}
                <a href="{{ route('cart') }}" aria-label="Keranjang belanja"
                    class="relative flex h-10 w-10 items-center justify-center rounded-lg text-gray-600 transition-colors hover:bg-primary-50 hover:text-primary">
                    <i class="fa-solid fa-cart-shopping"></i>
                    @if ($cartCount > 0)
                        <span
                            class="absolute -right-0.5 -top-0.5 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-primary px-1 text-[10px] font-bold text-white">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- Menu akun --}}
                <div class="relative hidden md:block" x-data="{ open: false }" @keydown.escape="open = false">
                    <button @click="open = !open" :aria-expanded="open"
                        class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-primary-50 hover:text-primary">
                        <span
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-primary text-xs font-bold text-white">
                            {{ Str::upper(Str::substr(Auth::user()->name, 0, 1)) }}
                        </span>
                        <span class="max-w-[120px] truncate">{{ Auth::user()->name }}</span>
                        <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="open && 'rotate-180'"></i>
                    </button>

                    <div x-show="open" x-transition @click.away="open = false" x-cloak
                        class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-gray-200 bg-white py-1 shadow-card-hover">
                        <div class="border-b border-gray-100 px-4 py-3">
                            <p class="truncate text-sm font-semibold">{{ Auth::user()->name }}</p>
                            <p class="truncate text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>

                        @foreach ([['order-list', 'fa-receipt', 'Pesanan Saya'], ['profile', 'fa-user', 'Profil']] as [$r, $i, $l])
                            <a href="{{ route($r) }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 transition-colors hover:bg-primary-50 hover:text-primary">
                                <i class="fa-solid {{ $i }} w-4 text-center text-gray-400"></i>{{ $l }}
                            </a>
                        @endforeach

                        <a href="{{ route('logout') }}"
                            class="flex items-center gap-3 border-t border-gray-100 px-4 py-2.5 text-sm text-primary-danger transition-colors hover:bg-red-50">
                            <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>Keluar
                        </a>
                    </div>
                </div>
            @else
                <x-ui.button href="{{ route('login') }}" size="sm" icon="fa-user" class="hidden md:inline-flex">
                    Masuk
                </x-ui.button>
            @endauth

            {{-- Tombol menu mobile --}}
            <button @click="mobileOpen = !mobileOpen" :aria-expanded="mobileOpen" aria-label="Buka menu"
                class="flex h-10 w-10 items-center justify-center rounded-lg text-gray-700 transition-colors hover:bg-primary-50 hover:text-primary md:hidden">
                <i class="fa-solid" :class="mobileOpen ? 'fa-xmark' : 'fa-bars'"></i>
            </button>
        </div>
    </nav>

    {{-- Drawer mobile --}}
    <div x-show="mobileOpen" x-collapse x-cloak class="border-t border-gray-100 pb-4 md:hidden">
        <div class="flex flex-col pt-2">
            @foreach ($links as $link)
                @php $active = request()->routeIs($link['route']); @endphp
                <a href="{{ route($link['route']) }}"
                    class="rounded-lg px-3 py-3 text-sm font-semibold transition-colors
                        {{ $active ? 'bg-primary-50 text-primary' : 'text-gray-700 hover:bg-gray-50' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <div class="mt-2 border-t border-gray-100 pt-2">
                @auth
                    <a href="{{ route('order-list') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        <i class="fa-solid fa-receipt w-4 text-center text-gray-400"></i>Pesanan Saya
                    </a>
                    <a href="{{ route('profile') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        <i class="fa-solid fa-user w-4 text-center text-gray-400"></i>Profil
                    </a>
                    <a href="{{ route('logout') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-semibold text-primary-danger hover:bg-red-50">
                        <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>Keluar
                    </a>
                @else
                    <x-ui.button href="{{ route('login') }}" block icon="fa-user" class="mt-2">Masuk</x-ui.button>
                @endauth
            </div>
        </div>
    </div>

    </div>
</header>
