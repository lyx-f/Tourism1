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

<body>

    <div class="hero-container">
        <div class="hero-content">
            <h1>Explore</h1>
            <span class="outlined-text">Destinations</span>
            <p>Experience it now!</p>
        </div>
        </section>

        <div class="destination-container">
            <?php if (!empty($destinations)): ?>
                <?php foreach ($destinations as $destination):
?>
                    <div class="card">
                        
                        <img src="<?= isset($destination['main_image']) && !empty($destination['main_image']) ? "../../assets/img/" . $destination['main_image'] : '../../assets/img/'; ?>"
                            alt="destination_image">
                        
                        <h3><?= htmlspecialchars($destination['name']); ?></h3>
                        <p><?= htmlspecialchars($destination['description']); ?></p>
                        <a href="des_info.php?id=<?= $destination['id']; ?>">Read More</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No destinations available at the moment.</p>
            <?php endif; ?>
            <!-- <div class="card">
                <img src="../../assets/img/1.jpg" alt="fiksur ni">
                <h3>Blue Bless Beach Resort</h3>
                <p>A serene beachfront escape perfect for relaxation and enjoying the sun.</p>
                <a href="#">Read More</a>
            </div>
            <div class="card">
                <img src="../../assets/img/2.jpg" alt="fiksur ni">
                <h3>Masao Beach Resort</h3>
                <p>A picturesque resort with stunning waterfront cottages and a fine white-sand beach.</p>
                <a href="#">Read More</a>
            </div>
            <div class="card">
                <img src="../../assets/img/5.jpg" alt="fiksur ni">
                <h3>Oriental Reef Beach Resort</h3>
                <p>A charming resort with beautiful coastal views and a tranquil setting.</p>
                <a href="#">Read More</a>
            </div> -->
        </div>
    </div>


    <div class="map-button-container">
        <a href="search.php" target="_blank" class="map-button">
            <i class="fas fa-map"></i> Open Maps
        </a>
    </div>


    <?php include('../../includes/footer.php'); ?>

    </bodyc>

</html>