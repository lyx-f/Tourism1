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
    <title>Verification Panel</title>
    <link rel="stylesheet" href="../assets/css/verification.css">
    <script>
        function handleAction(id, action) {
            if (confirm(`Are you sure you want to ${action} this entry?`)) {
                const formData = new FormData();
                formData.append('id', id);
                formData.append('action', action);

                fetch('../admin/process_verification.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>
</head>

<body>
    <h1>Pending Verifications</h1>

    <table border="1">
        <tr>

            <th>Email</th>
            <th>Phone</th>
            <th>Name</th>
            <th>Location</th>
            <th>Description</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php
        $sql = "SELECT * FROM pending_verifications WHERE status = 'pending'";
        $result = mysqli_query($conn, $sql);

        // Check if there are rows in the result
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>

                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><img src="../assets/img/<?php echo htmlspecialchars($row['image_url']); ?>" width="100"></td>
                    <td>
                        <button onclick="handleAction(<?php echo $row['id']; ?>, 'approve')">Approve</button>
                        <button onclick="handleAction(<?php echo $row['id']; ?>, 'reject')">Reject</button>
                    </td>
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