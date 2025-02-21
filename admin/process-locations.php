<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../website/login.php');
    exit();
}

// Include database connection
include('../config/database.php');


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
        $name = $_POST["name"];
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];
    
        // Insert query using prepared statement
        $stmt = $conn->prepare("INSERT INTO locations (name, latitude, longitude) VALUES (?, ?, ?)");
        $stmt->bind_param("sdd", $name, $latitude, $longitude);
    
        if ($stmt->execute()) {
            echo "Location added successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    
        $stmt->close();
    }
}

mysqli_close($conn);
?>