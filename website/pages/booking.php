<?php
include("../../includes/homepage_navbar.php");
include('../../config/database.php');


// Get business_id from URL or session
$business_id = isset($_GET['business_id']) ? intval($_GET['business_id']) : (isset($_SESSION['business_id']) ? $_SESSION['business_id'] : null);

if (empty($business_id)) {
    die("Error: No business selected for booking. Please go back and select a business.");
}

// Store business_id in session
$_SESSION['business_id'] = $business_id;

// Fetch business details from the database
$query = "SELECT category FROM businesses WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $business_id);
$stmt->execute();
$result = $stmt->get_result();
$business = $result->fetch_assoc();

$category = $business['category'] ?? 'accommodations'; // Default category
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ucfirst($category) ?> Booking</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/booking.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>

<body>
    <div class="booking-form">
        <h2><?= ucfirst($category) ?> Booking Form</h2>
        <form action="process-booking.php" method="POST">
            <input type="hidden" name="business_id" value="<?= htmlspecialchars($business_id); ?>">
            <input type="hidden" name="category" value="<?= htmlspecialchars($category); ?>">

            <?php if ($category === 'accommodations'): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="checkin">Check-in Date & Time:</label>
                        <input type="datetime-local" id="checkin" name="checkin" required onchange="calculateBill()">
                    </div>
                    <div class="form-group">
                        <label for="checkout">Check-out Date & Time:</label>
                        <input type="datetime-local" id="checkout" name="checkout" required onchange="calculateBill()">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="adults">Number of Adults:</label>
                        <input type="number" id="adults" name="adults" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="children">Number of Children:</label>
                        <input type="number" id="children" name="children" min="0">
                    </div>
                </div>

                <div class="form-group">
                    <label for="room_type">Room Type:</label>
                    <select id="room_type" name="room_type" required onchange="calculateBill()">
                        <option value="standard">Standard Room</option>
                        <option value="double">Double Room</option>
                        <option value="family">Family Room</option>
                        <option value="king">King Room</option>
                        <option value="queen">Queen Room</option>
                        <option value="deluxe">Deluxe Room</option>
                        <option value="suite">Suite</option>
                    </select>
                </div>


            <?php elseif ($category === 'attractions'): ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="visit_date">Visit Date:</label>
                    <input type="date" id="visit_date" name="visit_date" required>
                </div>

                <h3>Select Ticket Type and Quantity</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="adult_quantity">Adult Tickets ($50 each):</label>
                        <input type="number" id="adult_quantity" name="adult_quantity" min="0" value="0" required
                            onchange="calculateBill()">
                    </div>
                    <div class="form-group">
                        <label for="senior_quantity">Senior Tickets ($40 each):</label>
                        <input type="number" id="senior_quantity" name="senior_quantity" min="0" value="0" required
                            onchange="calculateBill()">
                    </div>
                    <div class="form-group">
                        <label for="child_quantity">Child Tickets ($30 each):</label>
                        <input type="number" id="child_quantity" name="child_quantity" min="0" value="0" required
                            onchange="calculateBill()">
                    </div>
                </div>

                <h3>Select Accommodation</h3>
                <div class="form-group">
                    <label for="accommodation_type">Accommodation Type:</label>
                    <select id="accommodation_type" name="accommodation_type" required onchange="toggleAccommodation()">
                        <option value="" disabled selected>Select category</option>
                        <option value="cottage">Cottage</option>
                        <option value="room">Room</option>
                    </select>
                </div>

                <!-- Cottage Options -->
                <div id="cottage_options" style="display: none;">
                    <div class="form-group">
                        <label for="attractions_cottage_type">Select Cottage Type:</label>
                        <select id="attractions_cottage_type" name="attractions_cottage_type" required>
                            <option value="standard">Standard - $50</option>
                            <option value="family">Family - $120</option>
                            <option value="group">Group - $250</option>
                        </select>
                    </div>
                </div>

                <!-- Room Options -->
                <div id="room_options" style="display: none;">
                    <div class="form-group">
                        <label for="attractions_room_type">Room Type:</label>
                        <select id="attractions_room_type" name="attractions_room_type">
                            <option value="single">Single - $80</option>
                            <option value="double">Double - $120</option>
                            <option value="family">Family - $200</option>
                        </select>
                    </div>
                </div>

            <?php elseif ($category === 'restaurants'): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                </div>


                <div class="form-group">
                    <label for="reservation_datetime">Reservation Date & Time:</label>
                    <input type="datetime-local" id="reservation_datetime" name="reservation_datetime" required>
                </div>
                <div class="form-group">
                    <label for="reservation_type">Reservation Type:</label>
                    <select id="reservation_type" name="reservation_type" required onchange="toggleReservationType()">
                        <option value="dining">Dining</option>
                        <option value="special_event">Special Event</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group" id="other_specify" style="display: none;">
                    <label for="other_details">If Other, please specify:</label>
                    <input type="text" id="other_details" name="other_details" placeholder="Specify your reservation">
                </div>

                <div class="form-group">
                    <label for="special_requests">Any Special Requests:</label>
                    <textarea id="special_requests" name="special_requests"
                        placeholder="Enter special requests here"></textarea>
                </div>

                <div class="form-group">
                    <label for="guests">Number of Guests:</label>
                    <input type="number" id="guests" name="guests" min="1" required onchange="calculateBill()">
                </div>

            <?php endif; ?>

            <p id="total_bill">Total Bill: $0.00</p>
            <button type="submit" class="btn-submit" name="category" value="<?= $category ?>">BOOK NOW</button>
        </form>
    </div>


    <?php include("../../includes/footer.php"); ?>
</body>

<script>
      
    document.addEventListener("DOMContentLoaded", function () {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
        })
        const urlParams = new URLSearchParams(window.location.search);
        const successMessage = urlParams.get("success");
        const error = urlParams.get("error");

        if (successMessage) {
            Toast.fire({
                icon: "success",
                title: successMessage,
            });
        }else if(error){
            Toast.fire({
                icon: "error",
                title: "Something went wrong",
            });
        }
    });

    function calculateBill() {
        let billAmount = 0;
        const category = "<?= $category ?>";

        if (category === 'accommodations') {
            const checkin = new Date(document.getElementById('checkin').value);
            const checkout = new Date(document.getElementById('checkout').value);
            const roomType = document.getElementById('room_type').value;
            const nights = (checkout - checkin) / (1000 * 60 * 60 * 24);

            let roomRates = { 'standard': 100, 'double': 150, 'family': 300, 'king': 400, 'queen': 350, 'suite': 250 };
            billAmount = nights * (roomRates[roomType] || 0);
        } else if (category === 'restaurants') {
            const guests = parseInt(document.getElementById('guests').value);
            const pricePerGuest = 20;
            billAmount = guests * pricePerGuest;
        } else if (category === 'attractions') {
            const adultQty = parseInt(document.getElementById('adult_quantity').value) || 0;
            const seniorQty = parseInt(document.getElementById('senior_quantity').value) || 0;
            const childQty = parseInt(document.getElementById('child_quantity').value) || 0;

            let ticketRates = { adult: 50, senior: 40, child: 30 };
            billAmount = (adultQty * ticketRates.adult) + (seniorQty * ticketRates.senior) + (childQty * ticketRates.child);
        }
        document.getElementById('total_bill').innerText = `Total Bill: $${billAmount.toFixed(2)}`;
    }

    function toggleAccommodation() {
        var selectedType = document.getElementById("accommodation_type").value;

        // Show or hide the correct option
        document.getElementById("cottage_options").style.display = selectedType === "cottage" ? "block" : "none";
        document.getElementById("room_options").style.display = selectedType === "room" ? "block" : "none";
    }

    function toggleReservationType() {
        let reservationType = document.getElementById('reservation_type').value;
        let otherSpecify = document.getElementById('other_specify');
        if (reservationType === 'other') {
            otherSpecify.style.display = 'block';
        } else {
            otherSpecify.style.display = 'none';
        }
    }
</script>

</html>