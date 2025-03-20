<?php include('../../includes/homepage_navbar.php');
include('../../config/database.php');
// Fetch top 3 businesses with the highest rating
$query = "SELECT b.id, b.name, b.description, b.image_url,b.main_image, AVG(f.rating) AS average_rating
FROM businesses b
LEFT JOIN feedbacks f ON b.id = f.destination_id
GROUP BY b.id, b.name
ORDER BY average_rating DESC
LIMIT 3;";

$result = $conn->query($query);

if ($result) {
    if ($result->num_rows > 0) {
        $destinations = $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
    } else {
        $destinations = []; // Empty array if no destinations found
    }
} else {
    die("Query failed: " . $conn->error); // Handle query errors
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourmatic</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">

</head>

<body class="homepage">
    <div class="main-content">
        <div class="hero-container">
            <div class="hero-content">
                <h1>Explore</h1>
                <span class="outlined-text">Destinations</span>
                <p>Kadi kamo!</p>
            </div>

            <div class="destination-container">
                <?php if (!empty($destinations)): ?>
                    <?php foreach ($destinations as $destination): ?>
                        <div class="card">
                            <img src="<?= isset($destination['image_url']) && !empty($destination['image_url']) ? "../../assets/img/" . $destination['image_url'] : '../../assets/img/'; ?>"
                                alt="destination_image">

                            <h3><?= htmlspecialchars($destination['name']); ?></h3>
                            <p><?= htmlspecialchars($destination['description']); ?></p>
                            <a href="des_info.php?id=<?= $destination['id']; ?>">Read More</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No destinations available at the moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="map-button-container">
            <a href="search.php" target="_blank" class="map-button">
                <i class="fas fa-map"></i> Mati Map
            </a>
        </div>
    </div>

    <?php include('../../includes/footer.php'); ?>
</body>


</html>