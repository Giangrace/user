<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'users'; // make sure this exists in phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    // Optional: Uncomment this line to confirm connection
    // echo "✅ Connected successfully to database: $db";
}
?>
