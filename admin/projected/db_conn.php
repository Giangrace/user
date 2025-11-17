<?php
$servername = "db.fr-pari1.bengt.wasmernet.com";
$username = "a7b8095073348000656f19d84301";
$password = "0691a7b8-0950-7508-8000-8054effaa657";
$dbname = "db_projects";
$port = 10272;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to prevent encoding issues
$conn->set_charset("utf8mb4");
?>