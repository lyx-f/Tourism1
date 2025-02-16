<?php
include('../config/database.php'); 

// Query to get locations and their categories from the database
$sql = "SELECT name, latitude, longitude, category FROM locations"; // Include the 'category' field
$result = $conn->query($sql);

$locations = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $locations[] = array(
            'name' => $row['name'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude'],
            'category' => $row['category'] 
        );
    }
}

// Return the locations as a JSON array
header('Content-Type: application/json');
echo json_encode($locations);
?>
