<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

if (!isset($_GET['id'])) {
    die('Invalid request');
}

$projectId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

// Database configuration
$host = 'localhost';
$dbname = 'user';  // Your database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get project file info (only if it belongs to this user)
    $stmt = $pdo->prepare("SELECT file_path, file_name FROM projects WHERE id = :id AND user_id = :user_id");
    $stmt->execute([':id' => $projectId, ':user_id' => $userId]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project || !$project['file_path']) {
        die('File not found');
    }
    
    $filePath = $project['file_path'];
    $fileName = $project['file_name'];
    
    // Check if file exists
    if (!file_exists($filePath)) {
        die('File not found on server');
    }
    
    // Get file extension and set content type
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $contentTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png'
    ];
    
    $contentType = $contentTypes[$fileExt] ?? 'application/octet-stream';
    
    // Set headers for download
    header('Content-Description: File Transfer');
    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . basename($fileName) . '"');
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Clear output buffer
    ob_clean();
    flush();
    
    // Read and output file
    readfile($filePath);
    exit();
    
} catch(PDOException $e) {
    die('Database error occurred');
}
?>