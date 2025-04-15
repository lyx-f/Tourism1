<?php
session_start();
include('../config/database.php');
include('includes/header.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch messages from database
$sql = "SELECT u.username, u.email, m.id, m.message, m.timestamp FROM admin_messages m JOIN users u on u.id =sender_id ORDER BY timestamp DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/chat.css">
    <script>
        // Auto-refresh the page every 10 seconds
        setInterval(() => {
            window.location.reload();
        }, 10000);
    </script>
</head>

<body>
    <div class="dashboard-container">
        <h1>Messages</h1>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Message</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                        <td><?php echo htmlspecialchars($row['timestamp']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
<?php
$conn->close();
?>