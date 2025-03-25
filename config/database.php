<?php
$servername = "localhost"; // or your database server name
$username = "u674941284_tourmatic_admi";        // your MySQL username
$password = "tourmaticSysad69";            // your MySQL password
$dbname = "u674941284_tourism"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
