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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="dashboard-container">
        <nav class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <li><a href="dashboard.php" class="active">Overview</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="feedbacks.php">Feedbacks</a></li>
                <li><a href="edit-business.php?business_id=<?php echo $business_id; ?>">Edit Information</a></li>
                <li><a href="../logout.php">Logout</a></li>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 for popup -->

            </ul>
        </nav>

        <main class="content">
            <h1>Good day, Welcome to <?php echo htmlspecialchars($business_name); ?>'s Dashboard</h1>
            <p>Select a menu option from the sidebar to view its content.</p>

            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Business details updated successfully.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>
            <?php endif; ?>

            <section class="cards">
                <?php
                // Initialize statistics
                $total_bookings = 0;
                $new_bookings = 0;

                if ($business_id) {
                    // Fetch booking statistics
                    $query_stats = "SELECT 
                                        COUNT(*) AS total_bookings, 
                                        SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS new_bookings 
                                    FROM bookings 
                                    WHERE business_id = ?";
                    $stmt_stats = $conn->prepare($query_stats);
                    $stmt_stats->bind_param("i", $business_id);
                    $stmt_stats->execute();
                    $stmt_stats->bind_result($total_bookings, $new_bookings);
                    $stmt_stats->fetch();
                    $stmt_stats->close();
                }

                // Statistics cards
                $statistics = [
                    ["title" => "Total Bookings", "value" => $total_bookings],
                    ["title" => "Pending Bookings", "value" => $new_bookings],
                ];

                foreach ($statistics as $stat) {
                    echo "<div class='card'>
                            <h3>{$stat['title']}</h3>
                            <p>{$stat['value']}</p>
                          </div>";
                }
                ?>
            </section>

            <!-- Activities Section -->
            <section class="data-table">
                <h2>Activities</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Business ID</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($business_id) {
                            // Fetch activities for the logged-in business
                            $query_activities = "SELECT booking_id, status, time 
                                                 FROM activities 
                                                 WHERE booking_id IN 
                                                 (SELECT id FROM bookings WHERE business_id = ?)";
                            $stmt_activities = $conn->prepare($query_activities);
                            $stmt_activities->bind_param("i", $business_id);
                            $stmt_activities->execute();
                            $result_activities = $stmt_activities->get_result();

                            if ($result_activities->num_rows > 0) {
                                while ($row = $result_activities->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$row['booking_id']}</td>
                                            <td>{$row['status']}</td>
                                            <td>{$row['time']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No activities found.</td></tr>";
                            }
                            $stmt_activities->close();
                        } else {
                            echo "<tr><td colspan='3'>No business found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>

            <!-- Active Bookings Section -->
            <section class="data-table">
                <h2>Active Bookings</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Business ID</th>
                            <th>Customer Name</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($business_id) {
                            // Fetch bookings for the logged-in owner's business
                            $query_bookings = "SELECT id, first_name, last_name, status 
                                               FROM bookings 
                                               WHERE business_id = ?";
                            $stmt_bookings = $conn->prepare($query_bookings);
                            $stmt_bookings->bind_param("i", $business_id);
                            $stmt_bookings->execute();
                            $result_bookings = $stmt_bookings->get_result();

                            if ($result_bookings->num_rows > 0) {
                                while ($row = $result_bookings->fetch_assoc()) {
                                    $customerName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
                                    echo "<tr>
                                            <td>{$row['id']}</td>
                                            <td>{$customerName}</td>
                                            <td>{$row['status']}</td>
                                            <td>
                                                <a href='../process-booking.php?id={$row['id']}&action=accept' class='btn btn-accept'>Accept</a>
                                                <a href='../process-booking.php?id={$row['id']}&action=reject' class='btn btn-reject'>Reject</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No bookings found.</td></tr>";
                            }
                            $stmt_bookings->close();
                        } else {
                            echo "<tr><td colspan='4'>Business not found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
