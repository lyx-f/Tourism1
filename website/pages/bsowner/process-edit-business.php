<?php
session_start();
include "../../../config/database.php";

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate business ID
if (!isset($_POST['business_id']) || !is_numeric($_POST['business_id'])) {
    die("Invalid business ID provided.");
}

$business_id = intval($_POST['business_id']);

// Fetch existing images
$sql_fetch = "SELECT main_image, image_url FROM businesses WHERE id = ? AND user_id = ?";
$stmt_fetch = $conn->prepare($sql_fetch);
$stmt_fetch->bind_param("ii", $business_id, $user_id);
$stmt_fetch->execute();
$result_fetch = $stmt_fetch->get_result();

$existing_main_image = "";
$existing_images = [];

if ($row = $result_fetch->fetch_assoc()) {
    $existing_main_image = $row['main_image'];
    $existing_images = explode(',', $row['image_url']);
}

$stmt_fetch->close();

// File upload directory
$targetDir = "../../../assets/img/";
$uploadedImages = [];
$mainImageName = $existing_main_image; // Default to existing main image if no new upload

// ✅ Process Main Image Upload (if provided)
if (!empty($_FILES['main_image']['name'])) {
    $mainTmpName = $_FILES['main_image']['tmp_name'];
    $mainSize = $_FILES['main_image']['size'];
    $mainFileType = strtolower(pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION));

    // Validate file type
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($mainFileType, $allowedTypes)) {
        die("Only JPG, JPEG, PNG, and GIF files are allowed for the main image.");
    }

    // Validate file size (max 5MB)
    if ($mainSize > 5 * 1024 * 1024) {
        die("Main image size must not exceed 5MB.");
    }

    // Generate unique file name
    $mainImageName = time() . "_" . uniqid() . "." . $mainFileType;
    $mainImagePath = $targetDir . $mainImageName;

    // Move file to the destination folder
    if (!move_uploaded_file($mainTmpName, $mainImagePath)) {
        die("Error uploading main image.");
    }
}

// ✅ Process Additional Images Upload
foreach ($_FILES['images']['name'] as $key => $imageName) {
    if (!empty($_FILES['images']['name'][$key])) {
        $imageTmpName = $_FILES['images']['tmp_name'][$key];
        $imageSize = $_FILES['images']['size'][$key];
        $imageFileType = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($imageFileType, $allowedTypes)) {
            die("Only JPG, JPEG, PNG, and GIF files are allowed.");
        }

        // Validate file size (max 5MB)
        if ($imageSize > 5 * 1024 * 1024) {
            die("File size must not exceed 5MB per image.");
        }

        // Generate unique file name
        $uniqueName = time() . "_" . uniqid() . "." . $imageFileType;
        $targetFile = $targetDir . $uniqueName;

        // Move file to the destination folder
        if (move_uploaded_file($imageTmpName, $targetFile)) {
            $uploadedImages[] = $uniqueName;
        }
    }
}

// Merge existing images with new ones
$finalImages = array_merge($existing_images, $uploadedImages);
$imageString = implode(',', $finalImages);

// ✅ Update business info in the database
$sql_update = "UPDATE businesses SET main_image = ?, image_url = ? WHERE id = ? AND user_id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("ssii", $mainImageName, $imageString, $business_id, $user_id);

if ($stmt_update->execute()) {
    header("Location: edit-business.php?business_id=$business_id&success=1");
    exit();
} else {
    die("Error updating business: " . $conn->error);
}

$stmt_update->close();
$conn->close();
?>
