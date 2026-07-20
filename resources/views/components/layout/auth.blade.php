@props([
    'title' => 'Masuk | Kusuka Catering',
])

@php
    // $setting dibagikan lewat view composer di AppServiceProvider.
    $phone = $setting['phone_number'] ?? '';
    $whatsapp = preg_replace('/^0/', '62', $phone);
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ $setting['logo'] ?? '' }}">
    <meta name="robots" content="noindex">

    <script src="https://kit.fontawesome.com/a47e0565cc.js" crossorigin="anonymous"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-poppins">
    <div class="grid min-h-screen lg:grid-cols-2">

        {{-- Panel merek --}}
        <div class="relative hidden overflow-hidden bg-gradient-to-br from-primary to-primary-secondary lg:block">
            <div class="relative z-10 flex h-full flex-col justify-between p-12">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-white/90 transition hover:text-white">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                    <span class="text-sm font-medium">Kembali ke beranda</span>
                </a>

                <div class="max-w-md">
                    <h1 class="text-4xl font-bold leading-tight text-white">
                        Hidangan Favorit Anda,<br>Segar &amp; Tepat Waktu
                    </h1>
                    <p class="mt-4 text-lg text-white/80">
                        Catering paling dipercaya di area Malang untuk acara besar maupun kecil.
                    </p>

                    <div class="mt-8 flex gap-6">
                        @foreach ([['fa-leaf', 'Bahan Fresh'], ['fa-truck-fast', 'Tepat Waktu'], ['fa-shield-halved', 'Aman']] as [$icon, $label])
                            <div class="flex items-center gap-2 text-white/90">
                                <i class="fa-solid {{ $icon }}"></i>
                                <span class="text-sm">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <p class="text-xs text-white/60">
                    &copy; {{ date('Y') }} {{ $setting['name'] ?? 'Kusuka Catering' }}
                </p>
            </div>

            <div class="absolute bottom-0 left-0 w-full">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" aria-hidden="true">
                    <path fill="#ffffff" fill-opacity="0.1"
                        d="M0,160L48,176C96,192,192,224,288,208C384,192,480,128,576,117.3C672,107,768,149,864,170.7C960,192,1056,192,1152,176C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                    </path>
                </svg>
            </div>
        </div>

        {{-- Panel formulir --}}
        <div class="flex items-center justify-center bg-white px-6 py-10 md:px-12">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-8 flex justify-center lg:hidden">
                    <img src="{{ $setting['logo'] ?? '' }}" alt="{{ $setting['name'] ?? 'Kusuka Catering' }}"
                        class="h-16 w-auto object-contain">
                </a>

                <x-alert.flash />

                {{ $slot }}
            </div>
        </div>
    </div>

    @if ($phone)
        <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener" aria-label="Hubungi kami via WhatsApp"
            class="fixed bottom-6 right-6 z-40 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-2xl text-white shadow-card-hover transition-transform duration-200 hover:scale-110">
            <i class="fa-brands fa-whatsapp"></i>
        </a>
    @endif

    @stack('scripts')
</body>

</html>
