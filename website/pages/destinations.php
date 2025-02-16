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
    <div class="card-grid">
        <?php if (!empty($destinations)): ?>
            <?php foreach ($destinations as $destination): ?>
                <div class="card" onclick="window.location.href='des_info.php?id=<?= $destination['id']; ?>'">
                    <div class="card-header">

                        <img src="<?= isset($destination['image_url']) && !empty($destination['image_url']) ? "../../assets/img/" . $destination['image_url'] : '../../assets/img/1.jpg'; ?>"
                            alt="destination_image">
                        <div class="badge">Guest favorite</div>
                    </div>
                    <div class="card-body">
                        <h3><?= htmlspecialchars($destination['name']); ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No destinations available at the moment.</p>
        <?php endif; ?>
    </div>

    <?php include("../../includes/footer.php"); ?>
</body>

</html>