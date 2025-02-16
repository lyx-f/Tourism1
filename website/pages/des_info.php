<?php

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
</head>
<body>
<div class="holder">

    <div class="image-section">
    <img src="<?= isset($destination['image_url']) && !empty($destination['image_url']) ? "../../assets/img/" . $destination['image_url'] : '../../assets/img/1.jpg'; ?>"
    alt="destination_image">
    </div>
    
    <div class="details">
        <h1><?= htmlspecialchars($destination['name']); ?></h1>
        <p><?= htmlspecialchars($destination['description']); ?></p>
        <p><?= htmlspecialchars($destination['location']); ?></p>
        <p><?= htmlspecialchars($destination['phone']); ?></p>

    </div>

    <div class="reservation-feedback-container">
        <div class="reservation">
            <p>₱<?= htmlspecialchars($destination['price']); ?> <span>night</span></p>
            
            <div class="dates">
                <input type="date" id="checkin" placeholder="Check-in">
                <input type="date" id="checkout" placeholder="Check-out">
            </div>
            
            <div class="guests">
                <select>
                    <option value="1">1 guest</option>
                    <option value="2">2 guests</option>
                    <option value="3">3 guests</option>
                </select>
            </div>
            
            <a href="booking.php?business_id=<?= $destination['id']; ?>">
                <button class="reserve-button">Reserve</button>
            </a>
            
            <p class="notice">You won't be charged yet</p>
            
            <div class="breakdown">
                <p>₱<?= htmlspecialchars($destination['price']); ?> x 1 nights <span>₱<?= htmlspecialchars($destination['price'] * 0); ?></span></p>
                <p>Cleaning fee <span>₱0</span></p>
                <p>Service fee <span>₱0</span></p>
                <p class="total">Total before taxes <span>₱<?= htmlspecialchars($destination['price'] * 0 + 0 + 0); ?></span></p>
            </div>
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

            <form class="feedback-form" action="submit_feedback.php" method="POST">
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
</html>
