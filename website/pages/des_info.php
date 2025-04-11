<?php
session_start();
// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}


$user_id = $_SESSION['user_id']; // Logged-in user ID

include("../../includes/homepage_navbar.php");
include('../../config/database.php');


// Get the destination ID from the URL
$destination_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch destination details from the database
$query = "SELECT * FROM businesses WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $destination_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $destination = $result->fetch_assoc();
} else {
    echo "<p>Destination not found.</p>";
    exit;
}


function isBooked($destination_id, $user_id, $conn)
{
    // Fetch destination details from the database
    $query = "SELECT * FROM bookings WHERE business_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $destination_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result->num_rows > 0;
}

$showAddFeedback = isBooked($destination_id, $user_id, $conn);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($destination['name']); ?> - Information</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/information.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <div class="holder">
        <div class="image-gallery">
            <?php
            // Fetch and clean image URLs
            $mainImage = isset($destination['main_image']) ? trim($destination['main_image']) : ''; // Fetch main image
            $imageUrls = isset($destination['image_url']) ? trim($destination['image_url']) : ''; // Fetch gallery images
            
            // Remove leading comma from `image_url`
            $imageUrls = ltrim($imageUrls, ',');

            $images = array_filter(array_map('trim', explode(',', $imageUrls)));
            $images = array_values($images); // Reset array index
            
            // Set correct file paths
            $mainImagePath = "../../assets/img/" . $mainImage;

            // Check if the main image exists before displaying
            if (!empty($mainImage) && file_exists($mainImagePath)):
                ?>
                <div class="image-main">
                    <img src="<?= htmlspecialchars($mainImagePath) ?>" alt="Main Image">
                </div>
            <?php else: ?>
                <div
                    style="display: flex; justify-content: center; align-items: center;color: #DEDEDE; border-radius: 20px; background-color: rgba(0, 109, 109, 0.79); height:100%">
                    No main image available</div>
            <?php endif; ?>

            <div class="image-grid">
                <?php
                foreach ($images as $galleryImage):
                    $galleryImagePath = "../../assets/img/" . $galleryImage;
                    ?>
                    <img src="<?= htmlspecialchars($galleryImagePath) ?>" alt="Gallery Image">
                <?php endforeach; ?>
            </div>
        </div>



        <div class="details">
            <h1><?= htmlspecialchars($destination['name']); ?></h1>
            <p><span class="icon-circle"><i class="fa-solid fa-align-left"></i></span>
                <?= htmlspecialchars($destination['description']); ?></p>
            <p><span class="icon-circle"><i class="fa-solid fa-location-dot"></i></span>
                <?= htmlspecialchars($destination['location']); ?></p>
            <p><span class="icon-circle"><i class="fa-solid fa-phone"></i></span>
                <?= htmlspecialchars($destination['phone']); ?></p>


        </div>

        <div class="reservation-feedback-container">
            <div class="reservation">

                <?php if (!empty($destination['amenities'])): ?>
                    <h3>What this place offers</h3>
                    <ul>
                        <?php
                        $amenitiesList = explode(',', $destination['amenities']);
                        $amenityIcons = [
                            'WiFi' => 'fas fa-wifi',
                            'Parking' => 'fas fa-car',
                            'Pool' => 'fas fa-swimming-pool',
                            'Restaurant' => 'fas fa-utensils',
                            'Gym' => 'fas fa-dumbbell',
                            'Bar' => 'fas fa-cocktail',
                            'Pet Friendly' => 'fas fa-paw'
                        ];

                        foreach ($amenitiesList as $amenity):
                            $trimmedAmenity = trim($amenity);
                            $iconClass = isset($amenityIcons[$trimmedAmenity]) ? $amenityIcons[$trimmedAmenity] : 'fas fa-check';
                            ?>
                            <li><i class="<?= $iconClass; ?>"></i> <?= htmlspecialchars($trimmedAmenity); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No amenities listed.</p>
                <?php endif; ?>
                <p>Reservations Only! Secure your spot now</p>


                <a href="booking.php?business_id=<?= $destination['id']; ?>">
                    <button class="reserve-button">Reserve</button>
                </a>
            </div>



            <div class="feedback-section">
                <h2>Feedback & Reviews</h2>

                <div class="comments">
                    <?php
                    // Fetch feedback for this destination
                    $feedback_query = "SELECT name, rating, comment, created_at FROM feedbacks WHERE destination_id = ? ORDER BY created_at DESC";
                    $feedback_stmt = $conn->prepare($feedback_query);
                    $feedback_stmt->bind_param("i", $destination_id);
                    $feedback_stmt->execute();
                    $feedback_result = $feedback_stmt->get_result();

                    if ($feedback_result->num_rows > 0) {
                        while ($row = $feedback_result->fetch_assoc()) {
                            echo '<div class="comment">';
                            echo '<p><strong>' . htmlspecialchars($row['name']) . '</strong></p>';
                            echo '<p>' . str_repeat('⭐️', $row['rating']) . '</p>';
                            echo '<p>' . htmlspecialchars($row['comment']) . '</p>';
                            echo '<p><small>' . date('F j, Y', strtotime($row['created_at'])) . '</small></p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No feedback available yet. Be the first to leave a review!</p>';
                    }
                    ?>
                </div>

                <form class="feedback-form <?= !$showAddFeedback ? 'hidden' : '' ?>"  action="submit_feedback.php"
                    method="POST">
                    <input type="hidden" name="destination_id" value="<?= $destination['id']; ?>">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" placeholder="Your Name" required>

                    <label for="rating">Your Rating:</label>
                    <select id="rating" name="rating" required>
                        <option value="5">⭐️⭐️⭐️⭐️⭐️ (5 Stars)</option>
                        <option value="4">⭐️⭐️⭐️⭐️ (4 Stars)</option>
                        <option value="3">⭐️⭐️⭐️ (3 Stars)</option>
                        <option value="2">⭐️⭐️ (2 Stars)</option>
                        <option value="1">⭐️ (1 Star)</option>
                    </select>

                    <label for="comment">Your Feedback:</label>
                    <textarea id="comment" name="comment" rows="4" placeholder="..." required></textarea>

                    <button type="submit" class="submit-button">Submit Feedback</button>
                </form>
            </div>
        </div>
    </div>

    <?php include('../../includes/footer.php'); ?>

</body>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const Toast = Swal.mixin({
            toast: true,
            position: 'center',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        });
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get("success");
        const error = urlParams.get("error");

        if (successMessage) {
            Toast.fire({
                icon: "success",
                title: successMessage,
            });
        } else if (error) {
            Toast.fire({
                icon: "error",
                title: "Something went wrong",
            });
        }
    });
</script>

</html>