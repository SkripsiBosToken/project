@php
    $address = json_decode($data['address'], true) ?: [];
@endphp

<x-layout.customer title="Profil Saya | Kusuka Catering" :noindex="true">

    <div class="my-8 md:my-12">
        <h1 class="mb-6 text-2xl font-bold text-gray-900 md:text-3xl">Profil Saya</h1>

        <div class="grid gap-6 lg:grid-cols-3">

            {{-- Kartu ringkas --}}
            <aside class="lg:col-span-1">
                <div class="lg:sticky lg:top-24 space-y-4">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 text-center shadow-card">
                        <span class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-primary text-2xl font-bold text-white">
                            {{ Str::upper(Str::substr($data['name'], 0, 1)) }}
                        </span>
                        <h2 class="mt-4 truncate font-bold text-gray-900">{{ $data['name'] }}</h2>
                        <p class="mt-0.5 truncate text-sm text-gray-500">{{ $data['email'] }}</p>

                        <div class="mt-4 rounded-lg bg-primary-50 px-4 py-3">
                            <p class="text-xs text-gray-500">Poin Loyalitas</p>
                            <p class="mt-0.5 text-2xl font-bold text-primary">{{ number_format($data['point'] ?? 0, 0, ',', '.') }}</p>
                            <p class="mt-1 text-[11px] text-gray-400">1 poin per Rp100.000 belanja</p>
                        </div>
                    </div>

                    <x-ui.button href="{{ route('order-list') }}" variant="outline" block icon="fa-receipt">
                        Lihat Pesanan Saya
                    </x-ui.button>
                </div>
            </aside>

            {{-- Formulir --}}
            <div class="lg:col-span-2">
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6" x-data="profileMap({
                    latitude: {{ (float) ($address['latitude'] ?? -7.9666) }},
                    longitude: {{ (float) ($address['longitude'] ?? 112.6326) }},
                })">
                    @csrf

                    <x-ui.card title="Data Diri" icon="fa-user">
                        <div class="space-y-4">
                            <div class="grid gap-4 sm:grid-cols-2">
                                <x-ui.input name="name" label="Nama Lengkap" :value="$data['name']" required
                                    icon="fa-user" autocomplete="name" />

                                {{-- type="tel" agar angka 0 di depan nomor telepon
                                     tidak dibuang seperti pada input number. --}}
                                <x-ui.input name="phone" type="tel" label="Nomor Telepon"
                                    :value="$data['phone_number']" required icon="fa-phone"
                                    inputmode="numeric" pattern="[0-9]{9,15}" autocomplete="tel" />
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">
                                <x-ui.input name="birth_date" type="date" label="Tanggal Lahir"
                                    :value="$data['birth_date']" max="{{ now()->subYears(10)->format('Y-m-d') }}" />

                                <x-ui.input name="postal_code" type="text" label="Kode Pos"
                                    :value="$address['postal_code'] ?? ''" icon="fa-map-pin"
                                    inputmode="numeric" pattern="[0-9]{5}" maxlength="5" />
                            </div>
                        </div>
                    </x-ui.card>

                    <x-ui.card title="Alamat Pengiriman" icon="fa-location-dot"
                        subtitle="Titik lokasi ini dipakai untuk menghitung ongkos kirim.">
                        <div class="space-y-4">
                            <x-ui.textarea name="address" label="Alamat Lengkap" rows="2" required
                                :value="$address['address'] ?? ''"
                                placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan" />

                            <div>
                                <label for="search-address" class="mb-1.5 block text-sm font-medium text-gray-700">
                                    Cari &amp; Tandai Lokasi
                                </label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                                        <input id="search-address" type="text" x-model="query"
                                            @keydown.enter.prevent="geocode()" placeholder="Cari nama jalan atau daerah…"
                                            class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-3 text-sm transition focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                                    </div>
                                    <button type="button" @click="geocode()" :disabled="searching || !query"
                                        class="flex items-center gap-2 whitespace-nowrap rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-900 disabled:cursor-not-allowed disabled:opacity-50">
                                        <i class="fa-solid" :class="searching ? 'fa-circle-notch fa-spin' : 'fa-magnifying-glass'"></i>
                                        Cari
                                    </button>
                                </div>

                                <button type="button" @click="useMyLocation()" :disabled="locating"
                                    class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline disabled:opacity-50">
                                    <i class="fa-solid" :class="locating ? 'fa-circle-notch fa-spin' : 'fa-location-crosshairs'"></i>
                                    Gunakan lokasi saya saat ini
                                </button>

                                <p x-show="error" x-cloak class="mt-2 text-xs text-primary-danger" x-text="error"></p>

                                <div class="relative mt-3">
                                    <div id="map" class="h-72 w-full rounded-lg border border-gray-200"></div>
                                    <div x-show="loadingMap" x-cloak
                                        class="absolute inset-0 flex flex-col items-center justify-center rounded-lg bg-gray-50">
                                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-primary"></div>
                                        <p class="mt-3 text-sm text-gray-500">Memuat peta…</p>
                                    </div>
                                </div>

                                <p class="mt-2 text-xs text-gray-500">
                                    <i class="fa-solid fa-circle-info mr-1"></i>
                                    Klik peta atau geser penanda untuk menyesuaikan titik lokasi.
                                </p>

                                <input type="hidden" name="latitude" x-model="latitude">
                                <input type="hidden" name="longitude" x-model="longitude">
                            </div>
                        </div>
                    </x-ui.card>

                    <div class="flex justify-end gap-3">
                        <x-ui.button href="{{ route('home') }}" variant="muted">Batal</x-ui.button>
                        <x-ui.button type="submit" icon="fa-floppy-disk">Simpan Perubahan</x-ui.button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>
            function profileMap(config) {
                return {
                    map: null,
                    marker: null,
                    query: '',
                    latitude: config.latitude,
                    longitude: config.longitude,
                    loadingMap: true,
                    searching: false,
                    locating: false,
                    error: '',

                    init() {
                        this.$nextTick(() => this.loadLeaflet());
                    },

                    loadLeaflet() {
                        if (document.querySelector('link[data-leaflet]')) {
                            this.initMap();
                            return;
                        }

                        const css = document.createElement('link');
                        css.rel = 'stylesheet';
                        css.href = 'https://unpkg.com/leaflet/dist/leaflet.css';
                        css.dataset.leaflet = 'true';
                        css.onload = () => this.initMap();
                        css.onerror = () => {
                            this.loadingMap = false;
                            this.error = 'Peta gagal dimuat. Koordinat lama tetap tersimpan.';
                        };
                        document.head.appendChild(css);
                    },

                    initMap() {
                        const element = document.getElementById('map');
                        if (!element || typeof L === 'undefined') {
                            this.loadingMap = false;
                            this.error = 'Peta tidak tersedia saat ini.';
                            return;
                        }

                        this.map = L.map(element).setView([this.latitude, this.longitude], 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors',
                        }).addTo(this.map);

                        this.marker = L.marker([this.latitude, this.longitude], { draggable: true }).addTo(this.map);

                        this.marker.on('dragend', (event) => {
                            const { lat, lng } = event.target.getLatLng();
                            this.setPosition(lat, lng, false);
                        });

                        this.map.on('click', (event) => {
                            this.setPosition(event.latlng.lat, event.latlng.lng);
                        });

                        this.loadingMap = false;
                    },

                    setPosition(lat, lng, recenter = true) {
                        this.latitude = lat;
                        this.longitude = lng;
                        this.error = '';

                        if (this.marker) this.marker.setLatLng([lat, lng]);
                        if (recenter && this.map) this.map.setView([lat, lng], 16);
                    },

                    async geocode() {
                        if (!this.query) return;

                        this.searching = true;
                        this.error = '';

                        try {
                            const response = await fetch(
                                `https://nominatim.openstreetmap.org/search?format=json&limit=1&q=${encodeURIComponent(this.query)}`
                            );
                            const results = await response.json();

                            if (results.length) {
                                this.setPosition(parseFloat(results[0].lat), parseFloat(results[0].lon));
                            } else {
                                this.error = 'Alamat tidak ditemukan. Coba kata kunci lain atau tandai manual di peta.';
                            }
                        } catch (e) {
                            this.error = 'Pencarian gagal. Periksa koneksi internet Anda.';
                        } finally {
                            this.searching = false;
                        }
                    },

                    useMyLocation() {
                        if (!navigator.geolocation) {
                            this.error = 'Browser Anda tidak mendukung deteksi lokasi.';
                            return;
                        }

                        this.locating = true;
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.setPosition(position.coords.latitude, position.coords.longitude);
                                this.locating = false;
                            },
                            () => {
                                this.error = 'Tidak bisa mengakses lokasi. Izinkan akses lokasi atau tandai manual di peta.';
                                this.locating = false;
                            }
                        );
                    },
                };
            }
        </script>
    @endpush

</x-layout.customer>
