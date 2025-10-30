<!-- ==================== test.php ==================== -->
<!-- Save this as: test.php in the same folder as project.html -->
<!-- Then open: http://localhost/user/htmlpromax/test.php -->

<?php
// Prevent any output before JSON
ob_start();

// Set JSON header
header('Content-Type: application/json');

// Test response
echo json_encode([
    'success' => true,
    'message' => 'PHP is working correctly!',
    'php_version' => phpversion(),
    'test_time' => date('Y-m-d H:i:s')
]);

// Clear any buffered output
ob_end_flush();
?>


<!-- ==================== UPDATED get_projects.php ==================== -->
<!-- Replace your current get_projects.php with this version -->

<?php
// Prevent any whitespace or output before JSON
ob_start();

// Set JSON header first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Check if config file exists
if (!file_exists('config.php')) {
    echo json_encode([
        'success' => false,
        'message' => 'Config file not found. Please create config.php'
    ]);
    exit;
}

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
        'projects' => $projects,
        'count' => count($projects)
    ]);
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Clear buffer and send output
ob_end_flush();
?>


<!-- ==================== UPDATED add_project.php ==================== -->
<!-- Replace your current add_project.php with this version -->

<?php
// Prevent any whitespace or output before JSON
ob_start();

// Set JSON header first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Check if config file exists
if (!file_exists('config.php')) {
    echo json_encode([
        'success' => false,
        'message' => 'Config file not found. Please create config.php'
    ]);
    exit;
}

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

// Clear buffer and send output
ob_end_flush();
?>


<!-- ==================== UPDATED delete_project.php ==================== -->
<!-- Replace your current delete_project.php with this version -->

<?php
// Prevent any whitespace or output before JSON
ob_start();

// Set JSON header first
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Check if config file exists
if (!file_exists('config.php')) {
    echo json_encode([
        'success' => false,
        'message' => 'Config file not found. Please create config.php'
    ]);
    exit;
}

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

// Clear buffer and send output
ob_end_flush();
?>