<?php
include '../config/database.php';
include '../model/signup.model.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = new User($conn);

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeatPassword = $_POST['confirmpass'];
    $adminCode = $_POST['admin_code'] ?? null; // Admin code (optional)

    // Default role is 'user'
    $role = 'user';

    // If admin code is provided, validate it and set the role to 'admin'
    if ($adminCode) {
        $stmt = $conn->prepare("SELECT * FROM admin_codes WHERE admin_code = ? AND email = ? AND is_used = 0");
        $stmt->bind_param("ss", $adminCode, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo '<script>alert("Invalid or already used admin code.");</script>';
            exit();
        }

        // If admin code is valid, mark it as used and set the role to admin
        $updateStmt = $conn->prepare("UPDATE admin_codes SET is_used = 1 WHERE admin_code = ?");
        $updateStmt->bind_param("s", $adminCode);
        $updateStmt->execute();

        // Set role to 'admin'
        $role = 'admin';
    }

    // Validate inputs
    if (empty($username) || empty($password) || empty($email) || empty($repeatPassword)) {
        echo '<script>alert("All fields are required. Please fill them out.");</script>';
    } elseif ($password !== $repeatPassword) {
        echo '<script>alert("Passwords do not match. Please try again.");</script>';
    } elseif ($user->checkUsernameAndEmail($username, $email)) {
        echo '<script>alert("Username or email is already taken.");</script>';
    } else {
        // Register the user with the assigned role (user or admin)
        $result = $user->registerUser($username, $password, $email, $role);

        if ($result) {
            header("Location: ../website/login.php");
            exit();
        } else {
            echo '<script>alert("Registration failed. Please try again.");</script>';
        }
    }
}
?>
