<?php
include 'db_conn.php';

echo "<h1>Database Diagnostic Tool</h1>";
echo "<hr>";

// Test connection
if ($conn) {
    echo "<h2 style='color: green;'>✅ Database Connected Successfully</h2>";
} else {
    echo "<h2 style='color: red;'>❌ Database Connection Failed</h2>";
    echo "Error: " . mysqli_connect_error();
    exit;
}

// Show table structure
echo "<h2>Projects Table Structure:</h2>";
$result = mysqli_query($conn, "DESCRIBE projects");
if ($result) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #333; color: white;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td><strong>{$row['Field']}</strong></td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>Error getting table structure: " . mysqli_error($conn) . "</p>";
}

echo "<br><br>";

// Count total projects
echo "<h2>Total Projects Count:</h2>";
$result = mysqli_query($conn, "SELECT COUNT(*) as total FROM projects");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "<p style='font-size: 24px; color: blue;'><strong>" . $row['total'] . " projects</strong> in database</p>";
}

echo "<br>";

// Show all projects
echo "<h2>All Projects Data:</h2>";
$sql = "SELECT * FROM projects ORDER BY id DESC LIMIT 10";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    
    // Get column names
    $firstRow = mysqli_fetch_assoc($result);
    echo "<tr style='background: #333; color: white;'>";
    foreach ($firstRow as $key => $value) {
        echo "<th>$key</th>";
    }
    echo "</tr>";
    
    // Reset pointer and display all rows
    mysqli_data_seek($result, 0);
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>No projects found in database</p>";
}

echo "<br><br>";

// Test delete query
echo "<h2>Test Delete Query (Simulation - Not Actual Delete):</h2>";
$testId = 1; // Change this to an actual ID from your database
$testSql = "SELECT * FROM projects WHERE id = '$testId'";
$result = mysqli_query($conn, $testSql);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<p style='color: green;'>✅ Project with ID $testId exists and can be deleted</p>";
    $row = mysqli_fetch_assoc($result);
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "<p style='color: orange;'>Project with ID $testId not found</p>";
}

mysqli_close($conn);
?>

<style>
    body {
        font-family: Arial, sans-serif;
        padding: 20px;
        background: #f5f5f5;
    }
    table {
        background: white;
        margin: 10px 0;
    }
    th {
        background: #333;
        color: white;
        padding: 10px;
    }
    td {
        padding: 8px;
    }
    tr:nth-child(even) {
        background: #f9f9f9;
    }
    h1 {
        color: #333;
    }
    h2 {
        color: #666;
        border-bottom: 2px solid #ddd;
        padding-bottom: 10px;
        margin-top: 30px;
    }
</style>