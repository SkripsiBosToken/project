<x-layout.customer>
    <div class="my-10 md:my-14" x-data="mapData()" x-init="init()">
        <x-form.custom action="{{ route('profile.update') }}" method="post">
            <div class="grid md:grid-cols-2 gap-x-4">
                <div class="mb-4">
                    <label for="name">Nama Lengkap</label>
                    <input name="name" type="text" placeholder="Name" class="w-full p-2 border rounded"
                        value="{{ $data['name'] }}" required>
                </div>
                <div class="mb-4">
                    <label for="name">Nomor Telpon</label>
                    <input name="phone" type="number" placeholder="Nomor Telpon" class="w-full p-2 border rounded"
                        value="{{ $data['phone_number'] }}" required>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-x-4">

                <div class="mb-4">
                    <label for="name">Tahun Tanggal Lahir</label>
                    <input name="birth_date" type="date" placeholder="Tahun Tanggal Lahir"
                        class="w-full p-2 border rounded" value="{{ $data['birth_date'] }}" required>
                </div>
                <div class="mb-4">
                    <label for="name">Kode Pos</label>
                    <input name="postal_code" type="number" placeholder="Kode Pos" class="w-full p-2 border rounded"
                        value="{{ json_decode($data['address'], true)['postal_code'] }}" required>
                </div>
            </div>
            <div class="mb-4">
                <label for="name">Alamat Lengkap</label>
                <textarea name="address" id="alamat" rows="2" placeholder="Alamat" class="w-full p-2 border rounded" required>{{ json_decode($data['address'], true)['address'] }}</textarea>
            </div>

            <div class="">
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
                        <x-button.custom
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded whitespace-nowrap"
                            @click="geocodeAddressReset()">
                            Reset
                        </x-button.custom>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="mb-4 hidden">
                        <label for="latitude" class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input id="latitude" name="latitude" x-model="latitude"
                            class="w-full p-2 border rounded flex-1">
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
                    <div id="map" class="mb-4 h-80 shadow-sm rounded-lg"></div>
                </div>
            </div>
            <div class="mt-8 ">
                <x-button.custom
                    class="w-full text-md md:text-xl bg-primary text-white py-3 rounded hover:bg-white hover:text-primary text-center"
                    type="submit">
                    Update
                </x-button.custom>
            </div>
        </x-form.custom>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        function mapData() {
            return {
                map: null,
                marker: null,
                address: '',
                latitude: {{ json_decode($data['address'], true)['latitude'] ?? -6.2088 }},
                longitude: {{ json_decode($data['address'], true)['longitude'] ?? 106.8456 }},
                selectedAddress: '',
                foundAddress: false,
                isLoading: true,

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

                geocodeAddressReset() {
                    const lat = {{ json_decode($data['address'], true)['latitude'] }};
                    const lon = {{ json_decode($data['address'], true)['longitude'] }};

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.display_name) {
                                this.latitude = lat;
                                this.longitude = lon;
                                this.selectedAddress = data.display_name;
                                this.address = data.display_name;
                                this.foundAddress = true;

                                this.updateLocation({
                                    lat: this.latitude,
                                    lng: this.longitude
                                });

                            } else {
                                alert("Alamat tidak ditemukan dari koordinat.");
                            }
                        })
                        .catch(error => {
                            console.error("Reverse geocoding error:", error);
                        });
                },

                useAddress() {
                    this.foundAddress = false;
                }
            };
        }
    </script>

</x-layout.customer>
