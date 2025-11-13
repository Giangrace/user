<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete System Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 0; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        th { background: #007bff; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background: #f9f9f9; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .info-box { background: #e7f3ff; border-left: 4px solid #007bff; padding: 15px; margin: 15px 0; }
    </style>
</head>
<body>
    <h1>🔍 Complete System Diagnostic Tool</h1>

    <!-- SESSION CHECK -->
    <div class="section">
        <h2>1. Session Information</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <p class="success">✅ User is logged in</p>
            <table>
                <tr><th>Session Variable</th><th>Value</th></tr>
                <tr><td>User ID</td><td><?php echo $_SESSION['user_id']; ?></td></tr>
                <tr><td>First Name</td><td><?php echo $_SESSION['first_name'] ?? 'Not set'; ?></td></tr>
                <tr><td>Last Name</td><td><?php echo $_SESSION['last_name'] ?? 'Not set'; ?></td></tr>
            </table>
        <?php else: ?>
            <p class="error">❌ No user logged in</p>
        <?php endif; ?>
    </div>

    <!-- DATABASE CONNECTION -->
    <div class="section">
        <h2>2. Database Connection</h2>
        <?php
        $host = 'localhost';
        $dbname = 'user';
        $username = 'root';
        $password = '';

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "<p class='success'>✅ Database connected successfully</p>";
            
            // TABLE STRUCTURE
            echo "<h3>Database Table Structure:</h3>";
            $stmt = $pdo->query("DESCRIBE projects");
            echo "<table><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td><strong>{$row['Field']}</strong></td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            // PROJECT COUNT
            $stmt = $pdo->query("SELECT COUNT(*) as total FROM projects");
            $count = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p><strong>Total projects in database:</strong> {$count['total']}</p>";
            
            // ALL PROJECTS DATA
            echo "<h3>All Projects (First 5):</h3>";
            $stmt = $pdo->query("SELECT * FROM projects ORDER BY id DESC LIMIT 5");
            $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (count($projects) > 0) {
                echo "<table><tr>";
                foreach (array_keys($projects[0]) as $column) {
                    echo "<th>$column</th>";
                }
                echo "</tr>";
                
                foreach ($projects as $project) {
                    echo "<tr>";
                    foreach ($project as $value) {
                        echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p class='warning'>⚠️ No projects found in database</p>";
            }
            
        } catch(PDOException $e) {
            echo "<p class='error'>❌ Database error: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>

    <!-- FILE SYSTEM CHECK -->
    <div class="section">
        <h2>3. File System Check</h2>
        <?php
        echo "<p><strong>Current directory:</strong> " . __DIR__ . "</p>";
        echo "<p><strong>Document root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
        
        // Check for uploads directory
        $uploadsPath = __DIR__ . '/uploads';
        echo "<h3>Uploads Directory Check:</h3>";
        
        if (is_dir($uploadsPath)) {
            echo "<p class='success'>✅ Uploads directory exists: $uploadsPath</p>";
            
            // List files in uploads
            $files = scandir($uploadsPath);
            $fileCount = count($files) - 2; // Exclude . and ..
            
            echo "<p><strong>Files in uploads directory:</strong> $fileCount files</p>";
            
            if ($fileCount > 0) {
                echo "<table><tr><th>Filename</th><th>Size</th><th>Modified</th></tr>";
                foreach ($files as $file) {
                    if ($file != '.' && $file != '..') {
                        $filePath = $uploadsPath . '/' . $file;
                        $size = filesize($filePath);
                        $modified = date("Y-m-d H:i:s", filemtime($filePath));
                        echo "<tr><td>$file</td><td>" . number_format($size) . " bytes</td><td>$modified</td></tr>";
                    }
                }
                echo "</table>";
            }
        } else {
            echo "<p class='error'>❌ Uploads directory NOT found at: $uploadsPath</p>";
            echo "<p class='warning'>You need to create an 'uploads' folder!</p>";
        }
        ?>
    </div>

    <!-- FILE PATH ANALYSIS -->
    <div class="section">
        <h2>4. File Path Analysis</h2>
        <?php
        if (isset($pdo) && isset($projects) && count($projects) > 0) {
            echo "<p>Checking if database file paths actually exist...</p>";
            echo "<table><tr><th>Project ID</th><th>Project Name</th><th>Database Path</th><th>File Exists?</th><th>Actual Location</th></tr>";
            
            foreach ($projects as $project) {
                $dbPath = $project['file_path'] ?? '';
                $exists = false;
                $actualPath = 'Not found';
                
                if (!empty($dbPath)) {
                    // Try multiple possible paths
                    $possiblePaths = [
                        $dbPath,
                        __DIR__ . '/' . $dbPath,
                        $_SERVER['DOCUMENT_ROOT'] . '/' . $dbPath,
                        __DIR__ . '/uploads/' . basename($dbPath)
                    ];
                    
                    foreach ($possiblePaths as $path) {
                        if (file_exists($path)) {
                            $exists = true;
                            $actualPath = $path;
                            break;
                        }
                    }
                }
                
                echo "<tr>";
                echo "<td>{$project['id']}</td>";
                echo "<td>" . htmlspecialchars($project['project_name']) . "</td>";
                echo "<td>" . htmlspecialchars($dbPath) . "</td>";
                echo "<td>" . ($exists ? "<span class='success'>✅ YES</span>" : "<span class='error'>❌ NO</span>") . "</td>";
                echo "<td>" . htmlspecialchars($actualPath) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        ?>
    </div>

    <!-- RECOMMENDATIONS -->
    <div class="section">
        <h2>5. Recommendations</h2>
        <div class="info-box">
            <h3>📋 Next Steps:</h3>
            <ol>
                <li>If files exist but paths are wrong, you need to update the database paths</li>
                <li>If files don't exist, you need to re-upload them</li>
                <li>If uploads directory doesn't exist, create it with proper permissions (755)</li>
                <li>Make sure file paths in database match actual file locations</li>
            </ol>
        </div>
    </div>

    <div class="section">
        <h2>6. Quick Fix SQL (Copy and Run if Needed)</h2>
        <p>If your files are in the uploads folder but database paths are wrong:</p>
        <pre>UPDATE projects SET file_path = CONCAT('uploads/', file_name) WHERE file_path NOT LIKE 'uploads/%';</pre>
        
        <p>Or if you need to add 'projected/' prefix:</p>
        <pre>UPDATE projects SET file_path = CONCAT('projected/uploads/', file_name) WHERE file_path NOT LIKE '%uploads/%';</pre>
    </div>

</body>
</html>