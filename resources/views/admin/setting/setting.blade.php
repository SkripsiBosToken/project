<x-layout.admin-v2>
    <form action="{{ route('system.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="content my-3">
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
                                <img id="logo-preview" src="{{ $system['logo'] }}" alt="Logo Saat Ini" height="80" width="80" class="mb-2 border rounded">

                                <label for="logo">Ubah Logo <small class="text-danger">* Disarankan berukuran (250x100) atau (160x160) pixel</small></label>
                                <input type="file" class="form-control" name="logo" id="logo" accept="image/*" onchange="previewLogo(event)">
                            </div>

                            <div class="form-group">
                                <label>Nomor Telpon</label>
                                <input type="number" class="form-control" name="phone_number" value="{{ $system['phone_number'] }}">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea class="form-control" name="address">{{ json_decode($system['office_address'], true)['address'] }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Kode Pos</label>
                                <input type="number" class="form-control" name="postal_code" value="{{ json_decode($system['office_address'], true)['postal_code'] }}">
                            </div>

                            <div class="form-group">
                                <label>Cari Lokasi Kantor</label>
                                <div class="d-flex gap-2">
                                    <input type="text" id="search-office-address" class="form-control" placeholder="Cari lokasi..." />
                                    <button type="button" class="btn btn-info" onclick="geocodeOfficeAddress()"><i class="fa fa-search mr-1" aria-hidden="true"></i> Cari</button>
                                    <button type="button" class="btn btn-secondary" onclick="resetToOriginal()"><i class="fa fa-undo mr-1" aria-hidden="true"></i> Reset</button>
                                </div>
                            </div>

                            <input type="hidden" id="latitude" name="latitude" value="{{ json_decode($system['office_address'], true)['latitude'] }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ json_decode($system['office_address'], true)['longitude'] }}">

                            <div class="form-group">
                                <label for="cc-payment" class="control-label mb-1">Address Point</label>
                                <div id="map-office" class="mb-4" style="height: 300px; border-radius: 8px; overflow: hidden;"></div>
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
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong class="card-title">Area yang Ditangani</strong>
                            <button type="button" class="btn btn-sm btn-success" onclick="addCoverageArea()"><i class="fa fa-plus"></i> Tambah Area</button>
                        </div>
                        <div class="card-body" id="coverage-container">
                            <!-- Coverage items will be appended here -->
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="our-coverage-json" name="our_coverage" />
            <button type="submit" class="btn btn-primary mt-3" onclick="prepareCoverageData()"><i class="fa fa-floppy-o mr-2" aria-hidden="true"></i> Simpan</button>
        </div>
    </form>
</x-layout.admin-v2>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let officeMap, officeMarker;
let coverageIndex = 0;
let coverageMaps = {};
let coverageMarkers = {};

function previewLogo(event) {
    const input = event.target;
    const preview = document.getElementById('logo-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function prepareCoverageData() {
    const coverages = [];
    document.querySelectorAll('[id^="coverage-box-"]').forEach((box) => {
        const index = box.getAttribute('id').split('-').pop();
        const lat = document.getElementById(`coverage-lat-${index}`)?.value;
        const lng = document.getElementById(`coverage-lng-${index}`)?.value;
        const radius = document.querySelector(`input[name='coverages[${index}][radius]']`)?.value;
        if (lat && lng && radius) {
            coverages.push({ lat, lng, radius });
        }
    });
    document.getElementById('our-coverage-json').value = JSON.stringify(coverages);
}

document.addEventListener("DOMContentLoaded", function () {
    const lat = parseFloat(document.getElementById("latitude").value);
    const lng = parseFloat(document.getElementById("longitude").value);

    officeMap = L.map("map-office").setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(officeMap);

    officeMarker = L.marker([lat, lng], { draggable: true }).addTo(officeMap);
    officeMarker.on("dragend", function (e) {
        const pos = e.target.getLatLng();
        document.getElementById("latitude").value = pos.lat;
        document.getElementById("longitude").value = pos.lng;
    });

    officeMap.on("click", function (e) {
        officeMarker.setLatLng(e.latlng);
        document.getElementById("latitude").value = e.latlng.lat;
        document.getElementById("longitude").value = e.latlng.lng;
    });

    const coverages = @json(json_decode($system['our_coverage'], true));
    if (Array.isArray(coverages)) {
        coverages.forEach((c) => addCoverageArea(c));
    }
});

function addCoverageArea(data = {}) {
    const index = coverageIndex++;
    const container = document.createElement("div");
    container.className = "border p-2 mb-3 rounded";
    container.id = `coverage-box-${index}`;
    container.innerHTML = `
        <div class="d-flex justify-content-between">
            <strong>Area ${index + 1}</strong>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeCoverageArea(${index})"><i class="fa fa-trash"></i></button>
        </div>
        <div class="form-group mt-2">
            <input type="text" id="search-coverage-${index}" class="form-control mb-2" placeholder="Cari lokasi...">
            <div class="d-flex gap-2 mb-2">
                <button type="button" class="btn btn-info" onclick="searchCoverage(${index})"><i class="fa fa-search mr-1"></i> Cari</button>
                <button type="button" class="btn btn-secondary" onclick="resetCoverage(${index})"><i class="fa fa-undo mr-1"></i> Reset</button>
            </div>
            <input type="hidden" name="coverages[${index}][lat]" id="coverage-lat-${index}" value="${data.lat || ''}">
            <input type="hidden" name="coverages[${index}][lng]" id="coverage-lng-${index}" value="${data.lng || ''}">
            <input type="number" class="form-control mb-2" name="coverages[${index}][radius]" value="${data.radius || ''}" placeholder="Radius (meter)" required>
            <div id="coverage-map-${index}" style="height: 200px; border-radius: 8px;"></div>
        </div>
    `;

    document.getElementById("coverage-container").appendChild(container);

    const lat = parseFloat(data.lat) || -6.2;
    const lng = parseFloat(data.lng) || 106.8;
    const map = L.map(`coverage-map-${index}`).setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    marker.on("dragend", function (e) {
        const pos = e.target.getLatLng();
        document.getElementById(`coverage-lat-${index}`).value = pos.lat;
        document.getElementById(`coverage-lng-${index}`).value = pos.lng;
    });

    map.on("click", function (e) {
        marker.setLatLng(e.latlng);
        document.getElementById(`coverage-lat-${index}`).value = e.latlng.lat;
        document.getElementById(`coverage-lng-${index}`).value = e.latlng.lng;
    });

    coverageMaps[index] = map;
    coverageMarkers[index] = marker;
}

function removeCoverageArea(index) {
    const box = document.getElementById(`coverage-box-${index}`);
    if (box) box.remove();
    delete coverageMaps[index];
    delete coverageMarkers[index];
}

function searchCoverage(index) {
    const query = document.getElementById(`search-coverage-${index}`).value;
    if (!query) return;
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                const { lat, lon } = data[0];
                coverageMaps[index].setView([lat, lon], 15);
                coverageMarkers[index].setLatLng([lat, lon]);
                document.getElementById(`coverage-lat-${index}`).value = lat;
                document.getElementById(`coverage-lng-${index}`).value = lon;
            } else {
                alert("Lokasi tidak ditemukan");
            }
        });
}

function resetCoverage(index) {
    const lat = -6.2;
    const lng = 106.8;
    coverageMaps[index].setView([lat, lng], 13);
    coverageMarkers[index].setLatLng([lat, lng]);
    document.getElementById(`coverage-lat-${index}`).value = lat;
    document.getElementById(`coverage-lng-${index}`).value = lng;
    document.getElementById(`search-coverage-${index}`).value = '';
}
</script>
