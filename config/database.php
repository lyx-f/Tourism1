<?php
$servername = "localhost"; // or your database server name
$username = "root";        // your MySQL username
$password = "";            // your MySQL password
$dbname = "tourism"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
