<?php
session_start();
if (isset($_SESSION['error'])) {
    echo "<p style='color: black; text-align: center; font-weight: bold;'>" . htmlspecialchars($_SESSION['error']) . "</p>";
    unset($_SESSION['error']);
}
include('../includes/login_navbar.php');
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
            <div class="text">
                LOGIN
            </div>
            <form action="../controller/login.controller.php" method="POST">
            <div class="field">
               <div class="fas fa-user"></div>
               <input type="text" name="username" placeholder="Enter Username">
             </div>

                <div class="field">
                    <div class="fas fa-lock"></div>
                    <input type="password" name="password" placeholder="Password">
                </div>
                <button type="submit">LOGIN</button>
             </form>  
            <div class="link">
                Not a member yet? <a href="signup.php">Signup Now</a>
            </div>
        </div>
    </div>


</body>
</html>

