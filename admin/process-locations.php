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
if (!isset($_POST['id']) || !isset($_POST['action'])) {
    echo "Invalid request.";
    exit();
}

$id = intval($_POST['id']);
$action = mysqli_real_escape_string($conn, $_POST['action']);

// Debugging: Check values of $id and $action
if (empty($id) || empty($action)) {
    echo "Invalid ID or Action.";
    exit();
}

// Process the action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'approve') {
        // Approve logic
        $approveSql = "UPDATE pending_verifications 
                       SET status = 'verified' 
                       WHERE id = ?";
        $stmt = mysqli_prepare($conn, $approveSql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "Entry approved successfully!";
        } else {
            echo "Error approving entry: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } elseif ($action === 'reject') {
        // Reject logic
        $rejectSql = "UPDATE pending_verifications 
                      SET status = 'rejected' 
                      WHERE id = ?";
        $stmt = mysqli_prepare($conn, $rejectSql);
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            echo "Entry rejected successfully!";
        } else {
            echo "Error rejecting entry: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Invalid action!";
    }
}

mysqli_close($conn);
?>
