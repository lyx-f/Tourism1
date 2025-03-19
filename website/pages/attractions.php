<?php
include("../../includes/homepage_navbar.php");
include('../../config/database.php'); 

<<<<<<< HEAD
// Ensure database connection works
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get category and subcategory from URL, default to 'Attractions'
$category = isset($_GET['category']) ? $_GET['category'] : 'Attractions';
$subcategory = isset($_GET['subcategory']) ? $_GET['subcategory'] : null;

// Prepare SQL query
$query = "SELECT id, name, image_url, status, subcategory FROM businesses WHERE category = ?";
$params = [$category]; 

if (!empty($subcategory)) {
    $query .= " AND subcategory = ?";
    $params[] = $subcategory;
}

//JHJHJHJJHJH

$stmt = $conn->prepare($query);

// Fix bind_param() for dynamic parameter count
if (count($params) == 1) {
    $stmt->bind_param("s", $params[0]);
} else {
    $stmt->bind_param("ss", $params[0], $params[1]);
}

// Execute query and check for errors
if (!$stmt->execute()) {
    die("Query Error: " . $stmt->error);
}

$result = $stmt->get_result();
$businesses = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];

// Group businesses by subcategory
$groupedBusinesses = [];
foreach ($businesses as $business) {
    $groupedBusinesses[$business['subcategory']][] = $business;
}
=======
// Fetch only attractions from the 'businesses' table
$query = "SELECT id, name, image_url, status FROM businesses WHERE category = 'Attractions'";
$result = $conn->query($query);

$attractions = $result->num_rows > 0 ? $result->fetch_all(MYSQLI_ASSOC) : [];
>>>>>>> de8dda576a272fee8719ae043331cb2d5832bc71
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title><?= htmlspecialchars($category); ?></title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/destination.css">
</head>
<body>

    <h1><?= htmlspecialchars($category); ?></h1>
    echo "<pre>" . print_r($params, true) . "</pre>";


    <div class="grid-container">
        <a href="attractions.php?category=Attractions&subcategory=<?= urlencode('Resorts'); ?>" class="card">
            <img src="../../assets/img/resorts.webp" alt="Resorts">
            <div class="card-content">
                <i class="fas fa-umbrella-beach"></i>
                <p>RESORTS</p>
            </div>
        </a>

        <a href="attractions.php?category=Attractions&subcategory=<?= urlencode('Parks'); ?>" class="card">
            <img src="../../assets/img/park.jpg" alt="Parks">
            <div class="card-content">
                <i class="fas fa-tree"></i>
                <p>PARKS</p>
            </div>
        </a>

        <a href="attractions.php?category=Attractions&subcategory=<?= urlencode('Agri-Tourism'); ?>" class="card">
            <img src="../../assets/img/agri-tourism.png" alt="Agri-Tourism">
            <div class="card-content">
                <i class="fas fa-seedling"></i>
                <p>AGRI-TOURISM</p>
            </div>
        </a>

        <a href="attractions.php?category=Attractions&subcategory=<?= urlencode('Museums'); ?>" class="card">
            <img src="../../assets/img/museum.jpg" alt="Museums">
            <div class="card-content">
                <i class="fas fa-landmark"></i>
                <p>MUSEUMS</p>
            </div>
        </a>

        <!-- Business Listings -->
        <?php if (!empty($groupedBusinesses)): ?>
            <?php foreach ($groupedBusinesses as $subcategory => $businesses): ?>
                <div class="subcategory-title"><?= htmlspecialchars($subcategory); ?></div>
                <?php foreach ($businesses as $business): ?>
                    <div class="card" onclick="window.location.href='des_info.php?id=<?= $business['id']; ?>'">
                        <div class="card-header">
                            <img src="../../assets/img/<?= htmlspecialchars($business['image_url']); ?>" alt="<?= htmlspecialchars($business['name']); ?>">
                            <span class="badge"><?= htmlspecialchars($business['status']); ?></span>
                        </div>
                        <div class="card-body">
                            <h3><?= htmlspecialchars($business['name']); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No businesses available in this category.</p>
        <?php endif; ?>
    </div>

</body>
<?php include("../../includes/footer.php"); ?>
=======
    <title>Attractions</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/destination.css">

</head>
<body>

    <div class="grid-container">
       <a href="resorts.php" class="card">
        <img src="../../assets/img/resorts.jpg" alt="Resorts">
        <div class="card-content">
            <i class="fas fa-umbrella-beach"></i>
            <p>RESORTS</p>
        </div>
    </a>

    <a href="parks.php" class="card">
        <img src="../../assets/img/parks.jpg" alt="Parks">
        <div class="card-content">
            <i class="fas fa-tree"></i>
            <p>PARKS</p>
        </div>
    </a>

    <a href="agri_tourism.php" class="card">
        <img src="../../assets/img/agri_tourism.jpg" alt="Agri-Tourism">
        <div class="card-content">
            <i class="fas fa-seedling"></i>
            <p>AGRI-TOURISM</p>
        </div>
    </a>

    <a href="museums.php" class="card">
        <img src="../../assets/img/museums.jpg" alt="Museums">
        <div class="card-content">
            <i class="fas fa-landmark"></i>
            <p>MUSEUMS</p>
        </div>
    </a>
</div>

        <div class="card-grid">
            <?php if (!empty($attractions)): ?>
                <?php foreach ($attractions as $attraction): ?>
                    <div class="card" onclick="window.location.href='des_info.php?id=<?= $attraction['id']; ?>'">
                        <div class="card-header">
                          <img src="../../assets/img/<?php echo htmlspecialchars($attraction['image_url']); ?>" alt="<?php echo htmlspecialchars($attraction['name']); ?>">
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
    </div>


</body>
    <?php include("../../includes/footer.php"); ?>

>>>>>>> de8dda576a272fee8719ae043331cb2d5832bc71
</html>
