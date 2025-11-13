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
        
        // Delete from database
        $deleteStmt = mysqli_prepare($conn, "DELETE FROM projects WHERE id = ?");
        mysqli_stmt_bind_param($deleteStmt, "i", $id);
        
        if (mysqli_stmt_execute($deleteStmt)) {
            $fileDeleted = false;
            
            // Try to delete the physical file
            if (!empty($filePath) && file_exists($filePath)) {
                if (unlink($filePath)) {
                    $fileDeleted = true;
                    $message = 'Project and file deleted successfully';
                } else {
                    $message = 'Project deleted but file could not be removed';
                }
            } else {
                $message = 'Project deleted (no file was attached)';
            }
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'file_deleted' => $fileDeleted
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete project']);
        }
        
        mysqli_stmt_close($deleteStmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Project not found']);
    }
    
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

mysqli_close($conn);
?>