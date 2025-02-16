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
        // Log the activity
        $current_time = date("Y-m-d H:i:s");
        $activity_sql = "INSERT INTO activities (booking_id, status, time) VALUES (?, ?, ?)";
        $activity_stmt = $conn->prepare($activity_sql);

        if ($activity_stmt) {
            $activity_stmt->bind_param("iss", $booking_id, $status, $current_time);
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
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $departureDate = $_POST['departure_date'];
    $arrivalDate = $_POST['arrival_date'];
    $guests = intval($_POST['guests']);
    $roomType = htmlspecialchars(trim($_POST['room_type']));
    $status = "Pending";

    // Validate required fields
    if (empty($business_id) || empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($departureDate) || empty($arrivalDate) || empty($guests) || empty($roomType)) {
        echo "All fields are required!";
        exit();
    }

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit();
    }

    // Validate dates
    if (strtotime($arrivalDate) > strtotime($departureDate)) {
        echo "Arrival date cannot be after departure date!";
        exit();
    }

    // Insert the booking into the database
    $sql = "INSERT INTO bookings (first_name, last_name, email, phone, business_id, status, departure_date, arrival_date, guests, room_type, user_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssssisssiss", $firstName, $lastName, $email, $phone, $business_id, $status, $departureDate, $arrivalDate, $guests, $roomType, $user_id);

    if ($stmt->execute()) {
        // Get the last inserted booking ID
        $booking_id = $stmt->insert_id;

        // Insert into activities table
        $activity_status = "Pending";
        $current_time = date("Y-m-d H:i:s");

        $activity_sql = "INSERT INTO activities (booking_id, status, time) VALUES (?, ?, ?)";
        $activity_stmt = $conn->prepare($activity_sql);

        if ($activity_stmt) {
            $activity_stmt->bind_param("iss", $booking_id, $activity_status, $current_time);

            if (!$activity_stmt->execute()) {
                echo "Booking successful, but failed to record activity: " . $activity_stmt->error;
            }

            $activity_stmt->close();
        } else {
            echo "Booking successful, but failed to prepare activity statement: " . $conn->error;
        }

        echo "Booking successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>