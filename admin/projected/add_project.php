<?php
include 'db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $project_name = mysqli_real_escape_string($conn, $_POST['project_name']);
    $project_description = mysqli_real_escape_string($conn, $_POST['project_description']);
    $project_category = mysqli_real_escape_string($conn, $_POST['project_category']);
    
    // Handle file upload
    $file_path = null;
    $file_name = null;
    
    if (isset($_FILES['project_file']) && $_FILES['project_file']['error'] === 0) {
        $upload_dir = 'uploads/'; // Create this folder if it doesn't exist
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_name = $_FILES['project_file']['name'];
        $file_tmp = $_FILES['project_file']['tmp_name'];
        
        // Generate unique filename to avoid conflicts
        $unique_name = time() . '_' . $file_name;
        $file_path = $upload_dir . $unique_name;
        
        // Move uploaded file
        if (move_uploaded_file($file_tmp, $file_path)) {
            // File uploaded successfully
            
            // Insert into database WITH file_path and file_name
            $sql = "INSERT INTO projects (user_id, project_name, project_description, project_category, file_path, file_name) 
                    VALUES ('$user_id', '$project_name', '$project_description', '$project_category', '$file_path', '$file_name')";
            
            if (mysqli_query($conn, $sql)) {
                echo json_encode(['success' => true, 'message' => 'Project added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    }
}

mysqli_close($conn);
?>