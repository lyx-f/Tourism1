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
// Fetch feedbacks for the business
$sql_feedback = "SELECT rating FROM feedbacks WHERE destination_id = ?";
$stmt_feedback = $conn->prepare($sql_feedback);

if (!$stmt_feedback) {
    die("Prepare failed for feedback query: " . $conn->error);
}

$stmt_feedback->bind_param("i", $business_id);
$stmt_feedback->execute();
$result_feedback = $stmt_feedback->get_result();

// Initialize counters
$totalFeedbacks = 0;
$excellentFeedbacks = 0;
$goodFeedbacks = 0;
$neutralFeedbacks = 0;
$poorFeedbacks = 0;
$veryPoorFeedbacks = 0;

// Process feedback results
while ($row_feedback = $result_feedback->fetch_assoc()) {
    $totalFeedbacks++;

    switch ($row_feedback['rating']) {
        case 5:
            $excellentFeedbacks++;
            break;
        case 4:
            $goodFeedbacks++;
            break;
        case 3:
            $neutralFeedbacks++;
            break;
        case 2:
            $poorFeedbacks++;
            break;
        case 1:
            $veryPoorFeedbacks++;
            break;
    }
}

$stmt_feedback->close();



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
                <strong><?php echo htmlspecialchars($business_name); ?></strong>.
            </p>
            <section id="ratingsChart"
                style="padding:10px; border:solid 1px gray; border-radius: 10px; height:200px; background-color:white;   box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); margin-bottom:20px;">
                <canvas id="feedbackChart"></canvas>
            </section>
            <section style="border-top:solid 1px gray; padding-top: 20px; "> 


                <?php
                $selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
                $selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

                $months = [
                    "01" => "January",
                    "02" => "February",
                    "03" => "March",
                    "04" => "April",
                    "05" => "May",
                    "06" => "June",
                    "07" => "July",
                    "08" => "August",
                    "09" => "September",
                    "10" => "October",
                    "11" => "November",
                    "12" => "December"
                ];

                echo "<form style='width: fit-content; display: inline-flex; flex-direction:row; margin-bottom: 10px;' method='GET' action=''>
                <div>
                <label style='width: fit-content;' for='month'>Month:</label>
                <select name='month' id='month'>";
                foreach ($months as $key => $value) {
                    echo "<option value='$key' " . ($selected_month == $key ? "selected" : "") . ">$value</option>";
                }
                echo "</select>
                        </div>
                        <div>
                <label style='width: fit-content;' for='year'>Year:</label>
                <select name='year' id='year'>";
                for ($y = date('Y'); $y >= date('Y') - 5; $y--) {
                    echo "<option value='$y' " . ($selected_year == $y ? "selected" : "") . ">$y</option>";
                }
                echo "</select>
                        </div>
                <button style='width:fit-content; padding: 4px 10px 4px 10px; font-size: 14px' type='submit'>Filter</button>
              </form>";

                ?>

                <form style="width: fit-content" method="GET" action="generate_report.php">
                    <input type="hidden" name="month" value="<?php echo $selected_month; ?>">
                    <input type="hidden" name="year" value="<?php echo $selected_year; ?>">
                    <button type="submit" style="padding: 6px 12px; font-size: 14px;">Download PDF</button>
                </form>
                <?php

                // Initialize counters before executing the query
                $totalBookings = 0;
                $pendingBookings = 0;
                $confirmedBookings = 0;
                $cancelledBookings = 0;

                // Fetch detailed booking data for this business
                $query = $conn->prepare("SELECT id, CONCAT(first_name, ' ', last_name) AS customer_name, status, arrival_date FROM bookings WHERE business_id = ? AND MONTH(arrival_date) = ? AND YEAR(arrival_date) = ? ORDER BY arrival_date DESC");
                $query->bind_param("iii", $business_id, $selected_month, $selected_year);
                $query->execute();
                $result = $query->get_result();

                // Process the results first
                $bookings = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $totalBookings++;

                        // Count bookings based on status
                        switch (strtolower($row['status'])) {
                            case 'pending':
                                $pendingBookings++;
                                break;
                            case 'accepted':
                                $confirmedBookings++;
                                break;
                            case 'cancelled':
                            case 'canceled': // Handling both spelling variations
                                $cancelledBookings++;
                                break;
                        }

                        $bookings[] = $row; // Store row data for later use
                    }
                }

                $query->close();

                ?>

                <section class="cards">
                    <div class="card">
                        <h3>Total Bookings</h3>
                        <p><?php echo $totalBookings; ?></p>
                    </div>
                    <div class="card">
                        <h3>Pending</h3>
                        <p><?php echo $pendingBookings; ?></p>
                    </div>
                    <div class="card">
                        <h3>Accepted</h3>
                        <p><?php echo $confirmedBookings; ?></p>
                    </div>
                    <div class="card">
                        <h3>Cancelled</h3>
                        <p><?php echo $cancelledBookings; ?></p>
                    </div>
                </section>


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
                            if (!empty($bookings)) {
                                foreach ($bookings as $row) {
                                    echo "<tr>
                            <td>B" . htmlspecialchars($row['id']) . "</td>
                            <td>" . htmlspecialchars($row['customer_name']) . "</td>
                            <td>" . htmlspecialchars($row['status']) . "</td>
                            <td>" . date("d M Y", strtotime($row['arrival_date'])) . "</td>
                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>No bookings found for the selected period.</td></tr>";
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </section>
            </section>
        </main>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // PHP variables converted into JavaScript
        const totalFeedbacks = <?php echo $totalFeedbacks; ?>;
        const excellentFeedbacks = <?php echo $excellentFeedbacks; ?>;
        const goodFeedbacks = <?php echo $goodFeedbacks; ?>;
        const neutralFeedbacks = <?php echo $neutralFeedbacks; ?>;
        const poorFeedbacks = <?php echo $poorFeedbacks; ?>;
        const veryPoorFeedbacks = <?php echo $veryPoorFeedbacks; ?>;

        // Get the canvas context
        const ctx = document.getElementById("feedbackChart").getContext("2d");

        // Create the chart
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Excellent (5★)", "Good (4★)", "Neutral (3★)", "Poor (2★)", "Very Poor (1★)"],
                datasets: [{
                    label: "Number of Feedbacks",
                    data: [excellentFeedbacks, goodFeedbacks, neutralFeedbacks, poorFeedbacks, veryPoorFeedbacks],
                    backgroundColor: [
                        "rgba(46, 204, 113, 0.8)",  // Green (Excellent)
                        "rgba(52, 152, 219, 0.8)",  // Blue (Good)
                        "rgba(241, 196, 15, 0.8)",  // Yellow (Neutral)
                        "rgba(230, 126, 34, 0.8)",  // Orange (Poor)
                        "rgba(231, 76, 60, 0.8)"    // Red (Very Poor)
                    ],
                    borderColor: [
                        "rgba(46, 204, 113, 1)",
                        "rgba(52, 152, 219, 1)",
                        "rgba(241, 196, 15, 1)",
                        "rgba(230, 126, 34, 1)",
                        "rgba(231, 76, 60, 1)"
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: "Feedback Count"
                        }
                    }
                }
            }
        });
    });
</script>

</html>