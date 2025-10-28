<?php
// Test database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'portfolio_db';

$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    echo "✅ Database connection successful!<br>";
    echo "Connected to database: " . $database;
    
    // Check if table exists
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'projects'");
    if (mysqli_num_rows($result) > 0) {
        echo "<br>✅ Projects table exists!";
    } else {
        echo "<br>❌ Projects table NOT found! Please create it.";
    }
} else {
    echo "❌ Connection failed: " . mysqli_connect_error();
}

mysqli_close($conn);
?>