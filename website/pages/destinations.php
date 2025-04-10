<?php
include("../../includes/homepage_navbar.php");
include('../../config/database.php'); // Include your database connection

// Fetch all destinations from the 'businesses' table
$query = "SELECT id, name, image_url, status, category FROM businesses";
$result = $conn->query($query);

// Check if the query returned results
if ($result->num_rows > 0) {
    $destinations = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
} else {
    $destinations = []; // Empty array if no destinations found
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/destination.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>

<body>
    <div class="grid-container">
        <a href="events.php" class="card">
            <img src="../../assets/img/events" alt="Events">
            <div class="card-content">
                <i class="far fa-calendar"></i>
                <p>EVENTS</p>
            </div>
        </a>

        <a href="attractions.php" class="card">
            <img src="../../assets/img/attractions" alt="Attractions">
            <div class="card-content">
                <i class="fas fa-camera"></i>
                <p>ATTRACTIONS</p>
            </div>
        </a>

        <a href="restaurants.php" class="card">
            <img src="../../assets/img/restaurants" alt="Restaurants">
            <div class="card-content">
                <i class="fas fa-utensils"></i>
                <p>RESTAURANTS</p>
            </div>
        </a>

        <a href="accommodations.php" class="card">
            <img src="../../assets/img/accommodation.webp" alt="Accommodations">
            <div class="card-content">
                <i class="fas fa-home"></i>
                <p>ACCOMMODATIONS</p>
            </div>
        </a>

        <a href="see-more.php" class="card">
            <img src="../../assets/img/lovemati" alt="See More">
            <div class="card-content">
                <i class="fas fa-search-location"></i>
                <p>SEE MORE</p>
            </div>
        </a>
    </div>


</body>
<?php include("../../includes/footer.php"); ?>

</html>