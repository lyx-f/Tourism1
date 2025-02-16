<?php
include ("../../includes/homepage_navbar.php");

session_start();

// Get the business_id from URL or Session
$business_id = isset($_GET['business_id']) ? intval($_GET['business_id']) : (isset($_SESSION['business_id']) ? $_SESSION['business_id'] : null);

// If no business_id is found, show an error message
if (empty($business_id)) {
    die("Error: No business selected for booking. Please go back and select a business.");
}

// Save the business_id in the session for future use
$_SESSION['business_id'] = $business_id;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Form</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css"> 
    <link rel="stylesheet" href="../../assets/css/booking.css"> 
</head>
<body>
    <div class="booking-form">
        <h2>Booking Form</h2>
        <form action="process-booking.php" method="POST">
            <!-- Hidden field for the business_id -->
            <input type="hidden" name="business_id" value="<?= htmlspecialchars($business_id); ?>">
            
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email address" required>
            </div>
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
            </div>
            <div class="form-group">
                <label for="departure_date">Departure Date</label>
                <input type="date" id="departure_date" name="departure_date" required>
            </div>
            <div class="form-group">
                <label for="arrival_date">Arrival Date</label>
                <input type="date" id="arrival_date" name="arrival_date" required>
            </div>
            <div class="form-group">
                <label for="guests">Number of Guests</label>
                <input type="number" id="guests" name="guests" placeholder="Enter the number of guests" required min="1">
            </div>
            <div class="form-group">
                <label for="room_type">Room Type</label>
                <select id="room_type" name="room_type" required>
                    <option value="" disabled selected>Select room type</option>
                    <option value="single">Single</option>
                    <option value="double">Double</option>
                    <option value="suite">Suite</option>
                    <option value="family">Family</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">BOOK NOW</button>
        </form>
    </div>

    <?php include ("../../includes/footer.php"); ?>

</body>
</html>
