<?php
session_start();

// Check if logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../../website/login.php');
    exit();
}

include "../../config/database.php";
$userId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["fetchBookings"])) {

    $sql = "SELECT 
                b.id AS booking_id, 
                b.status AS status,
                biz.id AS business_id, 
                biz.name AS business_name, 
                biz.location, 
                biz.category,
                CASE 
                    WHEN biz.category = 'accommodations' THEN acc.checkin
                    WHEN biz.category = 'attractions' THEN attr.visit_date
                    WHEN biz.category = 'restaurants' THEN res.reservation_datetime
                    ELSE NULL 
                END AS date
            FROM bookings AS b
            LEFT JOIN businesses AS biz ON b.business_id = biz.id
            LEFT JOIN accommodations_booking_details AS acc ON b.id = acc.booking_id AND biz.category = 'accommodations'
            LEFT JOIN attractions_booking_details AS attr ON b.id = attr.booking_id AND biz.category = 'attractions'
            LEFT JOIN restaurants_booking_details AS res ON b.id = res.booking_id AND biz.category = 'restaurants'
            WHERE b.user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            "id" => htmlspecialchars($row["booking_id"]),
            "business_id" => htmlspecialchars($row["business_id"]),
            "name" => htmlspecialchars($row["business_name"]),
            "date" => htmlspecialchars($row["date"]), // Dynamic date based on category
            "location" => htmlspecialchars($row["location"]),
            "category" => htmlspecialchars($row["category"]),
            "status" => htmlspecialchars($row["status"]),
        ];
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($data);

    // Close connections
    $stmt->close();
    $conn->close();
    exit();
}

include("../../includes/homepage_navbar.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Submission</title>
    <link rel="stylesheet" href="../../assets/css/homepage.css">
    <link rel="stylesheet" href="../../assets/css/footer.css">
    <link rel="stylesheet" href="../../assets/css/user-profile.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<style>
    /* Booking Modal */
    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        /* background: white; */
        padding: 20px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        z-index: 1000;
    }

    .modal-content {
        text-align: center;
    }

    .close-btn {
        float: right;
        font-size: 20px;
        cursor: pointer;
    }

    /* Chat Button Inside Each Booking */
    .chat-now-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
        margin-top: 10px;
    }

    /* Chat Modal */
    .chat-modal {
        display: none;
        position: fixed;
        bottom: 10px;
        right: 20px;
        width: 500px;
        height: 400px;
        background: #f0ebe3;
        color: #1e1e1e;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px 8px 8px 8px;
        overflow: clip;
        z-index: 1000;
        flex-direction: column;
    }


    /* Chat Header */
    .chat-header {
        background: rgb(2, 90, 90);
        color: white;
        padding: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }


    .chat-close {
        font-size: 18px;
        cursor: pointer;
    }

    /* Chat Body */
    .chat-body {
        height: calc(100% - 50px);
        flex: 1;
        display: flex;
        justify-content: space-between;
        flex-direction: column;
        padding: 10px;
    }


    /* Scrollable Messages */
    .chat-messages {
        flex-grow: 1;
        overflow-y: auto;
        max-height: 250px;
        /* Limit height for scrolling */
        padding: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border-bottom: 1px solid #ccc;
    }


    /* Message Bubbles */
    .message {
        padding: 8px 12px;
        border-radius: 10px;
        max-width: 70%;
        word-wrap: break-word;
    }

    /* Received (Align Left) */
    .received {
        background: #e0e0e0;
        align-self: flex-start;
    }

    /* Sent (Align Right) */
    .sent {
        background: rgb(2, 90, 90);
        color: white;
        align-self: flex-end;
    }

    .send-message-section {
        display: flex;
        background-color: black;
        flex-direction: column;
    }

    .chat-container {
        margin-top: 10px;
        display: flex;
        gap: 10px;
        flex-direction: row;
    }

    /* Chat Input */
    textarea {
        margin-top: 0px;
        height: 60px;
        height: 40px;
        border: 1px solid #ccc;
        border-radius: 10px;
        resize: none;
    }

    /* Send Button */
    #sendChatBtn {
        background: rgb(2, 90, 90);
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 10px;
        width: 100px;
    }

    #sendChatBtn:hover {
        background: rgb(2, 82, 82);
    }
</style>

<body>
    <div class="profile-container">
        <div class="profile">
            <i class="fas fa-user-circle fa-4x profile-icon"></i>
            <h2><?= $_SESSION['username'] ?></h2>
            <button class="toggle-btn" id="openForm">+</button>
        </div>
        <div class="icon-row">
            <div class="icon-item">
                <button id="bookingInfoBtn" style="all: unset; cursor: pointer;">
                    <i class="fas fa-history"></i>
                    <span>Booking Info</span>
                </button>
            </div>
            <div class="separator"></div>
            <div class="icon-item">
                <form action="../pages/logout.php" method="POST">
                    <button type="submit" style="all: unset; cursor: pointer;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Booking Info Modal -->
    <div id="bookingInfoModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h4>Booking Information</h4>
            <div id="bookingDetails">
                <!-- Fetched booking details will be inserted here -->
            </div>
        </div>
    </div>
    <!-- Chat Modal -->
    <div id="chatModal" class="chat-modal">
        <div class="chat-header">
            <h4>Chat Support</h4>
            <span class="chat-close">&times;</span>
        </div>
        <div class="chat-body">
            <p><strong>Chatting with:</strong> <span id="chatBookingName"></span></p>
            <div id="messages" class="chat-messages">

            </div>
            <div class="chat-container">
                <textarea id="chatInput" placeholder="Type a message..."></textarea>
                <button id="sendChatBtn">Send</button>
            </div>

        </div>
    </div>

    <!-- Business Form Modal -->
    <div id="businessFormModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="close-business-modal-btn">&times;</span>
            <h4>Business Owner Registration</h4>
            <form action="submit_destination.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="owner_username">Username:</label>
                        <input type="text" id="owner_username" name="owner_username" placeholder="Enter your username"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="owner_password">Password:</label>
                        <input type="password" id="owner_password" name="owner_password" placeholder="Enter password"
                            required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>
                    </div>
                </div>

                <h4>Business Information</h4>
                <label for="destinationName">Business Name:</label>
                <input type="text" id="destinationName" name="destinationName" placeholder="Enter business name"
                    required>

                <h4>Business Address</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="street">Street Address:</label>
                        <input type="text" id="street" name="street"
                            placeholder="House No. / Building Name & Street Name" required>
                    </div>
                    <div class="form-group">
                        <label for="barangay">Barangay (if applicable):</label>
                        <input type="text" id="barangay" name="barangay" placeholder="Enter barangay (optional)">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City / Municipality:</label>
                        <input type="text" id="city" name="city" placeholder="Enter city or municipality" required>
                    </div>
                    <div class="form-group">
                        <label for="province">Province / State:</label>
                        <input type="text" id="province" name="province" placeholder="Enter province or state" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="zip">ZIP Code:</label>
                        <input type="text" id="zip" name="zipcode" placeholder="Enter ZIP code" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country:</label>
                        <input type="text" id="country" name="country" placeholder="Enter country" required>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description" placeholder="Enter description" required></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="room_type">Category:</label>
                        <select id="room_type" name="room_type" required>
                            <option value="" disabled selected>Select category</option>
                            <option value="attraction">Attraction</option>
                            <option value="accommodation">Accommodation</option>
                            <option value="restaurant">Restaurant</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" id="image" name="image" accept="image/*" required>
                    </div>
                </div>

                <input type="hidden" name="role" value="user">
                <button type="submit" class="submit-btn">Submit for Verification</button>
            </form>
        </div>
    </div>

    <!-- <script src="../../assets/js/add-location.js"></script> -->
    <script src="../../assets/js/message.js"></script>
    <script src="../../assets/js/booking.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById("businessFormModal");
            const openFormBtn = document.getElementById("openForm");
            const closeBtn = document.getElementById("close-business-modal-btn");
            openFormBtn.addEventListener("click", () => modal.style.display = "flex");
            closeBtn.addEventListener("click", () => modal.style.display = "none");
            window.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });
        });
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
        console.log(successMessage)
        if (successMessage) {
            Toast.fire({
                icon: "success",
                title: successMessage,
            });
        }else if(error){
            Toast.fire({
                icon: "error",
                title: error,
            });
        }
    });
    </script>
</body>

</html>
<?php include("../../includes/footer.php"); ?>