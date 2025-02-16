<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

include "../../config/database.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch user's booking details
$query = "SELECT * FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed for booking query: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}

$stmt->close();
include("../../includes/homepage_navbar.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Submission</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/history.css">
</head>
<body>


        <main class="content">
            <h1>Booking History</h1>

            <?php if (count($bookings) > 0): ?>
                <?php foreach ($bookings as $booking): ?>
                    <div class="booking-card">
                        <h2>Booking ID: <?php echo $booking['id']; ?></h2>
                        <p><strong>First Name:</strong> <?php echo htmlspecialchars($booking['first_name']); ?></p>
                        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($booking['last_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
                        <p><strong>Departure Date:</strong> <?php echo htmlspecialchars($booking['departure_date']); ?></p>
                        <p><strong>Arrival Date:</strong> <?php echo htmlspecialchars($booking['arrival_date']); ?></p>
                        <p><strong>Guests:</strong> <?php echo htmlspecialchars($booking['guests']); ?></p>
                        <p><strong>Room Type:</strong> <?php echo htmlspecialchars($booking['room_type']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($booking['status']); ?></p>

                        <?php if ($booking['status'] != 'Cancelled'): ?>
                            <div class="button-container">
            <a href="process-booking.php?id=<?php echo $booking['id']; ?>&action=cancel" class="btn btn-cancel">Cancel Booking</a>
                          </div>
                        <?php else: ?>
                            <p>You cancelled your booking.</p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>You have no past bookings.</p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
<?php include("../../includes/footer.php"); ?>

