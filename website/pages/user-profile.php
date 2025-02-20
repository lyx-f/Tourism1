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
            <h2><?=  $_SESSION['username'] ?></h2>
            <button class="toggle-btn">+</button>
        </div>
        <div class="icon-row">

        <div class="icon-item">
        <form action="../pages/history.php" method="GET" style="display: inline;">
            <button type="submit" style="all: unset; cursor: pointer;">
                <i class="fas fa-history"></i>
                <span>History</span>
            </button>
        </form>
    </div>

    <div class="separator"></div>


    <div class="separator"></div>

    <div class="icon-item">
        <form action="../pages/logout.php" method="POST" style="display: inline;">
            <button type="submit" style="all: unset; cursor: pointer;">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</div>




        <div class="form-container" id="addForm">
        <form action="submit_destination.php" method="POST" enctype="multipart/form-data">

    <h4>Business Owner Registration</h4>

    <label for="owner_username">Username:</label>
    <input type="text" id="owner_username" name="owner_username" placeholder="Enter your username" required>

    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="Enter your email address" required>

    <label for="owner_password">Password:</label>
    <input type="password" id="owner_password" name="owner_password" placeholder="Enter password" required>

    <label for="phone">Phone:</label>
    <input type="tel" id="phone" name="phone" placeholder="Enter phone number" required>


    <h4>Business Information</h4>

    <label for="destinationName">Destination Name:</label>
    <input type="text" id="destinationName" name="destinationName" placeholder="Enter destination name" required>

    <label for="location">Location:</label>
    <input type="text" id="location" name="location" placeholder="Enter location" required>

    <label for="description">Description:</label>
    <textarea id="description" name="description" placeholder="Enter description" required></textarea>

    <label for="price">Price per Night:</label>
    <input type="number" id="price" name="price" placeholder="Enter price per night" required>

    <label for="image">Image:</label>
    <input type="file" id="image" name="image" accept="image/*" required>

    <!-- Hidden Field to Capture Business Owner Role -->
    <input type="hidden" name="role" value="user"> <!-- Default role for regular user -->
    <input type="hidden" name="business_id" value="<?php echo $business_id; ?>">

    <button type="submit" class="submit-btn">Submit for Verification</button>
</form>

    </div>
  </div>
  </div>
</body>
</html>

    <?php include("../../includes/footer.php"); ?>
    <script src="../../assets/js/add-location.js"></script>
</body>
</html>
