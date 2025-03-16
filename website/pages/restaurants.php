<?php
include("../../includes/homepage_navbar.php");
include('../../config/database.php'); // Include database connection

// Fetch only restaurants from the 'businesses' table
$query = "SELECT id, name, image_url, status FROM businesses WHERE category = 'Restaurants'";
$result = $conn->query($query);

// Check if the query returned results
$restaurants = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurants</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/categories.css">
</head>
<body>

    <div class="container">
        <h1 class="page-title">Popular Restaurants</h1>

        <div class="card-grid">
            <?php if (!empty($restaurants)): ?>
                <?php foreach ($restaurants as $restaurant): ?>
                    <div class="card" onclick="window.location.href='des_info.php?id=<?= $restaurant['id']; ?>'">
                        <div class="card-header">
                            <img src="../../assets/img/<?php echo htmlspecialchars($restaurant['image_url']); ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>">
                            <span class="badge"><?php echo htmlspecialchars($restaurant['status']); ?></span>
                        </div>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($restaurant['name']); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-hotel-card">
                    <p>No restaurants available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include("../../includes/footer.php"); ?>
</body>
</html>
