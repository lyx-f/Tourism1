<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

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
    $is_business_owner = $row_user['business_owner'];
} else {
    echo "User not found.";
    exit();
}
$stmt_user->close();

// Fetch business information
$business_name = "Your Business";
$business_id = null;

if ($is_business_owner) {
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
        echo "<p style='color: red;'>No business found for this user.</p>";
        $business_id = null;
    }
    $stmt_business->close();
}

// Ensure we focus only on bookings related to this business
if (!$business_id) {
    echo "<p style='color: red;'>No business associated with the logged-in user. Cannot display bookings.</p>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Booking Summary</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="dashboard.php">Overview</a></li>
                <li><a href="reports.php" class="active">Reports</a></li>
                <li><a href="edit-business.php?business_id=<?php echo $business_id; ?>">Edit Information</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="content">
            <h1>Booking Reports</h1>
            <p>Below is an overview of the booking activity and detailed reports for
                <strong><?php echo htmlspecialchars($business_name); ?></strong>.</p>

            <!-- Summary Section -->
            <section class="cards">
                <?php
                // Fetch booking summary data for this business
                $summary = [
                    "total" => 0,
                    "pending" => 0,
                    "confirmed" => 0,
                    "canceled" => 0,
                ];

                $query = $conn->prepare("SELECT 
                                             COUNT(*) AS total,
                                             SUM(status = 'Pending') AS pending,
                                             SUM(status = 'Accepted') AS confirmed,
                                             SUM(status = 'Cancelled') AS canceled
                                         FROM bookings WHERE business_id = ?");
                $query->bind_param("i", $business_id);
                $query->execute();
                $result = $query->get_result();

                if ($result && $row = $result->fetch_assoc()) {
                    $summary["total"] = $row["total"] ?? 0;
                    $summary["pending"] = $row["pending"] ?? 0;
                    $summary["confirmed"] = $row["confirmed"] ?? 0;
                    $summary["canceled"] = $row["canceled"] ?? 0;
                } else {
                    // Set defaults if no result
                    $summary = [
                        "total" => 0,
                        "pending" => 0,
                        "confirmed" => 0,
                        "canceled" => 0
                    ];
                }
                $query->close();

                $cardData = [
                    ["title" => "Total Bookings", "value" => $summary["total"]],
                    ["title" => "Pending Bookings", "value" => $summary["pending"]],
                    ["title" => "Confirmed Bookings", "value" => $summary["confirmed"]],
                    ["title" => "Canceled Bookings", "value" => $summary["canceled"]],
                ];

                // Render cards dynamically
                foreach ($cardData as $item) {
                    echo "<div class='card'>
                            <h3>" . htmlspecialchars($item['title']) . "</h3>
                            <p>" . htmlspecialchars($item['value']) . "</p>
                          </div>";
                }
                ?>
            </section>

            <!-- Detailed Reports Section -->
            <section class="data-table">
                <h2>Detailed Booking Report</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer Name</th>
                            <th>Status</th>
                            <th>Booking Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Fetch detailed booking data for this business
                        $detailedQuery = $conn->prepare("SELECT id, CONCAT(first_name, ' ', last_name) AS customer_name, status, arrival_date 
                                                         FROM bookings WHERE business_id = ? ORDER BY arrival_date DESC");
                        $detailedQuery->bind_param("i", $business_id);
                        $detailedQuery->execute();
                        $detailedResult = $detailedQuery->get_result();

                        if ($detailedResult->num_rows > 0) {
                            while ($row = $detailedResult->fetch_assoc()) {
                                echo "<tr>
                                        <td>B" . htmlspecialchars($row['id']) . "</td>
                                        <td>" . htmlspecialchars($row['customer_name']) . "</td>
                                        <td>" . htmlspecialchars($row['status']) . "</td>
                                        <td>" . date("d M Y", strtotime($row['arrival_date'])) . "</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No bookings found for this business.</td></tr>";
                        }

                        $detailedQuery->close();
                        $conn->close(); // Close database connection
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>