<?php
include 'db_conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'No project ID provided']);
        exit;
    }
    
    $id = intval($_POST['id']);
    
    // Get file path before deleting
    $stmt = mysqli_prepare($conn, "SELECT file_path FROM projects WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $filePath = $row['file_path'];
        
        // Delete from database first
        $deleteStmt = mysqli_prepare($conn, "DELETE FROM projects WHERE id = ?");
        mysqli_stmt_bind_param($deleteStmt, "i", $id);
        
        if (mysqli_stmt_execute($deleteStmt)) {
            $fileDeleted = false;
            $message = '';
            
            // Try to delete the physical file if it exists
            if (!empty($filePath)) {
                // The file path is relative, so just use it directly
                if (file_exists($filePath)) {
                    if (unlink($filePath)) {
                        $fileDeleted = true;
                        $message = 'Project and file deleted successfully';
                    } else {
                        $message = 'Project deleted but file could not be removed (permission issue)';
                    }
                } else {
                    // File doesn't exist on disk
                    $message = 'Project deleted (file was not found on server)';
                }
            } else {
                // No file path in database (old projects)
                $message = 'Project deleted (no file was attached)';
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'file_deleted' => $fileDeleted,
                'file_path' => $filePath // For debugging
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to delete project from database: ' . mysqli_error($conn)
            ]);
        }
        
        mysqli_stmt_close($deleteStmt);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Project not found in database'
        ]);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Use POST.'
    ]);
}

mysqli_close($conn);
?>