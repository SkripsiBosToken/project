<x-layout.admin-v2>
    <form action="{{ route('system.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="content mt-3">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><strong class="card-title">Pengaturan Umum</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Nama Website</label>
                                <input type="text" class="form-control" name="name" value="{{ $system['name'] }}">
                            </div>
                            <div class="form-group">
                                <label>Logo Saat Ini</label><br>
                                <img id="logo-preview" src="{{ $system['logo'] }}" alt="Logo Saat Ini" height="80"
                                    width="80" class="mb-2 border rounded">

                                <label for="logo">Ubah Logo <small class="text-danger">* Disarankan berukuran
                                        (250x100) atau (160x160) pixel</small></label>
                                <input type="file" class="form-control" name="logo" id="logo"
                                    accept="image/*" onchange="previewLogo(event)">
                            </div>

                            <div class="form-group">
                                <label>Nomor Telpon</label>
                                <input type="number" class="form-control" name="phone_number"
                                    value="{{ $system['phone_number'] }}">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" name="address">{{ json_decode($system['office_address'], true)['address'] }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Kode Pos</label>
                                <input type="number" class="form-control" name="postal_code"
                                    value="{{ json_decode($system['office_address'], true)['postal_code'] }}">
                            </div>

                            <div class="form-group">
                                <label>Cari Lokasi Kantor</label>
                                <div class="d-flex gap-2">
                                    <input type="text" id="search-office-address" class="form-control"
                                        placeholder="Cari lokasi..." />
                                    <button type="button" class="btn btn-info"
                                        onclick="geocodeOfficeAddress()">Cari</button>
                                    <button type="button" class="btn btn-secondary"
                                        onclick="resetToOriginal()">Reset</button>
                                </div>

                            </div>

                            <input type="hidden" id="latitude" name="latitude"
                                value="{{ json_decode($system['office_address'], true)['latitude'] }}">
                            <input type="hidden" id="longitude" name="longitude"
                                value="{{ json_decode($system['office_address'], true)['longitude'] }}">

                            <div class="form-group">
                                <label for="cc-payment" class="control-label mb-1">Address Point</label>
                                @php
                                    $officeAddress = [];
                                    $address = json_decode($system['office_address'], true);
                                    $data = [
                                        'lat' => (float) $address['latitude'],
                                        'lng' => (float) $address['longitude'],
                                        'label' => 'Origin',
                                    ];
                                    array_push($officeAddress, $data);
                                @endphp

                                @if (is_array($officeAddress))
                                    <div id="map" class="mb-4"
                                        style="height: 300px; border-radius: 8px; overflow: hidden;"></div>
                                @else
                                    <p>Data lokasi kantor tidak valid atau tidak tersedia.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header"><strong class="card-title">Tujuan</strong></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Visi</label>
                                <textarea class="form-control" name="visi">{{ $system['visi'] }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Misi</label>
                                <textarea class="form-control" name="misi">{{ $system['misi'] }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-floppy-o mr-2" aria-hidden="true"></i>
                Simpan</button>
        </div>
    </form>
</x-layout.admin-v2>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
    let map, marker;
    const originalLat = parseFloat(document.getElementById("latitude").value) || -6.2;
    const originalLng = parseFloat(document.getElementById("longitude").value) || 106.8;


    function resetToOriginal() {
        document.getElementById("latitude").value = originalLat;
        document.getElementById("longitude").value = originalLng;

        map.setView([originalLat, originalLng], 13);
        marker.setLatLng([originalLat, originalLng]);

        document.getElementById("search-office-address").value = "";
    }

    document.addEventListener('DOMContentLoaded', function() {
        const lat = parseFloat(document.getElementById("latitude").value) || -6.2;
        const lng = parseFloat(document.getElementById("longitude").value) || 106.8;

        map = L.map('map').setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([lat, lng], {
            draggable: true
        }).addTo(map);
        marker.on("dragend", function(event) {
            const position = event.target.getLatLng();
            document.getElementById("latitude").value = position.lat;
            document.getElementById("longitude").value = position.lng;
        });

        map.on("click", function(e) {
            const {
                lat,
                lng
            } = e.latlng;
            marker.setLatLng([lat, lng]);
            document.getElementById("latitude").value = lat;
            document.getElementById("longitude").value = lng;
        });
    });

    function geocodeOfficeAddress() {
        const query = document.getElementById("search-office-address").value;
        if (!query) return;

        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    const {
                        lat,
                        lon
                    } = data[0];
                    document.getElementById("latitude").value = lat;
                    document.getElementById("longitude").value = lon;

                    map.setView([lat, lon], 15);
                    marker.setLatLng([lat, lon]);
                } else {
                    alert("Lokasi tidak ditemukan");
                }
            })
            .catch(err => {
                alert("Terjadi kesalahan saat mencari lokasi");
                console.error(err);
            });
    }

    function previewLogo(event) {
        const input = event.target;
        const preview = document.getElementById('logo-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
