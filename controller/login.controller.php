<?php
session_start();
include "../config/database.php";
include "../model/login.model.php";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the submitted username/email and password
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Username/Email and password are required!";
        header('Location: ../website/login.php');
        exit();
    }

    // Query to check if the user exists
    $sql = "SELECT id, username, email, password_hash, role, business_owner 
            FROM users 
            WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $username, $username); // Check against both username and email
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['business_owner'] = $user['business_owner'];

            // Check user role and redirect
            if ($user['role'] === 'admin') {
                $_SESSION['admin_name'] = $user['username']; // Store admin's username
                header('Location: ../admin/index.php'); // Admin Dashboard
                exit();
            } elseif ($user['business_owner'] == 1) {
                // Redirect business owner
                header('Location: ../website/pages/bsowner/dashboard.php'); // Business Owner Dashboard
                exit();
            } else {
                // Redirect regular user
                header('Location: ../website/pages/homepage.php'); // User Homepage
                exit();
            }
        } else {
            // Invalid password
            $_SESSION['error'] = "Invalid username/email or password!";
            header('Location: ../website/login.php');
            exit();
        }
    } else {
        // User not found
        $_SESSION['error'] = "Invalid username/email or password!";
        header('Location: ../website/login.php');
        exit();
    }
}

// If the request method is not POST, redirect to login page
header('Location: ../website/login.php');
exit();
?>
