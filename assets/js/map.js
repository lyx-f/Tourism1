// Initialize the map
var map = L.map('map').setView([6.9336, 126.2632], 12); // Default location
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Add a marker and display coordinates when the map is clicked
var marker;
map.on('click', function (e) {
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    // Update input fields
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    // Add or update the marker
    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng]).addTo(map);
    }
});

// Fetch existing locations from the database
fetch('../../admin/get-locations.php')
    .then(response => response.json())
    .then(data => {
        data.forEach(location => {
            L.marker([location.latitude, location.longitude])
                .addTo(map)
                .bindPopup(location.name);
        });
    })
    .catch(error => {
        console.error('Error fetching locations:', error);
    });
