<?php
session_start();
include "../../../config/database.php";

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


$user_id = $_SESSION['user_id'];

// Initialize $is_business_owner to avoid undefined variable warnings
$is_business_owner = 0;

// Fetch user details
$sql_user = "SELECT id, username, role, business_owner FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);

if (!$stmt_user) {
    die("Prepare failed for user query: " . $conn->error);
}

$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($row_user = $result_user->fetch_assoc()) {
    $username = $row_user['username'];
    $role = $row_user['role'];
    $is_business_owner = $row_user['business_owner']; // Now always defined
} else {
    echo "User not found.";
    exit();
}
$stmt_user->close();

// Fetch business information
$business_name = "Your Business";
$business_id = null;

if ($is_business_owner) {
    // Correct query: Using `user_id` to fetch the business details
    $sql_business = "SELECT id, name FROM businesses WHERE user_id = ?";
    $stmt_business = $conn->prepare($sql_business);

    if (!$stmt_business) {
        die("Prepare failed for business query: " . $conn->error);
    }

    $stmt_business->bind_param("i", $user_id);
    $stmt_business->execute();
    $result_business = $stmt_business->get_result();

    if ($row_business = $result_business->fetch_assoc()) {
        $business_id = $row_business['id'];
        $business_name = $row_business['name'];
    } else {
        echo "<p>No business found for this user.</p>";
        $business_id = null;
    }
    $stmt_business->close();
}


function fetchMessages($conn, $business_id)
{
    $sql = "SELECT * 
            FROM feedbacks
            WHERE destination_id = ?;";

    // Prepare statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed for business query: " . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("i", $business_id);
    $stmt->execute();

    return $stmt->get_result();
}

$messages = fetchMessages($conn, $business_id);
?>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .message-section {
        border: 1px solid lightgray;
        border-radius: 20px;
        height: calc(100vh - 50%);
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
</style>

<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Overview</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="feedbacks.php">Feedbacks</a></li>
                <li><a href="messages.php">Messages</a></li>
                <li><a href="edit-business.php?business_id=<?php echo $business_id; ?>">Edit Information</a></li>
                <li><a href="../logout.php">Logout</a></li>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for popup -->

            </ul>
        </nav>
        <main class="content">
            <h1>Feedbacks</h1>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>

                    <!-- Display Messages -->
                    <?php if ($messages && $messages->num_rows > 0): ?>
                        <?php while ($row = $messages->fetch_assoc()): ?>
                            <tr onclick="window.location.href='./message.php?conversation_id=<?= urlencode($row['id']) ?>'">
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['rating']) ?></td>
                                <td><?= htmlspecialchars($row['comment']) ?></td>
                                <td><?= htmlspecialchars($row['created_at']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan='3'>No activities found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </main>
    </div>
</body>

</html>