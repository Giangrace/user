<?php
include 'db_conn.php';

// Set JSON header
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Validate required fields
    if (!isset($_POST['user_id']) || !isset($_POST['project_name']) || 
        !isset($_POST['project_description']) || !isset($_POST['project_category'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Missing required fields'
        ]);
        exit;
    }
    
    $user_id = intval($_POST['user_id']);
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $project_description = mysqli_real_escape_string($conn, $_POST['project_description']);
    $project_category = mysqli_real_escape_string($conn, $_POST['project_category']);
    
    // Handle file upload
    $file_path = null;
    $file_name = null;
    
    if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === UPLOAD_ERR_OK) {
        
        // Define upload directory
        $upload_dir = __DIR__ . '/uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to create uploads directory'
                ]);
                exit;
            }
        }
        
        // Check if directory is writable
        if (!is_writable($upload_dir)) {
            echo json_encode([
                'success' => false, 
                'message' => 'Uploads directory is not writable'
            ]);
            exit;
        }
        
        // Get file information
        $file_name = $_FILES['project_file']['name'];
        $file_tmp = $_FILES['project_file']['tmp_name'];
        $file_size = $_FILES['project_file']['size'];
        $file_error = $_FILES['project_file']['error'];
        
        // Get file extension
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed file extensions
        $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar'];
        
        // Validate file extension
        if (!in_array($file_ext, $allowed_extensions)) {
            echo json_encode([
                'success' => false, 
                'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowed_extensions)
            ]);
            exit;
        }
        
        // Validate file size (max 10MB)
        $max_size = 10 * 1024 * 1024; // 10MB in bytes
        if ($file_size > $max_size) {
            echo json_encode([
                'success' => false, 
                'message' => 'File size exceeds 10MB limit'
            ]);
            exit;
        }
        
        // Generate unique filename to avoid conflicts
        $unique_name = time() . '_' . uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file_name);
        $full_path = $upload_dir . $unique_name;
        
        // Relative path for database storage
        $file_path = 'uploads/' . $unique_name;
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $full_path)) {
            // File uploaded successfully - Insert into database
            
            // Use prepared statement for security
            $stmt = mysqli_prepare($conn, 
                "INSERT INTO projects (user_id, project_name, project_description, project_category, file_path, file_name, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, NOW())"
            );
            
            mysqli_stmt_bind_param($stmt, "isssss", 
                $user_id, 
                $project_name, 
                $project_description, 
                $project_category, 
                $file_path, 
                $file_name
            );
            
            if (mysqli_stmt_execute($stmt)) {
                $project_id = mysqli_insert_id($conn);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Project added successfully',
                    'project_id' => $project_id,
                    'file_name' => $file_name,
                    'file_path' => $file_path
                ]);
            } else {
                // Database error - delete the uploaded file
                if (file_exists($full_path)) {
                    unlink($full_path);
                }
                
                echo json_encode([
                    'success' => false, 
                    'message' => 'Database error: ' . mysqli_error($conn)
                ]);
            }
            
            mysqli_stmt_close($stmt);
            
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to move uploaded file'
            ]);
        }
        
    } else {
        // Handle file upload errors
        $error_message = 'No file uploaded';
        
        if (isset($_FILES['project_file']['error'])) {
            switch ($_FILES['project_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error_message = 'File size exceeds limit';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error_message = 'File was only partially uploaded';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $error_message = 'No file was uploaded';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error_message = 'Missing temporary folder';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error_message = 'Failed to write file to disk';
                    break;
                default:
                    $error_message = 'Unknown upload error';
            }
        }
        
        echo json_encode([
            'success' => false, 
            'message' => $error_message
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method. Use POST.'
    ]);
}

mysqli_close($conn);
?>