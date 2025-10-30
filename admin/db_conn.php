<?php
// ==================== config.php ====================
// Database configuration file
// Save this as: config.php

<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Change to your database username
define('DB_PASS', '');      // Change to your database password
define('DB_NAME', 'portfolio_db');  // Change to your database name

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed: ' . $conn->connect_error
        ]));
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// File upload configuration
define('UPLOAD_DIR', 'uploads/projects/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'jpeg', 'png']);

// Create upload directory if it doesn't exist
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
?>


// ==================== get_projects.php ====================
// Fetch all projects from database
// Save this as: get_projects.php

<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $conn = getDBConnection();
    
    // Fetch all projects ordered by most recent first
    $sql = "SELECT id, project_name, description, category, file_path, created_at 
            FROM projects 
            ORDER BY created_at DESC";
    
    $result = $conn->query($sql);
    
    if ($result === false) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    $projects = [];
    while ($row = $result->fetch_assoc()) {
        $projects[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'projects' => $projects
    ]);
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


// ==================== add_project.php ====================
// Add new project to database
// Save this as: add_project.php

<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Check if required fields are present
    if (empty($_POST['projectName']) || empty($_POST['projectDescription']) || empty($_POST['projectCategory'])) {
        throw new Exception('All required fields must be filled!');
    }
    
    $projectName = trim($_POST['projectName']);
    $projectDescription = trim($_POST['projectDescription']);
    $projectCategory = trim($_POST['projectCategory']);
    $filePath = null;
    
    // Handle file upload if present
    if (isset($_FILES['projectFile']) && $_FILES['projectFile']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['projectFile'];
        
        // Validate file size
        if ($file['size'] > MAX_FILE_SIZE) {
            throw new Exception('File size must be less than 5MB!');
        }
        
        // Get file extension
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        // Validate file extension
        if (!in_array($fileExtension, ALLOWED_EXTENSIONS)) {
            throw new Exception('Invalid file type. Allowed: ' . implode(', ', ALLOWED_EXTENSIONS));
        }
        
        // Generate unique filename
        $uniqueFileName = uniqid() . '_' . time() . '.' . $fileExtension;
        $uploadPath = UPLOAD_DIR . $uniqueFileName;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
            throw new Exception('Failed to upload file!');
        }
        
        $filePath = $uploadPath;
    }
    
    // Insert into database
    $conn = getDBConnection();
    
    $stmt = $conn->prepare("INSERT INTO projects (project_name, description, category, file_path, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $projectName, $projectDescription, $projectCategory, $filePath);
    
    if (!$stmt->execute()) {
        // Delete uploaded file if database insert fails
        if ($filePath && file_exists($filePath)) {
            unlink($filePath);
        }
        throw new Exception('Failed to save project: ' . $stmt->error);
    }
    
    $projectId = $stmt->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'Project added successfully!',
        'project_id' => $projectId
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


// ==================== delete_project.php ====================
// Delete project from database
// Save this as: delete_project.php

<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    // Check if ID is provided
    if (empty($_POST['id']) || !is_numeric($_POST['id'])) {
        throw new Exception('Invalid project ID!');
    }
    
    $projectId = intval($_POST['id']);
    
    $conn = getDBConnection();
    
    // First, get the file path to delete the file
    $stmt = $conn->prepare("SELECT file_path FROM projects WHERE id = ?");
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Project not found!');
    }
    
    $project = $result->fetch_assoc();
    $filePath = $project['file_path'];
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $projectId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to delete project: ' . $stmt->error);
    }
    
    // Delete file if exists
    if ($filePath && file_exists($filePath)) {
        unlink($filePath);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Project deleted successfully!'
    ]);
    
    $stmt->close();
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>


// ==================== database.sql ====================
// SQL to create the projects table
// Run this in your MySQL/phpMyAdmin

CREATE DATABASE IF NOT EXISTS portfolio_db;
USE portfolio_db;

CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_name VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    file_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_created_at (created_at),
    INDEX idx_category (category)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;