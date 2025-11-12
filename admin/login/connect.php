<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "login"; // your desired database name

// Step 1: Connect to MySQL server (without selecting a database yet)
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Step 2: Create the database if it doesn’t exist
if (!$conn->select_db($db)) {
    $createDB = "CREATE DATABASE `$db`";
    if ($conn->query($createDB) === TRUE) {
        echo "Database '$db' created successfully!<br>";
        $conn->select_db($db);
    } else {
        die("Error creating database: " . $conn->error);
    }
}

// Step 3: Create the users table if it doesn’t exist
$tableSQL = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($tableSQL) === TRUE) {
    echo "Database connected successfully!";
} else {
    die("Error creating table: " . $conn->error);
}
?>
