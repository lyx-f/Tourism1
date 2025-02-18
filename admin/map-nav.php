<?php
include '../config/database.php';

$query = "SELECT id, name, latitude, longitude, status FROM locations";  // Modify as needed
$result = $conn->query($query);  // Execute the query and store the result in $result

if (!$result) {
    // Handle query error (optional)
    die("Query failed: " . $conn->error);
}
?>

<?php include('includes/header.php'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Resort</title>
    <link rel="stylesheet" href="../assets/css/map-nav.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <style>
        #map {
            height: 400px;
            width: 100%;
            margin-bottom: 20px;
        }

        .form-container {
            max-width: 500px;
            margin: 0 auto;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h1>Add a Location</h1>
        <form action="process-locations.php" method="POST">
            <input type="hidden" name="action" value="add">
            <label for="name">Location Name:</label>
            <input type="text" id="name" name="name" required><br><br>

            <div id="map"></div>

            <label for="latitude">Latitude:</label>
            <input type="text" id="latitude" name="latitude" readonly required><br><br>

            <label for="longitude">Longitude:</label>
            <input type="text" id="longitude" name="longitude" readonly required><br><br>

            <button type="submit">Save Location</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Location Name</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $statusName = ($row['status'] === 'inactive') ? 'Activate' : 'Deactivate';
                    $statusValue = ($row['status'] === 'inactive') ? 'active' : 'inactive';
                  
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";

                    echo "<form action='process-locations.php' method='POST'>";
                    echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";

                    echo "<td><input type='text' name='name' value='" . $row['name'] . "' required></td>";
                    echo "<td><input type='text' name='latitude' value='" . $row['latitude'] . "' required></td>";
                    echo "<td><input type='text' name='longitude' value='" . $row['longitude'] . "' required></td>";
                    echo "<td>" . $row['status'] . "</td>";
                    echo "<td style=''>
                            <button type='submit' name='update' value='update'>Update</button>
                            <button type='submit' name='updateStatus' value='". $statusValue ."' class='deactivate-btn'>". $statusName ."</button>
                        </td>";
                    echo "</form>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No locations added yet</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script src="../assets/js/map.js"></script>

</body>

</html>