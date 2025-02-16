<?php
include('../config/database.php'); 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Validate coordinates
    if (is_numeric($latitude) && is_numeric($longitude)) {
        // Insert the coordinates into the database
        $query = "INSERT INTO locations (latitude, longitude) VALUES (?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$latitude, $longitude]);

        echo "Coordinates saved successfully!";
    } else {
        echo "Invalid coordinates!";
    }
}
?>
