<?php
// This script will fix your database structure

$host = 'localhost';
$username = 'root';
$password = '';

// IMPORTANT: Change this to your correct database name
// Either 'user' or 'portfolio_db'
$dbname = 'portfolio_db';  // Change this if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h1>Database Fixer</h1>";
    echo "<p>Connected to database: <strong>$dbname</strong></p>";
    echo "<hr>";
    
    // Check current structure
    echo "<h2>Current Table Structure:</h2>";
    $stmt = $pdo->query("DESCRIBE projects");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<ul>";
    foreach ($columns as $col) {
        echo "<li><strong>{$col['Field']}</strong> - {$col['Type']}</li>";
    }
    echo "</ul>";
    
    // Check if user_id exists
    $hasUserId = false;
    $hasFileName = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] == 'user_id') $hasUserId = true;
        if ($col['Field'] == 'file_name') $hasFileName = true;
    }
    
    echo "<hr><h2>Applying Fixes:</h2><ul>";
    
    // Add user_id column if missing
    if (!$hasUserId) {
        try {
            $pdo->exec("ALTER TABLE projects ADD COLUMN user_id INT DEFAULT 1 AFTER id");
            echo "<li style='color: green;'>✅ Added user_id column</li>";
        } catch (Exception $e) {
            echo "<li style='color: red;'>❌ Error adding user_id: " . $e->getMessage() . "</li>";
        }
    } else {
        echo "<li style='color: blue;'>ℹ️ user_id column already exists</li>";
    }
    
    // Add file_name column if missing
    if (!$hasFileName) {
        try {
            $pdo->exec("ALTER TABLE projects ADD COLUMN file_name VARCHAR(255) AFTER file_path");
            echo "<li style='color: green;'>✅ Added file_name column</li>";
        } catch (Exception $e) {
            echo "<li style='color: red;'>❌ Error adding file_name: " . $e->getMessage() . "</li>";
        }
    } else {
        echo "<li style='color: blue;'>ℹ️ file_name column already exists</li>";
    }
    
    // Rename columns if needed
    $hasProjectDescription = false;
    $hasProjectCategory = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] == 'project_description') $hasProjectDescription = true;
        if ($col['Field'] == 'project_category') $hasProjectCategory = true;
    }
    
    // If we have 'description' but not 'project_description', rename it
    if (!$hasProjectDescription) {
        try {
            $pdo->exec("ALTER TABLE projects CHANGE description project_description TEXT");
            echo "<li style='color: green;'>✅ Renamed description to project_description</li>";
        } catch (Exception $e) {
            // Column might already be correct
        }
    }
    
    // If we have 'category' but not 'project_category', rename it
    if (!$hasProjectCategory) {
        try {
            $pdo->exec("ALTER TABLE projects CHANGE category project_category VARCHAR(100)");
            echo "<li style='color: green;'>✅ Renamed category to project_category</li>";
        } catch (Exception $e) {
            // Column might already be correct
        }
    }
    
    echo "</ul>";
    
    echo "<hr><h2>Final Table Structure:</h2>";
    $stmt = $pdo->query("DESCRIBE projects");
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr style='background: #007bff; color: white;'><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td><strong>{$row['Field']}</strong></td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h2 style='color: green;'>✅ Database structure updated!</h2>";
    echo "<p><a href='view_projects.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Projects</a></p>";
    
} catch(PDOException $e) {
    echo "<h2 style='color: red;'>Error: " . $e->getMessage() . "</h2>";
}
?>

<style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
    h1 { color: #333; }
    table { background: white; margin: 20px 0; }
    ul { background: white; padding: 20px 40px; border-radius: 8px; }
    li { margin: 10px 0; }
</style>