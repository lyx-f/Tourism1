<?php
session_start();

// Check if logged in
if (!isset($_SESSION['username'])) {
    header('Location: ../../website/login.php');
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
</head>
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
            <p>Details about booking will be shown here...</p>
        </div>
    </div>

    <!-- Business Form Modal -->
    <div id="businessFormModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h4>Business Owner Registration</h4>
            <form action="submit_destination.php" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="owner_username">Username:</label>
                        <input type="text" id="owner_username" name="owner_username" placeholder="Enter your username" required>
                    </div>
                    <div class="form-group">
                        <label for="owner_password">Password:</label>
                        <input type="password" id="owner_password" name="owner_password" placeholder="Enter password" required>
                    </div>
                </div>
                
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
                <input type="text" id="destinationName" name="destinationName" placeholder="Enter business name" required>
                
                <h4>Business Address</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="street">Street Address:</label>
                        <input type="text" id="street" name="street" placeholder="House No. / Building Name & Street Name" required>
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
                        <input type="text" id="zip" name="zip" placeholder="Enter ZIP code" required>
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

    <script src="../../assets/js/add-location.js"></script>
    <script>
           document.addEventListener("DOMContentLoaded", function () {
            const modal = document.getElementById("businessFormModal");
            const openFormBtn = document.getElementById("openForm");
            const closeBtn = document.querySelector(".close-btn");
            openFormBtn.addEventListener("click", () => modal.style.display = "flex");
            closeBtn.addEventListener("click", () => modal.style.display = "none");
            window.addEventListener("click", (e) => { if (e.target === modal) modal.style.display = "none"; });
        });
        document.addEventListener("DOMContentLoaded", function () {
            const bookingModal = document.getElementById("bookingInfoModal");
            const bookingBtn = document.getElementById("bookingInfoBtn");
            const closeBtns = document.querySelectorAll(".close-btn");

            bookingBtn.addEventListener("click", () => bookingModal.style.display = "flex");
            closeBtns.forEach(btn => btn.addEventListener("click", () => bookingModal.style.display = "none"));
            window.addEventListener("click", (e) => { if (e.target === bookingModal) bookingModal.style.display = "none"; });
        });
    </script>
</body>
</html>
<?php include("../../includes/footer.php"); ?>
