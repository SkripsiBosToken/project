@php
    // Guard: office_address bisa kosong atau tidak lengkap pada instalasi
    // baru, dan sebelumnya langsung diakses sehingga halaman error.
    $address = json_decode($setting->office_address ?? '{}', true) ?: [];
    $hasCoordinates = isset($address['latitude'], $address['longitude'])
        && $address['latitude'] !== '' && $address['longitude'] !== '';

    $pins = $hasCoordinates
        ? [['lat' => (float) $address['latitude'], 'lng' => (float) $address['longitude'], 'label' => 'Kantor']]
        : [];

    $phone = $setting['phone_number'] ?? '';
    $whatsapp = preg_replace('/^0/', '62', $phone);
@endphp

@php
    use App\Support\Seo;

    $schema = Seo::breadcrumbs([
        'Beranda' => route('home'),
        'Kontak' => route('contact'),
    ]);
@endphp

<x-layout.customer title="Kontak & Lokasi | Kusuka Catering Malang"
    description="Hubungi Kusuka Catering Malang via WhatsApp {{ $setting['phone_number'] ?? '' }}. Lihat alamat kantor, peta lokasi, dan media sosial kami."
    :canonical="route('contact')" :schema="$schema">

    <div class="my-8 md:my-12">
        <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Hubungi Kami</h1>
        <p class="mt-1 text-sm text-gray-500">Kami siap membantu kebutuhan catering acara Anda.</p>

        <div class="mt-8 grid gap-6 lg:grid-cols-3">

            {{-- Info kontak --}}
            <div class="space-y-4 lg:col-span-1">
                @if ($phone)
                    <a href="https://wa.me/{{ $whatsapp }}" target="_blank" rel="noopener"
                        class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-card transition hover:shadow-card-hover">
                        <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-[#25D366]/10 text-[#25D366]">
                            <i class="fa-brands fa-whatsapp text-lg"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900">WhatsApp</p>
                            <p class="mt-0.5 break-all text-sm text-gray-500">{{ $phone }}</p>
                            <p class="mt-1 text-xs font-semibold text-primary">Chat sekarang →</p>
                        </div>
                    </a>
                @endif

                @if (! empty($address['address']))
                    <div class="flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-5 shadow-card">
                        <span class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-lg bg-primary-50 text-primary">
                            <i class="fa-solid fa-location-dot text-lg"></i>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900">Alamat Kantor</p>
                            <p class="mt-0.5 text-sm leading-relaxed text-gray-500">{{ $address['address'] }}</p>
                        </div>
                    </div>
                @endif

                @if (! empty($social_medias))
                    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-card">
                        <p class="text-sm font-bold text-gray-900">Media Sosial</p>
                        <div class="mt-3 space-y-2">
                            @foreach ($social_medias as $social_media)
                                <a href="{{ $social_media['href'] }}" target="_blank" rel="noopener"
                                    class="flex items-center gap-3 rounded-lg px-2 py-2 transition hover:bg-primary-50">
                                    <img src="{{ $social_media['logo'] }}" alt="" class="h-6 w-6 object-contain">
                                    <span class="text-sm text-gray-700">{{ $social_media['name'] }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square ml-auto text-xs text-gray-300"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Peta --}}
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h2 class="flex items-center gap-2 text-base font-bold text-gray-900">
                            <i class="fa-solid fa-map-location-dot text-primary"></i>Lokasi Kantor
                        </h2>
                    </div>

                    @if ($hasCoordinates)
                        <x-map.custom :pinArea="$pins" />
                    @else
                        <x-ui.empty icon="fa-map-pin" title="Lokasi belum diatur"
                            message="Koordinat kantor belum diisi pada pengaturan sistem." />
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-layout.customer>
