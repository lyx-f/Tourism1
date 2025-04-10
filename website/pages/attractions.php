<?php
include("../../includes/homepage_navbar.php");
include('../../config/database.php'); // Include database connection

// Fetch only attractions from the 'businesses' table
$query = "SELECT id, name, image_url, status FROM businesses WHERE category = 'Attractions'";
$result = $conn->query($query);

// Check if the query returned results
$attractions = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attractions</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/destination.css">

</head>

<body>

    <div class="grid-container">
        <a href="resorts.php" class="card">
            <img src="../../assets/img/resorts.webp" alt="Resorts">
            <div class="card-content">
                <i class="fas fa-umbrella-beach"></i>
                <p>RESORTS</p>
            </div>
        </a>

        <a href="parks.php" class="card">
            <img src="../../assets/img/park.jpg" alt="Parks">
            <div class="card-content">
                <i class="fas fa-tree"></i>
                <p>PARKS</p>
            </div>
        </a>

        <a href="agri_tourism.php" class="card">
            <img src="../../assets/img/agri-tourism.png" alt="Agri-Tourism">
            <div class="card-content">
                <i class="fas fa-seedling"></i>
                <p>AGRI-TOURISM</p>
            </div>
        </a>

        <a href="museums.php" class="card">
            <img src="../../assets/img/museum.jpg" alt="Museums">
            <div class="card-content">
                <i class="fas fa-landmark"></i>
                <p>MUSEUMS</p>
            </div>
        </a>
        <?php if (!empty($attractions)): ?>
            <?php foreach ($attractions as $attraction): ?>
                <div class="card" onclick="window.location.href='des_info.php?id=<?= $attraction['id']; ?>'">
                    <div class="card-header">
                        <img src="../../assets/img/<?php echo htmlspecialchars($attraction['image_url']); ?>"
                            alt="<?php echo htmlspecialchars($attraction['name']); ?>">
                        <span class="badge"><?php echo htmlspecialchars($attraction['status']); ?></span>
                    </div>
                    <div class="card-body">
                        <h3><?= htmlspecialchars($attraction['name']); ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No attractions available at the moment.</p>
        <?php endif; ?>
    </div>

    <div class="card-grid">
        
    </div>
    </div>


</body>
<?php include("../../includes/footer.php"); ?>

</html>