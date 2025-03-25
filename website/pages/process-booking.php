<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include('../../config/database.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("User not logged in.");
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

// Fetch user details
$sql_user = "SELECT id, username, role, business_owner FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);


$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_role = $result_user->fetch_assoc();

// Handle booking acceptance, rejection, or cancellation
if (isset($_GET['id'], $_GET['action'])) {
    $booking_id = intval($_GET['id']);
    $action = $_GET['action'];
    $status = '';

    // Determine the status based on the action
    if ($action === 'accept') {
        $status = 'Accepted';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    } elseif ($action === 'cancel') {
        $status = 'Cancelled';
    } else {
        die("Invalid action.");
    }

    // Update the booking status in the database
    $query = "UPDATE bookings SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        die("Error preparing query: " . $conn->error);
    }

    $stmt->bind_param('si', $status, $booking_id);

    if ($stmt->execute()) {
        $activity_sql = "INSERT INTO activities (booking_id, time) VALUES (?, ?)";
        $activity_stmt = $conn->prepare($activity_sql);

        if ($activity_stmt) {
            $activity_stmt->bind_param("is", $booking_id,$status );
            if (!$activity_stmt->execute()) {
                echo "Status updated, but failed to record activity: " . $activity_stmt->error;
            }
            $activity_stmt->close();
        } else {
            echo "Status updated, but failed to prepare activity statement: " . $conn->error;
        }

        if ($user_role["business_owner"] == 1) {
            // Redirect back to the dashboard with a success message
            header("Location: ../pages/bsowner/dashboard.php?message=Booking $status successfully");
            exit();
        }
        header("Location: ../pages/history.php?message=Booking $status successfully");

        exit();
    } else {
        echo "Error updating booking: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    exit();
}

// Handle form submission for new bookings
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect booking data from the form
    $business_id = intval($_POST['business_id']); // Get the selected business ID
    $status = "Pending";


    if ($_POST['category'] === "accommodations") {

        accomodationBooking($conn, $user_id, $business_id);

    }

} else {
    echo "Invalid request method.";
}


function accomodationBooking($conn, $user_id, $business_id)
{
    $firstName = $_POST["first_name"] ?? null;
    $lastName = $_POST["last_name"] ?? null;
    $email = $_POST["email"] ?? null;
    $phone = $_POST["phone"] ?? null;
    $checkIn = $_POST["checkin"] ?? null;
    $checkOut = $_POST["checkout"] ?? null;
    $adults = $_POST["adults"] ?? 0;
    $children = $_POST["children"] ?? 0;
    $roomType = $_POST["room_type"] ?? null;
    $status = "Pending"; // Default status

    // Validate required fields
    if (
        empty($business_id) || empty($user_id) || empty($firstName) || empty($lastName) || empty($email) ||
        empty($phone) || empty($checkIn) || empty($checkOut) || ($adults == 0 && $children == 0)
    ) {
        header("Location: booking.php?business_id=" . $business_id . "&error=" . urlencode("All fields are required!"));
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: booking.php?business_id=" . $business_id . "&error=" . urlencode("Invalid email format!"));
        exit();
    }

    // Validate check-in and check-out dates
    if (strtotime($checkIn) > strtotime($checkOut)) {
        header("Location: booking.php?business_id=" . $business_id . "&error=" . urlencode("Check-in date cannot be after check-out date!"));
        exit();
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into bookings table
        $booking_sql = "INSERT INTO bookings (business_id, first_name, last_name, user_id, status) VALUES (?, ?, ?, ?, ?)";
        $booking_stmt = $conn->prepare($booking_sql);
        if (!$booking_stmt) {
            throw new Exception("Error preparing booking statement: " . $conn->error);
        }

        $booking_stmt->bind_param("issis", $business_id, $firstName, $lastName, $user_id, $status);
        if (!$booking_stmt->execute()) {
            throw new Exception("Error inserting booking: " . $booking_stmt->error);
        }

        $booking_id = $booking_stmt->insert_id; // Get the last inserted booking ID
        $booking_stmt->close();

        // Insert into accommodations_booking_details table
        $details_sql = "INSERT INTO accommodations_booking_details (booking_id, email, phone_number, checkIn, checkOut, adultsCount, childrenCount, room_type) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $details_stmt = $conn->prepare($details_sql);
        if (!$details_stmt) {
            throw new Exception("Error preparing details statement: " . $conn->error);
        }

        $details_stmt->bind_param("isssssis", $booking_id, $email, $phone, $checkIn, $checkOut, $adults, $children, $roomType);
        if (!$details_stmt->execute()) {
            throw new Exception("Error inserting booking details: " . $details_stmt->error);
        }

        $details_stmt->close();

        // Insert into activities table
        $activity_status = "Pending";
        $current_time = date("Y-m-d H:i:s");

        $activity_sql = "INSERT INTO activities (booking_id, status, time) VALUES (?, ?, ?)";
        $activity_stmt = $conn->prepare($activity_sql);
        if (!$activity_stmt) {
            throw new Exception("Error preparing activity statement: " . $conn->error);
        }

        $activity_stmt->bind_param("iss", $booking_id, $activity_status, $current_time);
        if (!$activity_stmt->execute()) {
            throw new Exception("Error inserting activity: " . $activity_stmt->error);
        }

        $activity_stmt->close();

        // Commit transaction
        $conn->commit();
        $conn->close();

        header("Location: booking.php?business_id=" . $business_id . "&success=" . urlencode("Booking successful!"));
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: booking.php?business_id=" . $business_id . "&error=" . urlencode($e->getMessage()));
        exit();
    }
}

