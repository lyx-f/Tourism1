<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../website/login.php');
    exit();
}
include('../config/database.php');

// Fetch top 10 businesses with the highest rating
$averageQuery = "SELECT b.id, b.name, AVG(f.rating) AS average_rating
          FROM businesses b
          INNER JOIN feedbacks f ON b.id = f.destination_id
          GROUP BY b.id, b.name
          ORDER BY average_rating DESC
          LIMIT 5;";

$averageResult = $conn->query($averageQuery);

if ($averageResult) {
    if ($averageResult->num_rows > 0) {
        $destinationRatings = $averageResult->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
    } else {
        $destinationRatings = []; // Empty array if no destinations found
    }
} else {
    die("Query failed: " . $conn->error); // Handle query errors
}

// Fetch top 10 businesses with the highest rating
$highestBookingQuery = "SELECT b.id, b.name, COUNT(bk.id) AS total_bookings
FROM businesses b
JOIN bookings bk ON b.id = bk.business_id
GROUP BY b.id, b.name
ORDER BY total_bookings DESC
LIMIT 5;";

$highestBookingResult = $conn->query($highestBookingQuery);

if ($highestBookingResult) {
    if ($highestBookingResult->num_rows > 0) {
        $highestBookingData = $highestBookingResult->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
    } else {
        $highestBookingData = []; // Empty array if no destinations found
    }
} else {
    die("Query failed: " . $conn->error); // Handle query errors
}



include('includes/header.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/analytics.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <h4>Good day, dear admin <?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Unknown'; ?>( •̀ -
        •́ )</h4>
    <div class="report-container flex justify-between items-center">
        <h3 class="text-center">Generate Report</h3>
        <div>
            <button class="download-button"><a class="" href="generate_report.php" target="_blank">Download
                    Report</a></button>
        </div>
    </div>
    <div class="flex chart-container">
        <div class="ratings-chart">
            <canvas id="ratingsChart"></canvas>
        </div>
        <div class="bookings-chart">
            <canvas id="bookingsChart"></canvas>
        </div>
    </div>
</body>
<script>
    // Convert PHP array to JavaScript
    const destinationRatings = <?php echo json_encode($destinationRatings); ?>;

    // Extract labels (business names) and data (average ratings)
    const labels = destinationRatings.map(item => item.name);
    const data = destinationRatings.map(item => parseFloat(item.average_rating));

    const ratingsChart = document.getElementById('ratingsChart');

    new Chart(ratingsChart, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Average Rating',
                data: data,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 5 // Assuming ratings are from 1 to 5
                }
            }
        },

    });

    // Convert PHP array to JavaScript
    const highestBookingData = <?php echo json_encode($highestBookingData); ?>;

    // Extract labels (business names) and data (average ratings)
    const bookingLabel = highestBookingData.map(item => item.name);
    const bookingData = highestBookingData.map(item => parseFloat(item.total_bookings));

    const bookingsChart = document.getElementById('bookingsChart');

    new Chart(bookingsChart, {
        type: 'line',
        data: {
            labels: bookingLabel,
            datasets: [{
                label: 'Highest Bookings',
                data: bookingData,
                backgroundColor: 'rgba(153, 0, 255, 0.5)',
                borderColor: 'rgba(153, 0, 255, 0.5)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 5 // Assuming ratings are from 1 to 5
                }
            }
        }
    });
</script>

</html>