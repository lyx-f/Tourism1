<?php
session_start();
require_once('../../../config/database.php'); // Adjust path as needed
require_once('../../../vendor/autoload.php'); // Load TCPDF via Composer

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql_user = "SELECT id, username, role, business_owner FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if (!$row_user = $result_user->fetch_assoc()) {
    die("User not found.");
}
$stmt_user->close();

$business_name = "Your Business";
$business_id = null;

if ($row_user['business_owner']) {
    $sql_business = "SELECT id, name FROM businesses WHERE user_id = ?";
    $stmt_business = $conn->prepare($sql_business);
    $stmt_business->bind_param("i", $user_id);
    $stmt_business->execute();
    $result_business = $stmt_business->get_result();

    if ($row_business = $result_business->fetch_assoc()) {
        $business_id = $row_business['id'];
        $business_name = $row_business['name'];
    }
    $stmt_business->close();
}

if (!$business_id) {
    die("No business associated with this user.");
}

$selected_month = isset($_GET['month']) ? $_GET['month'] : date('m');
$selected_year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Fetch booking data
$query = $conn->prepare("SELECT id, CONCAT(first_name, ' ', last_name) AS customer_name, status, arrival_date FROM bookings WHERE business_id = ? AND MONTH(arrival_date) = ? AND YEAR(arrival_date) = ? ORDER BY arrival_date DESC");
$query->bind_param("iii", $business_id, $selected_month, $selected_year);
$query->execute();
$result = $query->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = $row;
}
$query->close();
$conn->close();

// Create PDF
$pdf = new TCPDF();
$pdf->SetCreator('Business Report System');
$pdf->SetAuthor($business_name);
$pdf->SetTitle('Booking Report');
$pdf->SetHeaderData('', 0, "Booking Report - $business_name", "Month: $selected_month | Year: $selected_year");
$pdf->setHeaderFont(['helvetica', '', 12]);
$pdf->setFooterFont(['helvetica', '', 10]);
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

// Content
$html = "<h1>Booking Report</h1>
<p><strong>Business:</strong> $business_name</p>
<p><strong>Month:</strong> $selected_month | <strong>Year:</strong> $selected_year</p>
<table border='1' cellpadding='5'>
<tr>
<th>Booking ID</th>
<th>Customer Name</th>
<th>Status</th>
<th>Booking Date</th>
</tr>";

foreach ($bookings as $row) {
    $html .= "<tr>
    <td>B" . htmlspecialchars($row['id']) . "</td>
    <td>" . htmlspecialchars($row['customer_name']) . "</td>
    <td>" . htmlspecialchars($row['status']) . "</td>
    <td>" . date("d M Y", strtotime($row['arrival_date'])) . "</td>
    </tr>";
}

$html .= "</table>";
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output("Booking_Report.pdf", "D");
?>
