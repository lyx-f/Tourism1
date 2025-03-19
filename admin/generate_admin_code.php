<?php 
session_start();
include('includes/header.php'); ?>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include '../config/database.php';
require '../includes/src/PHPMailer.php';
require '../includes/src/SMTP.php';
require '../includes/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to generate a random admin code
function generateAdminCode($length = 8) {
    return bin2hex(random_bytes($length / 2)); // Generates a random string
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email']; // The email address of the new admin
    $adminCode = generateAdminCode(); // Generate a unique admin code

    $stmt = $conn->prepare("INSERT INTO admin_codes (admin_code, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $adminCode, $email);
    if ($stmt->execute()) {
        try {
            $mail = new PHPMailer(true);

            // SMTP configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lyxferrel@gmail.com';
            $mail->Password = 'fybk pnds nxju kvfv';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  
            $mail->Port = 587;

            // Email settings/message
            $mail->setFrom('lyxferrel@gmail.com', 'Tourmatic Admin');
            $mail->addAddress($email);
            $mail->Subject = 'You have been invited as an Admin on Tourmatic';
            $mail->Body = "Hello,\n\nYou have been invited to become an admin on Tourmatic!\n\nYour Admin Code: $adminCode\n\nPlease use this code during signing up to create your admin account.";

            $mail->send();
            echo '<script>alert("Admin code generated and email sent successfully!");</script>';
        } catch (Exception $e) {
            echo '<script>alert("Error: Email could not be sent. PHPMailer Error: ' . $mail->ErrorInfo . '");</script>';
        }
    } else {
        echo '<script>alert("Error: Could not save admin code to the database.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Admin Code</title>
    <link rel="stylesheet" href="../assets/css/code.css"> 

</head>
<body>
    <form action="generate_admin_code.php" method="POST">
        <label for="email">Admin Email:</label>
        <input type="email" name="email" placeholder="Enter Email" required>
        <button type="submit">Generate Admin Code</button>
    </form>
</body>
</html>
