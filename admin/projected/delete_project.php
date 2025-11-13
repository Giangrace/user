<?php
include 'db_conn.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    
    // Get file path before deleting
    $sql = "SELECT file_path FROM projects WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $filePath = $row['file_path'];
        
        // Delete from database
        $deleteSql = "DELETE FROM projects WHERE id = '$id'";
        
        if (mysqli_query($conn, $deleteSql)) {
            // Try to delete the physical file
            $fullPath = __DIR__ . '/' . $filePath;
            
            if (file_exists($fullPath)) {
                if (unlink($fullPath)) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Project and file deleted successfully'
                    ]);
                } else {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Project deleted but file removal failed',
                        'warning' => 'File could not be deleted from server'
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Project deleted (file not found on server)'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false, 
                'message' => 'Database deletion failed: ' . mysqli_error($conn)
            ]);
        }
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Project not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method'
    ]);
}

mysqli_close($conn);
?>