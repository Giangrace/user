<?php
$host = 'localhost';
$username = 'root';
$password = ''; // Leave empty if you haven't set a password
$database = 'user_db'; // Your database name

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>