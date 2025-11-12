<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'user';  // Your database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Validate form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = trim($_POST['projectName'] ?? '');
    $projectDescription = trim($_POST['projectDescription'] ?? '');
    $projectCategory = trim($_POST['projectCategory'] ?? '');
    $userId = $_SESSION['user_id'];

    // Validation
    if (empty($projectName)) {
        echo json_encode(['success' => false, 'message' => 'Project name is required']);
        exit();
    }

    if (empty($projectDescription)) {
        echo json_encode(['success' => false, 'message' => 'Project description is required']);
        exit();
    }

    if (empty($projectCategory)) {
        echo json_encode(['success' => false, 'message' => 'Project category is required']);
        exit();
    }

    // Handle file upload
    $uploadedFile = null;
    $uploadedFileName = null;
    
    if (isset($_FILES['projectFile']) && $_FILES['projectFile']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['projectFile'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        
        // Get file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed = array('pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'jpeg', 'png');
        
        if (!in_array($fileExt, $allowed)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: PDF, DOC, DOCX, ZIP, RAR, JPG, PNG']);
            exit();
        }
        
        // Check file size (5MB max)
        if ($fileSize > 5242880) {
            echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
            exit();
        }
        
        // Create uploads directory if it doesn't exist
        $uploadDir = 'uploads/projects/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique file name
        $newFileName = uniqid('project_', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;
        
        // Move uploaded file
        if (move_uploaded_file($fileTmpName, $uploadPath)) {
            $uploadedFile = $uploadPath;
            $uploadedFileName = $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            exit();
        }
    }

    // Insert into database
    try {
        $sql = "INSERT INTO projects (user_id, project_name, project_description, project_category, file_path, file_name, created_at) 
                VALUES (:user_id, :project_name, :project_description, :project_category, :file_path, :file_name, NOW())";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':project_name' => $projectName,
            ':project_description' => $projectDescription,
            ':project_category' => $projectCategory,
            ':file_path' => $uploadedFile,
            ':file_name' => $uploadedFileName
        ]);

        echo json_encode(['success' => true, 'message' => 'Project added successfully!']);
    } catch(PDOException $e) {
        // If there's an error and a file was uploaded, delete it
        if ($uploadedFile && file_exists($uploadedFile)) {
            unlink($uploadedFile);
        }
        
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>