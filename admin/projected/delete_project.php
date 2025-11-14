<?php
session_start();
include 'db_conn.php';

header('Content-Type: application/json');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get project_id - check both POST and JSON input
$project_id = 0;

// Try getting from regular POST first
if (isset($_POST['project_id'])) {
    $project_id = intval($_POST['project_id']);
}

// If not in POST, try JSON body
if ($project_id === 0) {
    $json_data = json_decode(file_get_contents('php://input'), true);
    if (isset($json_data['project_id'])) {
        $project_id = intval($json_data['project_id']);
    }
}

// Validate project_id
if ($project_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'No valid project ID provided']);
    exit;
}

try {
    // First, get the file path so we can delete the file
    $stmt = $conn->prepare("SELECT file_path FROM projects WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $project_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Project not found or you do not have permission to delete it']);
        $stmt->close();
        exit;
    }

    $row = $result->fetch_assoc();
    $file_path = $row['file_path'];
    $stmt->close();

    // Delete the project from database
    $delete_stmt = $conn->prepare("DELETE FROM projects WHERE id = ? AND user_id = ?");
    $delete_stmt->bind_param("ii", $project_id, $user_id);
    
    if ($delete_stmt->execute()) {
        // Delete the physical file if it exists
        if (!empty($file_path) && file_exists($file_path)) {
            unlink($file_path);
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Project deleted successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Failed to delete project: ' . $conn->error
        ]);
    }
    
    $delete_stmt->close();
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>