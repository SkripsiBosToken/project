@props(['coverageArea' => null, 'pinArea' => null])

<div class="w-full h-36 md:h-96 shadow-md">
    <div id="map" class="rounded-xl w-full h-full"></div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var pinArea = @json($pinArea);
        var coverageArea = {!! json_encode($coverageArea) !!};

        var centerLat = -7.9666; 
        var centerLng = 112.6326; 
        var zoomLevel = 12;

        if (pinArea && pinArea.length > 0) {
            centerLat = pinArea[0].lat;
            centerLng = pinArea[0].lng;
            zoomLevel = 15;
        }

        var map = L.map('map').setView([centerLat, centerLng], zoomLevel);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        if (coverageArea && coverageArea.length > 0) {
            coverageArea.forEach(function(area) {
            if (area.lat && area.lng) {
                L.circle([area.lat, area.lng], {
                    color: "#588157",
                    fillColor: "#588157",
                    fillOpacity: 0.2,
                    radius: area.radius || 5000
                }).addTo(map).bindPopup("Area Coverage");
            }
        });
        }

        if (pinArea && pinArea.length > 0) {
            pinArea.forEach(function(pin) {
                L.marker([pin.lat, pin.lng])
                    .addTo(map)
                    .bindPopup(pin.label || "Lokasi");
            });

            map.on('click', function() {
                var googleMapsUrl = `https://www.google.com/maps?q=${pinArea[0].lat},${pinArea[0].lng}`;
                window.open(googleMapsUrl, '_blank');
            });
        }
    });
</script>