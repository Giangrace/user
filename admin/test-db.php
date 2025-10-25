<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php';

echo "✅ Connected to database!<br>";

// Check if table exists
$result = $conn->query("SHOW TABLES LIKE 'user'");
if ($result->num_rows > 0) {
    echo "✅ Table 'user' exists!<br>";
} else {
    echo "❌ Table 'user' does NOT exist!<br>";
}

// Check table structure
$result = $conn->query("DESCRIBE user");
echo "<h3>Table Structure:</h3>";
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "<br>";
}
?>