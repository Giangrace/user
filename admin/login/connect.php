<?php
$host = "db.fr-pari1.bengt.wasmernet.com";
$user = "a7b8095073348000656f19d84301";
$pass = "0691a7b8-0950-7508-8000-8054effaa657";
$db   = "db_projects";
$port = 10272;

$conn = new mysqli($host, $user, $pass, $port);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

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
