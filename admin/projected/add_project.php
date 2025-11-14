<?php
// Prevent any HTML output before JSON
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output

session_start();

// Clear any output buffer that might have been started
ob_clean();

// Set JSON header
header('Content-Type: application/json');

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('User not logged in. Please login first.');
    }

    // Include database connection
    if (!file_exists('db_conn.php')) {
        throw new Exception('Database connection file not found');
    }
    
    include 'db_conn.php';
    
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    $user_id = $_SESSION['user_id'];

    // Validate POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get form data
    $project_name = isset($_POST['project_name']) ? trim($_POST['project_name']) : '';
    $project_description = isset($_POST['project_description']) ? trim($_POST['project_description']) : '';
    $project_category = isset($_POST['project_category']) ? trim($_POST['project_category']) : '';

    // Validate inputs
    if (empty($project_name)) {
        throw new Exception('Project name is required');
    }
    if (empty($project_description)) {
        throw new Exception('Project description is required');
    }
    if (empty($project_category)) {
        throw new Exception('Project category is required');
    }

    // Check if file was uploaded
    if (!isset($_FILES['project_file'])) {
        throw new Exception('No file uploaded');
    }

    $file_error = $_FILES['project_file']['error'];
    if ($file_error !== UPLOAD_ERR_OK) {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File too large (exceeds server limit)',
            UPLOAD_ERR_FORM_SIZE => 'File too large (exceeds form limit)',
            UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file selected',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped'
        ];
        throw new Exception($errors[$file_error] ?? 'Unknown upload error');
    }

    // Create uploads directory
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new Exception('Cannot create uploads folder. Check permissions.');
        }
    }

    // Get file info
    $original_filename = basename($_FILES['project_file']['name']);
    $file_tmp = $_FILES['project_file']['tmp_name'];
    $file_size = $_FILES['project_file']['size'];
    $file_ext = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));

    // Validate file size (10MB)
    if ($file_size > 10 * 1024 * 1024) {
        throw new Exception('File size exceeds 10MB limit');
    }

    // Validate file type
    $allowed = ['pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'jpeg', 'png'];
    if (!in_array($file_ext, $allowed)) {
        throw new Exception('Invalid file type. Allowed: PDF, DOC, DOCX, ZIP, RAR, JPG, PNG');
    }

    // Generate unique filename
    $new_filename = time() . '_' . uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $new_filename;

    // Move file
    if (!move_uploaded_file($file_tmp, $file_path)) {
        throw new Exception('Failed to save file. Check folder permissions.');
    }

    // Prepare SQL statement
    $sql = "INSERT INTO projects (user_id, project_name, project_description, project_category, file_name, file_path, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        // Delete uploaded file if query preparation fails
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        throw new Exception('Database error: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param("isssss", $user_id, $project_name, $project_description, $project_category, $original_filename, $file_path);

    // Execute
    if (!$stmt->execute()) {
        // Delete uploaded file if insertion fails
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        throw new Exception('Failed to save project: ' . $stmt->error);
    }

    $stmt->close();
    $conn->close();

    // Success
    echo json_encode([
        'success' => true,
        'message' => '✓ Project added successfully! Redirecting...'
    ]);

} catch (Exception $e) {
    // Error
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// End output buffering
ob_end_flush();
?>