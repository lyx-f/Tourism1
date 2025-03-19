<?php
include("../../includes/homepage_navbar.php");
include('../../config/database.php'); // Include database connection

// Fetch only accommodations from the 'businesses' table
$query = "SELECT id, name, image_url, status FROM businesses WHERE category = 'Accommodations'";
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
       <a href="resorts.php" class="card">
        <img src="../../assets/img/resorts.jpg" alt="hotels">
        <div class="card-content">
            <i class="fas fa-hotel"></i>
            <p>HOTELS</p>
        </div>
    </a>

       <a href="resorts.php" class="card">
        <img src="../../assets/img/resorts.jpg" alt="inns">
        <div class="card-content">
            <i class="fas fa-bed"></i>
            <p>INNS</p>
        </div>
    </a>
        <div class="card-grid">
            <?php if (!empty($accommodations)): ?>
                <?php foreach ($accommodations as $accommodation): ?>
                    <div class="card" onclick="window.location.href='des_info.php?id=<?= $accommodation['id']; ?>'">
                        <div class="card-header">
                          <img src="../../assets/img/<?php echo htmlspecialchars($accommodation['image_url']); ?>" alt="<?php echo htmlspecialchars($accommodation['name']); ?>">
                            <span class="badge"><?php echo htmlspecialchars($accommodation['status']); ?></span>
                        </div>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($accommodation['name']); ?></h3>
                        </div>
                    </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-hotel-card">
                <p>No accommodations found.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include("../../includes/footer.php"); ?> <!-- Footer -->

</body>
</html>
