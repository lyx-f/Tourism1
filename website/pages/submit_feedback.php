<?php
include('../../config/database.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $destination_id = isset($_POST['destination_id']) ? intval($_POST['destination_id']) : 0;
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment = isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : '';

    if ($destination_id && $name && $rating && $comment) {
        // Insert feedback into the database
        $query = "INSERT INTO feedbacks (destination_id, name, rating, comment) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("isis", $destination_id, $name, $rating, $comment);
            if ($stmt->execute()) {
                // Redirect back to the dashboard with a success message
                header("Location: ../pages/des_info.php?id=" . $destination_id . "&success=" . urlencode("Feedback successfully submitted"));
                exit();
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "All fields are required.";
    }
} else {
    echo "Invalid request method.";
}
?>