@props(['coverageArea' => null, 'pinArea' => null])

<div class="w-full h-36 md:h-96 shadow-md">
    <div id="map" class="rounded-xl w-full h-full"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@if ($pinArea === null)
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var map = L.map('map').setView([-7.9666, 112.6326], 12);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            var coverageArea = @json($coverageArea);
            var pinArea = @json($pinArea);

            if (coverageArea && coverageArea.length > 0) {
                L.polygon(coverageArea, {
                    color: "#588157", 
                    fillColor: "#588157",
                    fillOpacity: 0.2
                }).addTo(map).bindPopup("Area Coverage");
            }

            if (pinArea && pinArea.length > 0) {
                pinArea.forEach(function(pin) {
                    L.marker([pin.lat, pin.lng])
                        .addTo(map)
                        .bindPopup(pin.label || "Lokasi");
                });
            }
        });
    </script>
@else
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var pinArea = @json($pinArea);

            var centerLat = pinArea.length > 0 ? pinArea[0].lat : -7.9666;
            var centerLng = pinArea.length > 0 ? pinArea[0].lng : 112.6326;

            var map = L.map('map').setView([centerLat, centerLng], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            if (pinArea && pinArea.length > 0) {
                pinArea.forEach(function(pin) {
                    L.marker([pin.lat, pin.lng])
                        .addTo(map)
                        .bindPopup(pin.label || "Lokasi");
                });
            }
        });
    </script>
@endif
