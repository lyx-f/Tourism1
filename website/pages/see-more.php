<?php
include("../../includes/homepage_navbar.php");
include('../../config/database.php'); // Include database connection

// Fetch only accommodations from the 'businesses' table
$query = "SELECT id, name, image_url, status FROM businesses WHERE category = 'Accommodation'";
$result = $conn->query($query);

// Check if the query returned results
$accommodations = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accommodations</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/destination.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
</head>
<body>

<div class="grid-container">
    <a href="salons.php" class="card">
        <img src="../../assets/img/salons.jpg" alt="Salons">
        <div class="card-content">
            <i class="fas fa-cut"></i>
            <p>SALONS</p>
        </div>
    </a>

    <a href="boutique.php" class="card">
        <img src="../../assets/img/boutique.jpg" alt="Boutique">
        <div class="card-content">
            <i class="fas fa-tshirt"></i>
            <p>BOUTIQUE</p>
        </div>
    </a>

    <a href="wellness.php" class="card">
        <img src="../../assets/img/wellness.jpg" alt="Wellness Centers">
        <div class="card-content">
            <i class="fas fa-spa"></i>
            <p>WELLNESS CENTERS</p>
        </div>
    </a>
</div>

<!-- Subcategories Container (Hidden Initially) -->
<div id="subcategories" style="display: none;"></div>

<!-- Booking Form (Hidden Initially) -->
<div id="booking-form" style="display: none;">
        <?php if (!empty($accommodations)): ?>
            <?php foreach ($accommodations as $accommodation): ?>
                <div class="card">
                    <img src="../../assets/img/<?php echo htmlspecialchars($accommodation['image_url']); ?>" alt="<?php echo htmlspecialchars($accommodation['name']); ?>">
                    <div class="card-content">
                        <h2><?php echo htmlspecialchars($accommodation['name']); ?></h2>
                        <p>Status: <?php echo htmlspecialchars($accommodation['status']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-hotel-card">
                <p>No destinations found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include("../../includes/footer.php"); ?> <!-- Footer -->

</body>
</html>
