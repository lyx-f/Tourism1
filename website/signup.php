<?php include('../includes/login_navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
</head>
<body>
<div class="container">
    <div class="slogan">
        <h1>
            <span class="bold-text">Welcome to</span>
            <span class="outlined-text">Tourmatic</span>
        </h1>
        <p>Your Matiful Compass</p>
    </div>
    <div class="login-form">
        <div class="text">SIGNUP</div>

        <form action="../controller/signup.controller.php" method="POST">
            <div class="field">
                <div class="fas fa-user"></div>
                <input type="text" name="username" placeholder="Enter Username" required>
            </div>

            <div class="field">
                <div class="fas fa-envelope"></div>
                <input type="email" name="email" placeholder="Enter Email" required>
            </div>

            <div class="field">
                <div class="fas fa-lock"></div>
                <input type="password" name="password" placeholder="Enter Password" required>
            </div>

            <div class="field">
                <div class="fas fa-lock"></div>
                <input type="password" name="confirmpass" placeholder="Confirm Password" required>
            </div>

            <div class="field">
                <div class="fas fa-key"></div>
                <input type="password" name="admin_code" placeholder="Enter Admin Code (Optional/if any)">
            </div>

            <button type="submit" id="register">REGISTER</button>

            <div>
                Already have an account? <a href="login.php">Login Here</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
