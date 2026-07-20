@php
    // $setting dibagikan lewat view composer di AppServiceProvider.
    // Guard null: kolom JSON bisa kosong pada instalasi baru dan sebelumnya
    // membuat foreach di bawah error.
    $socials = json_decode($setting['social_media'] ?? '[]', true) ?: [];
    $office = json_decode($setting['office_address'] ?? '{}', true) ?: [];
    $phone = $setting['phone_number'] ?? '';
    $whatsapp = preg_replace('/^0/', '62', $phone);
@endphp

<footer class="mt-16 border-t border-gray-200 bg-white font-poppins">
    <div class="container mx-auto px-4 py-12 md:px-0">
        <div class="grid gap-8 md:grid-cols-4">

            {{-- Brand --}}
            <div class="md:col-span-1">
                <img src="{{ $setting['logo'] ?? '' }}" alt="{{ $setting['name'] ?? 'Kusuka Catering' }}"
                    class="h-14 w-auto object-contain">
                <p class="mt-3 text-sm leading-relaxed text-gray-500">
                    Catering Malang dengan menu lezat, fresh, dan siap diantar ke lokasi Anda.
                </p>
            </div>

            {{-- Navigasi --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-900">Jelajahi</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach ([['home', 'Beranda'], ['catalogue', 'Katalog'], ['about', 'Tentang Kami'], ['contact', 'Kontak']] as [$r, $l])
                        <li>
                            <a href="{{ route($r) }}"
                                class="text-gray-500 transition-colors hover:text-primary">{{ $l }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Legal --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-900">Informasi</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach (['Kebijakan Privasi', 'Syarat & Ketentuan', 'FAQ'] as $item)
                        <li>
                            <a href="#" class="text-gray-500 transition-colors hover:text-primary">{{ $item }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Kontak --}}
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wide text-gray-900">Hubungi Kami</h3>

                @if ($phone)
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener"
                        class="mt-3 flex items-start gap-2.5 text-sm text-gray-500 transition-colors hover:text-primary">
                        <i class="fa-brands fa-whatsapp mt-0.5 text-base"></i>
                        <span>{{ $phone }}</span>
                    </a>
                @endif

                @if (! empty($office['address']))
                    <p class="mt-2 flex items-start gap-2.5 text-sm leading-relaxed text-gray-500">
                        <i class="fa-solid fa-location-dot mt-0.5"></i>
                        <span>{{ $office['address'] }}</span>
                    </p>
                @endif

                @if ($socials)
                    <div class="mt-4 flex items-center gap-3">
                        @foreach ($socials as $item)
                            <a href="{{ $item['href'] ?? '#' }}" target="_blank" rel="noopener"
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-100 transition-colors hover:bg-primary-50"
                                aria-label="Media sosial">
                                <img src="{{ $item['logo'] ?? '' }}" alt="" class="h-4 w-4 object-contain">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-10 border-t border-gray-100 pt-6 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ $setting['name'] ?? 'Kusuka Catering' }}. Seluruh hak cipta dilindungi.
        </div>
    </div>
</footer>
