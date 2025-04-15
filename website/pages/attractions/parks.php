<?php
include("../../../includes/homepage_navbar.php");
include('../../../config/database.php'); // Include database connection

// Fetch only hotels from the 'businesses' table
$query = "SELECT id, name, image_url, status FROM businesses WHERE sub_category = 'parks'";
$result = $conn->query($query);

// Check if the query returned results
$hotels = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations</title>
    <link rel="stylesheet" href="../../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../../assets/css/categories.css">
    <link rel="stylesheet" href="../../../assets/css/footer.css">
</head>

<body>
    <div class="wrapper">


        <div class=" container">
            <h1 class="page-title">Available Parks</h1>

            <div class="card-grid">
                <?php if (!empty($hotels)): ?>
                    <?php foreach ($hotels as $hotel): ?>
                        <div class="card" onclick="window.location.href='des_info.php?id=<?= $hotel['id']; ?>'">
                            <div class="card-header">
                                <img src="../../../assets/img/<?php echo htmlspecialchars($hotel['image_url']); ?>"
                                    alt="<?php echo htmlspecialchars($hotel['name']); ?>">
                                <span class="badge"><?php echo htmlspecialchars($hotel['status']); ?></span>
                            </div>
                            <div class="card-content">
                                <h2><?php echo htmlspecialchars($hotel['name']); ?></h2>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-hotel-card">
                        <p>No Parks found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include("../../../includes/footer.php"); ?> <!-- Footer -->

</body>

</html>