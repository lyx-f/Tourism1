<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('../config/database.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../includes/src/PHPMailer.php';
require '../includes/src/SMTP.php';
require '../includes/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $action = mysqli_real_escape_string($conn, $_POST['action']);

    if (!in_array($action, ['approve', 'reject'])) {
        echo "Invalid action!";
        exit;
    }

    try {
        if ($action === 'approve') {
            // Update status to 'verified'
            $sql = "UPDATE pending_verifications SET status = 'verified' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Error preparing SQL statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                // Move business details to 'businesses' table
                $insertSql = "INSERT INTO businesses (user_id, email, phone, name, location, description, price, image_url) 
                              SELECT user_id, email, phone, name, location, description, price, image_url 
                              FROM pending_verifications WHERE id = ?";
                $insertStmt = mysqli_prepare($conn, $insertSql);
                if (!$insertStmt) {
                    throw new Exception("Error preparing INSERT statement: " . mysqli_error($conn));
                }
                mysqli_stmt_bind_param($insertStmt, "i", $id);

                if (mysqli_stmt_execute($insertStmt)) {
                    // Fetch business owner details
                    $selectOwnerSql = "SELECT user_id, email FROM pending_verifications WHERE id = ?";
                    $ownerStmt = mysqli_prepare($conn, $selectOwnerSql);
                    if (!$ownerStmt) {
                        throw new Exception("Error preparing SELECT statement: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($ownerStmt, "i", $id);
                    mysqli_stmt_execute($ownerStmt);
                    $result = mysqli_stmt_get_result($ownerStmt);
                    $ownerRow = mysqli_fetch_assoc($result);

                    if (!$ownerRow) {
                        throw new Exception("Owner details not found for ID: $id");
                    }

                    $ownerId = $ownerRow['user_id']; // Correctly use user_id
                    $ownerEmail = $ownerRow['email'];
                    
                    // Update user table: Set 'business_owner' to 1
                    $updateUserSql = "UPDATE users SET business_owner = 1 WHERE id = ?";
                    $updateUserStmt = mysqli_prepare($conn, $updateUserSql);
                    if (!$updateUserStmt) {
                        throw new Exception("Error preparing UPDATE statement: " . mysqli_error($conn));
                    }
                    mysqli_stmt_bind_param($updateUserStmt, "i", $ownerId);
                    if (mysqli_stmt_execute($updateUserStmt)) {
                        echo "Entry approved, user flagged as business owner, and moved to businesses.";
                    } else {
                        throw new Exception("Error updating user as business owner: " . mysqli_error($conn));
                    }

                    mysqli_stmt_close($updateUserStmt);
                } else {
                    throw new Exception("Error moving entry to businesses: " . mysqli_error($conn));
                }

                mysqli_stmt_close($insertStmt);
            } else {
                throw new Exception("Error approving entry: " . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt);

            // Send approval email
            sendEmail(
                $ownerEmail,
                'Congratulations! Your Business Verification has been Approved',
                "<p>Your business has been successfully verified!</p>
                 <p>To manage your business, bookings, and reports, please log in here:</p>
                 <p><a href='http://localhost/tourism/website/login.php'>Log In to Your Dashboard</a></p>
                 <p>Best regards,<br>TourMatic Team</p>"
            );
        } elseif ($action === 'reject') {
            // Update status to 'rejected'
            $sql = "UPDATE pending_verifications SET status = 'rejected' WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                throw new Exception("Error preparing SQL statement: " . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt, "i", $id);

            if (mysqli_stmt_execute($stmt)) {
                echo "Entry rejected successfully!";
            } else {
                throw new Exception("Error rejecting entry: " . mysqli_error($conn));
            }
            mysqli_stmt_close($stmt);
        }
    } catch (Exception $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}

mysqli_close($conn);

function sendEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;

        // Replace with real environment variables
        $mail->Username = 'lyxferrel@gmail.com'; 
        $mail->Password = 'fybk pnds nxju kvfv'; 

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('lyxferrel@gmail.com', 'Tourmatic Team');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        echo 'Email sent successfully!';
    } catch (Exception $e) {
        echo "Email sending failed: {$mail->ErrorInfo}";
    }
}
?>
