<?php
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../website/login.php');
    exit();
}

include('../config/database.php');
include('includes/header.php');
?>

<!DOCTYPE html>
<html>

<head>
    <title>Business Owners Panel</title>
    <link rel="stylesheet" href="../assets/css/bs-lists.css">
</head>

<body>
    <h1>Businesses</h1>

    <table border="1">
        <tr>

            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Location</th>
        </tr>
        <?php
        $sql = "SELECT * FROM businesses";
        $result = mysqli_query($conn, $sql);

        // Check if there are rows in the result
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>

                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                </tr>
            <?php }
        } else { ?>
            <tr>
                <td colspan="9" style="text-align: center;">There are no pending verifications at the moment.</td>
            </tr>
        <?php } ?>
    </table>

</body>

</html>
