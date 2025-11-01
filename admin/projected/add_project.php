<?php
// Turn off error display to prevent HTML in JSON response
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $projectName = mysqli_real_escape_string($conn, $_POST['projectName']);
    $projectDescription = mysqli_real_escape_string($conn, $_POST['projectDescription']);
    $projectCategory = mysqli_real_escape_string($conn, $_POST['projectCategory']);
    $filePath = null;

    // Validate required fields
    if (empty($projectName) || empty($projectDescription) || empty($projectCategory)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required!']);
        exit;
    }

    // Handle file upload
    if (isset($_FILES['projectFile']) && $_FILES['projectFile']['error'] === 0) {
        $file = $_FILES['projectFile'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];

        // Get file extension
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed = array('pdf', 'doc', 'docx', 'zip', 'rar', 'jpg', 'jpeg', 'png');

        if (in_array($fileExt, $allowed)) {
            // Check file size (5MB max)
            if ($fileSize <= 5242880) {
                // Create uploads directory if it doesn't exist
                $uploadDir = 'uploads/';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate unique file name
                $newFileName = uniqid('project_', true) . '.' . $fileExt;
                $fileDestination = $uploadDir . $newFileName;

                // Move uploaded file
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $filePath = $fileDestination;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to upload file!']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB!']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type!']);
            exit;
        }
    }

    // Insert into database
    $sql = "INSERT INTO projects (project_name, description, category, file_path, created_at) 
            VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $projectName, $projectDescription, $projectCategory, $filePath);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Project added successfully!']);
    } else {
        // If database insert fails, delete uploaded file
        if ($filePath && file_exists($filePath)) {
            unlink($filePath);
        }
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method!']);
}

mysqli_close($conn);
?>