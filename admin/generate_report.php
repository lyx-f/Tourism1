<?php
require_once('../vendor/autoload.php'); // Load TCPDF via Composer
require_once('../config/database.php'); // Load database connection

session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../website/login.php');
  exit();
}

// Prevent accidental output before PDF generation
ob_start(); 

// Fetch data from the database
$averageQuery = "SELECT b.id, b.name, AVG(f.rating) AS average_rating
                 FROM businesses b
                 INNER JOIN feedbacks f ON b.id = f.destination_id
                 GROUP BY b.id, b.name
                 ORDER BY average_rating DESC
                 LIMIT 5;";
$averageResult = $conn->query($averageQuery);
$destinationRatings = ($averageResult && $averageResult->num_rows > 0) ? $averageResult->fetch_all(MYSQLI_ASSOC) : [];

$highestBookingQuery = "SELECT b.id, b.name, COUNT(bk.id) AS total_bookings
                        FROM businesses b
                        JOIN bookings bk ON b.id = bk.business_id
                        GROUP BY b.id, b.name
                        ORDER BY total_bookings DESC
                        LIMIT 5;";
$highestBookingResult = $conn->query($highestBookingQuery);
$highestBookingData = ($highestBookingResult && $highestBookingResult->num_rows > 0) ? $highestBookingResult->fetch_all(MYSQLI_ASSOC) : [];

// Clear output buffer before PDF generation
ob_clean();

// Create new PDF document
$pdf = new TCPDF();
$pdf->SetCreator('TourMatic');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('TourMatic Analytics Report');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);

// Report Title
$pdf->Cell(0, 10, 'TourMatic Analytics Report', 0, 1, 'C');
$pdf->Ln(5);

// =========================== //
// Highest Rated Businesses Table //
// =========================== //
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Highest Rated Businesses', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Table Header
$pdf->SetFillColor(200, 200, 200); // Gray background
$pdf->Cell(10, 8, '#', 1, 0, 'C', true);
$pdf->Cell(120, 8, 'Business Name', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Average Rating', 1, 1, 'C', true);

$pdf->SetFont('helvetica', '', 10);
$count = 1;
foreach ($destinationRatings as $destination) {
    $pdf->Cell(10, 8, $count++, 1, 0, 'C');
    $pdf->Cell(120, 8, $destination['name'], 1, 0, 'L');
    $pdf->Cell(40, 8, number_format($destination['average_rating'], 2), 1, 1, 'C');
}
$pdf->Ln(5);

// =========================== //
// Most Booked Businesses Table //
// =========================== //
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Most Booked Businesses', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);

// Table Header
$pdf->SetFillColor(200, 200, 200); // Gray background
$pdf->Cell(10, 8, '#', 1, 0, 'C', true);
$pdf->Cell(120, 8, 'Business Name', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Total Bookings', 1, 1, 'C', true);

$pdf->SetFont('helvetica', '', 10);
$count = 1;
foreach ($highestBookingData as $booking) {
    $pdf->Cell(10, 8, $count++, 1, 0, 'C');
    $pdf->Cell(120, 8, $booking['name'], 1, 0, 'L');
    $pdf->Cell(40, 8, $booking['total_bookings'], 1, 1, 'C');
}
$pdf->Ln(10);

// Output PDF
$pdf->Output('tourmatic_report.pdf', 'D'); // 'D' forces download
exit();
?>
