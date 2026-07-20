<x-layout.auth title="Daftar | Kusuka Catering">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 md:text-3xl">Buat Akun Baru</h1>
        <p class="mt-2 text-sm text-gray-500">Daftar untuk mulai memesan catering.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6" x-data="registerForm()">
        @csrf

        {{-- Data akun --}}
        <fieldset class="space-y-4">
            <legend class="mb-1 text-sm font-bold text-gray-900">Data Akun</legend>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-ui.input name="name" label="Nama Lengkap" icon="fa-user" placeholder="Nama lengkap" required
                    autocomplete="name" />
                <x-ui.input name="username" label="Username" icon="fa-at" placeholder="Username" required
                    autocomplete="username" />
            </div>

            <x-ui.input name="email" type="email" label="Email" icon="fa-envelope" placeholder="nama@email.com"
                required autocomplete="email" />

            <div>
                <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Password <span class="text-primary-danger">*</span>
                </label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <i class="fa-solid fa-lock text-sm"></i>
                    </span>
                    <input :type="showPassword ? 'text' : 'password'" name="password" id="password" required
                        x-model="password" autocomplete="new-password" placeholder="Minimal 8 karakter"
                        class="w-full rounded-lg border border-gray-300 py-2.5 pl-10 pr-10 text-sm transition placeholder:text-gray-400 focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 transition hover:text-primary"
                        :aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'">
                        <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                    </button>
                </div>

                {{-- Indikator kekuatan password --}}
                <div x-show="password.length > 0" x-cloak class="mt-2">
                    <div class="flex gap-1">
                        <template x-for="level in 3" :key="level">
                            <div class="h-1 flex-1 rounded-full transition-colors"
                                :class="strength >= level ? strengthColour : 'bg-gray-200'"></div>
                        </template>
                    </div>
                    <p class="mt-1 text-xs" :class="strengthTextColour" x-text="strengthLabel"></p>
                </div>
            </div>
        </fieldset>

        {{-- Kontak --}}
        <fieldset class="space-y-4">
            <legend class="mb-1 text-sm font-bold text-gray-900">Kontak</legend>

            <div class="grid gap-4 sm:grid-cols-2">
                {{-- type="tel", bukan "number": input number membuang angka 0
                     di depan sehingga 08123… tersimpan sebagai 8123…. --}}
                <x-ui.input name="phone" type="tel" label="Nomor Telepon" icon="fa-phone" placeholder="08123456789"
                    required autocomplete="tel" inputmode="numeric" pattern="[0-9]{9,15}"
                    hint="Contoh: 08123456789" />

                <x-ui.input name="birth_date" type="date" label="Tanggal Lahir" required
                    max="{{ now()->subYears(10)->format('Y-m-d') }}" />
            </div>
        </fieldset>

        {{-- Alamat --}}
        <fieldset class="space-y-4">
            <legend class="mb-1 text-sm font-bold text-gray-900">Alamat Pengiriman</legend>

            <x-ui.textarea name="address" label="Alamat Lengkap" rows="2" required
                placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan"
                hint="Pastikan detail alamat sudah lengkap agar kurir mudah menemukan lokasi." />

            <x-ui.input name="postal_code" type="text" label="Kode Pos" icon="fa-map-pin" placeholder="65151"
                inputmode="numeric" pattern="[0-9]{5}" maxlength="5" />

            {{-- Pencarian lokasi --}}
            <div>
                <label for="search-address" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Tandai Lokasi di Peta
                </label>
                <div class="flex gap-2">
                    <div class="relative flex-1">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-400"></i>
                        <input id="search-address" type="text" x-model="query" @keydown.enter.prevent="geocode()"
                            placeholder="Cari nama jalan atau daerah…"
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
                    <div id="map" class="h-56 w-full rounded-lg border border-gray-200"></div>

                    <div x-show="loadingMap" x-cloak
                        class="absolute inset-0 flex flex-col items-center justify-center rounded-lg bg-gray-50">
                        <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-primary"></div>
                        <p class="mt-3 text-sm text-gray-500">Memuat peta…</p>
                    </div>
                </div>

                <p class="mt-2 text-xs text-gray-500">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    Klik peta atau geser penanda untuk menyesuaikan titik lokasi. Ongkos kirim dihitung dari titik ini.
                </p>

                <input type="hidden" name="latitude" x-model="latitude">
                <input type="hidden" name="longitude" x-model="longitude">
            </div>
        </fieldset>

        <x-ui.button type="submit" size="lg" block icon="fa-user-plus">Daftar Sekarang</x-ui.button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-semibold text-primary hover:underline">Masuk di sini</a>
    </p>

    @push('scripts')
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>
            function registerForm() {
                return {
                    map: null,
                    marker: null,
                    query: '',
                    // Default diarahkan ke Malang (area layanan), bukan Jakarta
                    // seperti sebelumnya, agar penanda tidak jauh dari pengguna.
                    latitude: -7.9666,
                    longitude: 112.6326,
                    loadingMap: true,
                    searching: false,
                    locating: false,
                    error: '',
                    showPassword: false,
                    password: '',

                    get strength() {
                        let score = 0;
                        if (this.password.length >= 8) score++;
                        if (/[A-Z]/.test(this.password) && /[a-z]/.test(this.password)) score++;
                        if (/\d/.test(this.password) || /[^A-Za-z0-9]/.test(this.password)) score++;
                        return score;
                    },

                    get strengthLabel() {
                        return ['Terlalu lemah', 'Lemah', 'Cukup', 'Kuat'][this.strength];
                    },

                    get strengthColour() {
                        return ['bg-red-400', 'bg-red-400', 'bg-amber-400', 'bg-green-500'][this.strength];
                    },

                    get strengthTextColour() {
                        return ['text-red-500', 'text-red-500', 'text-amber-600', 'text-green-600'][this.strength];
                    },

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
                            this.error = 'Peta gagal dimuat. Anda tetap bisa mendaftar, lalu atur lokasi di halaman profil.';
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

                        this.map = L.map(element).setView([this.latitude, this.longitude], 13);

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

                    // Memindahkan penanda saja; versi lama membangun ulang
                    // seluruh peta setiap kali pencarian dilakukan.
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

</x-layout.auth>
