<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include "../../../config/database.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate and fetch the business_id
if (!isset($_GET['business_id']) || !is_numeric($_GET['business_id'])) {
    die("Invalid business ID provided.");
}

$business_id = intval($_GET['business_id']);

// Fetch the business data for the logged-in user
$sql = "SELECT id, name, description, location, price, main_image, image_url, amenities 
        FROM businesses 
        WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Failed to prepare SQL statement: " . $conn->error);
}

$stmt->bind_param('ii', $business_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $businessName = $row['name'];
    $description = $row['description'];
    $location = $row['location'];
    $price = $row['price'];
    $mainImage = !empty($row['main_image']) ? trim($row['main_image']) : ''; // Fetch main image
    $imagePath = !empty($row['image_url']) ? trim($row['image_url']) : ''; // Fetch additional images
    $otherImages = !empty($imagePath) ? explode(',', $imagePath) : []; // Convert to array
    $amenities = $row['amenities']; // Fetch amenities from DB
} else {
    die("No business data found for the provided ID or insufficient permissions.");
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Business</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for popups -->
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Overview</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="feedbacks.php">Feedbacks</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="edit-business.php?business_id=<?php echo $business_id; ?>" class="active">Edit Business</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="content">
            <h1>Edit Business</h1>
            <p>Update the business details below:</p>

            <!-- Success message -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Business details updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
            <?php endif; ?>

            <!-- Edit Business Form -->
            <form action="process-edit-business.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="business_id" value="<?php echo $business_id; ?>">

                <!-- Business Name -->
                <label for="businessName">Business Name:</label>
                <input type="text" id="businessName" name="businessName" placeholder="Enter business name"
                    value="<?php echo htmlspecialchars($businessName); ?>" required>

                <!-- Business Description -->
                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="5" placeholder="Enter business description"
                    required><?php echo htmlspecialchars($description); ?></textarea>

                <!-- Business Location -->
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" placeholder="Enter business location"
                    value="<?php echo htmlspecialchars($location); ?>" required>

                <!-- Business Amenities -->
                <label for="amenities">Amenities:</label>
                <input type="text" id="amenities" name="amenities" placeholder="WiFi, Parking, Pool, etc."
                    value="<?php echo htmlspecialchars($amenities); ?>">
                <p><small>Separate amenities with commas (,)</small></p>

                <!-- Business Images -->
                <label for="main_image">Main Business Image:</label>
                <input type="file" id="main_image" name="main_image" accept="image/*">

                <!-- Display Existing Main Image -->
                <?php if (!empty($mainImage)): ?>
                    <p>Current Main Image:</p>
                    <img src="<?= "../../../assets/img/" . htmlspecialchars($mainImage) ?>" 
                         style="max-width: 300px; object-fit: cover; margin: 5px; border: 2px solid #00796b;">
                <?php endif; ?>

                <label for="images">Additional Business Images:</label>
                <input type="file" id="image1" name="images[]" accept="image/*">
                <input type="file" id="image2" name="images[]" accept="image/*">
                <input type="file" id="image3" name="images[]" accept="image/*">
                <input type="file" id="image4" name="images[]" accept="image/*">

                <!-- Display Existing Additional Images -->
                <?php if (!empty($otherImages)): ?>
                    <p>Current Additional Images:</p>
                    <div>
                        <?php foreach ($otherImages as $image): ?>
                            <img src="<?= "../../../assets/img/" . htmlspecialchars(trim($image)) ?>" 
                                 style="max-width: 150px; object-fit: cover; margin: 5px;">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <button type="submit">Save Changes</button>
            </form>
        </main>
    </div>
</body>

</html>
