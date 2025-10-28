<?php
// Database configuration
$host = 'localhost';
$username = 'root';
$password = ''; // Change this if you have a password
$database = 'portfolio_db';

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8mb4 for better compatibility
mysqli_set_charset($conn, "utf8mb4");
?>