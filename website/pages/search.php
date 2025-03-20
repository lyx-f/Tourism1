<?php include('../../includes/homepage_navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/search.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>

<div class="search-layout">

<div class="categories-container">
<h3>Categories</h3>

<div class="categories">
    <div class="category">
        <i class="fas fa-camera"></i>
        <p>Attractions</p>
    </div>
    <div class="category">
        <i class="fas fa-house"></i>
        <p>Inns</p>
    </div>
    <div class="category">
        <i class="fas fa-hotel"></i>
        <p>Hotels</p>
    </div>
    <div class="category">
        <i class="fas fa-utensils"></i>
        <p>Restaurants</p>
    </div>
</div>
</div>

    <div class="map-container">
        <h2>TourMatic Map - Mati, Philippines</h2>
        <div id="map"></div>
    </div>
</div>

<script>
    // Initialize the map
var map = L.map('map').setView([6.9336, 126.2632], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);

// Global variable to store all locations
let allLocations = [];

// Fetch locations from the server
fetch('../../admin/get-locations.php') 
    .then(response => response.json())
    .then(data => {
        allLocations = data; // Store locations globally
        showLocations('all'); // Show all locations by default
    })
    .catch(error => {
        console.error('Error fetching locations:', error);
    });

// Function to show locations on the map
function showLocations(category) {
    map.eachLayer(layer => {
        if (layer instanceof L.Marker) map.removeLayer(layer); // Remove existing markers
    });

    allLocations.forEach(location => {
        if (category === 'all' || location.category === category) {
            let tooltipContent = `
                <b>${location.name}</b><br>
                Price Range: ${location.price_range || 'N/A'}<br>
                ⭐ ${location.star_review || 'No Reviews'}
            `;

            let marker = L.marker([location.latitude, location.longitude])
                .addTo(map)
                .bindTooltip(tooltipContent, { 
                    permanent: false, 
                    direction: "top", 
                    className: "custom-tooltip"
                });

            // Redirect on click
            marker.on('click', function() {
                window.location.href = `des_info.php?id=${location.id}`;
            });
        }
    });
}




</script>

<?php include('../../includes/footer.php'); ?>
</body>
</html>
