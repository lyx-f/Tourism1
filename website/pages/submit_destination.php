<?php
session_start();
include('../../config/database.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../website/login.php');
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!$conn) {
        die("<p style='color: red;'>Database connection failed: " . mysqli_connect_error() . "</p>");
    }

    // Fetch the user ID of the logged-in user
    $query_user = "SELECT id FROM users WHERE username = ?";
    $stmt_user = mysqli_prepare($conn, $query_user);
    if (!$stmt_user) {
        die("<p style='color: red;'>Failed to prepare statement for user ID: " . mysqli_error($conn) . "</p>");
    }

    mysqli_stmt_bind_param($stmt_user, "s", $username);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if ($row = mysqli_fetch_assoc($result_user)) {
        $user_id = $row['id'];
    } else {
        die("<p style='color: red;'>No user found with username: $username</p>");
    }
    mysqli_stmt_close($stmt_user);

    //setup post variables
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $destinationName = $_POST['destinationName'];
    $street = $_POST['street'];
    $barangay = $_POST['barangay'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $zipcode = $_POST['zipcode'];
    $description = $_POST['description'];
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;
    $image = basename($_FILES['image']['name']); // Image file


    $location = $street . ", " . $barangay  . ", " . $city . ", " . $province . ", " . $zipcode;
    $location = str_replace(", ,", ",", $location); // Remove any double commas
 
    // File upload logic
    $targetDir = "../../assets/img/";
    $targetFile = $targetDir . $image;
    $uploadOk = true;

    // Validate file type and size
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        die("<p style='color: red;'>Only JPG, JPEG, PNG, and GIF files are allowed.</p>");
    }

    if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
        die("<p style='color: red;'>File size must not exceed 5MB.</p>");
    }

    if ($uploadOk && move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        // Insert data into the pending_verifications table
        $sql = "INSERT INTO pending_verifications (user_id, email, phone, name, location, description, price, image_url, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";

        $stmt = mysqli_prepare($conn, $sql);

        if (!$stmt) {
            die("<p style='color: red;'>Failed to prepare the database statement: " . mysqli_error($conn) . "</p>");
        }

        mysqli_stmt_bind_param($stmt, "isssssss", $user_id, $email, $phone, $destinationName, $location, $description, $price, $image);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: user-profile.php?success=" . urlencode("Upload successful! Your destination is under review."));
            exit();
        } else {
            die("<p style='color: red;'>Database error during execution: " . mysqli_error($conn) . "</p>");
        }

        mysqli_stmt_close($stmt);
    } else {
        header("Location: user-profile.php?error=" . urlencode("Failed to upload image"));
        exit();
    }

    mysqli_close($conn);
}
?>