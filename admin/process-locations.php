<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../website/login.php');
    exit();
}

// Include database connection
include('../config/database.php');

// Debugging: Check if POST variables are set
if (!isset($_POST['id'])) {
    echo "Invalid request.";
    exit();
}

$id = intval($_POST['id']);


// Debugging: Check values of $id and $action
if (empty($id)) {
    echo "Invalid ID or Action.";
    exit();
}

// Process the action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["updateStatus"])) {

        $query = "UPDATE locations SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "si", $_POST['updateStatus'], $_POST['id']);

        if (mysqli_stmt_execute($stmt)) {
            echo "Location status updated successfully!";
        } else {
            echo "Error updating location: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
    } else if (isset($_POST["update"]) && $_POST['update'] === 'update') {
        $query = "UPDATE locations SET name = ?, latitude = ?, longitude = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);

        mysqli_stmt_bind_param($stmt, "ssdi", $_POST['name'], $_POST['latitude'], $_POST['longitude'], $_POST['id']);

        if (mysqli_stmt_execute($stmt)) {
            echo "Location updated successfully!";
        } else {
            echo "Error updating location: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);

    } else {
        echo "Invalid ID or Action.";
        exit();
    }
}

mysqli_close($conn);
?>