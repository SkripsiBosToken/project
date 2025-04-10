<x-layout.auth>

    @if (session('error'))
        <div class="fixed top-8 left-8 px-4 py-4 bg-primary-danger text-white p-4 rounded-lg shadow-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="flex flex-col justify-center px-8 md:py-0" x-data="mapData()">
        <div class="text-2xl font-bold mb-4 md:text-left text-center">
            Hello Again!
        </div>
        <div class="text-lg mb-8 md:text-left text-center">
            Sign Up to Get Started
        </div>
        <div class="w-full">
            <x-form.custom action="{{ route('register') }}" method="post">
                <div class="grid md:grid-cols-2 gap-x-4">
                    <div class="mb-4">
                        <input name="name" type="text" placeholder="Name" class="w-full p-2 border rounded"
                            required>
                    </div>
                    <div class="mb-4">
                        <input name="username" type="text" placeholder="Username" class="w-full p-2 border rounded"
                            required>
                    </div>
                </div>
                <div class="grid md:grid-cols-2 gap-x-4">
                    <div class="mb-4">
                        <input name="email" type="email" placeholder="Email Address"
                            class="w-full p-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <input name="password" type="password" placeholder="Password" class="w-full p-2 border rounded"
                            required>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-x-4">
                    <div class="mb-4">
                        <input name="phone" type="number" placeholder="Nomor Telpon"
                            class="w-full p-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <input name="birth_date" type="date" placeholder="Tahun Tanggal Lahir"
                            class="w-full p-2 border rounded" required>
                        <label for="alamat" class="block text-sm font-medium text-gray-700"><span
                                class="text-primary-danger text-xs">*Tahun Tanggal Lahir</span></label>

                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-x-4">

                    <div class="mb-4">
                        <input name="postal_code" type="number" placeholder="Kode Pos"
                            class="w-full p-2 border rounded" required>
                    </div>
                    <div class="mb-4">
                        <textarea name="address" id="alamat" rows="2" placeholder="Alamat" class="w-full p-2 border rounded"></textarea>
                        <label for="alamat" class="block text-sm font-medium text-gray-700"><span
                                class="text-primary-danger text-xs">*Pastikan detail alamat sudah lengkap</span></label>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="search-address" class="block text-sm font-medium text-gray-700">
                        Cari Daerah <span class="text-primary-danger text-xs">*Pastikan titik sudah benar</span>
                    </label>
                    <div class="flex gap-2">
                        <input id="search-address" type="text" x-model="address" placeholder="Cari Alamat"
                            class="w-full p-2 border rounded flex-1">
                        <x-button.custom
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap"
                            @click="geocodeAddress()">
                            Cari
                        </x-button.custom>
                    </div>
                </div>

                <div class="mb-4 hidden">
                    <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                    <input id="latitude" name="latitude" x-model="latitude" class="w-full p-2 border rounded flex-1">
                </div>

                <div class="mb-4 hidden">
                    <label for="longitude" class="block text-sm font-medium text-gray-700">Longitude</label>
                    <input id="longitude" name="longitude" type="hidden" x-model="longitude"
                        class="w-full p-2 border rounded flex-1">
                </div>

                <div id="loading-screen" class="text-center flex flex-row justify-center gap-x-2">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-gray-300 border-t-primary">
                    </div>
                    <p class="mt-4 text-lg font-semibold text-gray-700">Memuat Peta...</p>
                </div>
                <div id="map" class="mb-4 h-40 shadow-sm rounded-lg"></div>

                <div class="mt-8 grid grid-flow-col grid-rows-4 gap-2">
                    <x-button.custom
                        class="w-full text-md md:text-xl bg-primary text-white py-3 rounded hover:bg-white hover:text-primary text-center"
                        type="submit">
                        Sign Up
                    </x-button.custom>
                </div>
            </x-form.custom>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        function mapData() {
            return {
                map: null,
                marker: null,
                address: '',
                latitude: -6.2088,
                longitude: 106.8456,
                selectedAddress: '',
                foundAddress: false,
                isLoading: true, // Tambahkan isLoading

                init() {
                    this.$nextTick(() => {
                        this.loadLeafletStyles();
                    });
                },

                loadLeafletStyles() {
                    const leafletCss = document.createElement("link");
                    leafletCss.rel = "stylesheet";
                    leafletCss.href = "https://unpkg.com/leaflet/dist/leaflet.css";
                    leafletCss.onload = () => {
                        this.initMap();
                    };

                    document.head.appendChild(leafletCss);
                },

                initMap() {
                    if (!document.getElementById("map")) {
                        console.error("Elemen #map tidak ditemukan!");
                        return;
                    }

                    if (this.map) {
                        this.map.remove();
                    }

                    this.map = L.map("map").setView([this.latitude, this.longitude], 13);

                    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                        attribution: "© OpenStreetMap contributors"
                    }).addTo(this.map);

                    this.marker = L.marker([this.latitude, this.longitude], {
                        draggable: true
                    }).addTo(this.map);

                    this.marker.on("dragend", (event) => {
                        const {
                            lat,
                            lng
                        } = event.target.getLatLng();
                        this.latitude = lat;
                        this.longitude = lng;
                    });

                    this.map.on("click", (event) => {
                        this.updateLocation(event.latlng);
                    });

                    // Sembunyikan loading setelah Leaflet selesai dimuat
                    this.isLoading = false;
                    document.getElementById("loading-screen").style.display = "none";
                },

                updateLocation(location) {
                    if (this.marker) {
                        this.marker.setLatLng(location);
                    }
                    this.latitude = location.lat;
                    this.longitude = location.lng;
                    this.map.setView(location, 15);
                },

                geocodeAddress() {
                    let searchQuery = this.address;
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${searchQuery}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                let location = data[0];
                                this.latitude = parseFloat(location.lat);
                                this.longitude = parseFloat(location.lon);
                                this.selectedAddress = location.display_name;
                                this.foundAddress = true;
                                this.$nextTick(() => this.initMap());
                            } else {
                                alert("Alamat tidak ditemukan.");
                                this.foundAddress = false;
                            }
                        });
                },

                useAddress() {
                    this.foundAddress = false;
                }
            };
        }
    </script>

</x-layout.auth>
