<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

include('../../config/database.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Capture form data
$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];



// Save message to database
$sql = "INSERT INTO admin_messages (sender_id, message, timestamp) VALUES (?, ?, NOW())";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $userId, $message);

if ($stmt->execute()) {
    // Redirect back to the dashboard with a success message
    header("Location: ../pages/contact.php?success=" . urlencode("Message successfully sent"));
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
