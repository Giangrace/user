<?php
$servername = "db.fr-pari1.bengt.wasmernet.com";
$username = "a7b8095073348000656f19d84301";
$password = "0691a7b8-0950-7508-8000-8054effaa657";
$dbname = "db_projects";
$port = 10272;

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// If connection fails, return JSON and stop script
if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'DB Connection failed: '.$conn->connect_error]);
    exit;
}

// Set charset
$conn->set_charset("utf8mb4");
?>
