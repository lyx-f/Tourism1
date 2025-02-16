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

// Validate business_id (now using POST)
if (!isset($_POST['business_id']) || !is_numeric($_POST['business_id'])) {
    die("Invalid business ID.");
}

$business_id = intval($_POST['business_id']);

// Sanitize input
$businessName = $_POST['businessName'];
$description = $_POST['description'];
$location = $_POST['location'];
$price = $_POST['price'];

// Handle file upload
$imagePath = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = '../../../assets/img';
    $fileName = basename($_FILES['image']['name']);
    $safeFileName = preg_replace('/[^a-zA-Z0-9_\.-]/', '_', $fileName);
    $imagePath = $uploadDir . $safeFileName;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        die("Error uploading file.");
    }
}

// Update the business data
$sql = "UPDATE businesses 
        SET name = ?, description = ?, location = ?, price = ?, image_url = IFNULL(?, image_url) 
        WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL prepare failed: " . $conn->error);
}

$stmt->bind_param('ssssdii', $businessName, $description, $location, $price, $imagePath, $business_id, $user_id);

if ($stmt->execute()) {
    header('Location: edit-business.php?success=1&business_id=' . $business_id);
    exit();
} else {
    die("Error updating record: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>
