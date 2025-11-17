<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

// Include DB connection
include 'db_conn.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Read raw JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON input']);
    exit;
}

$id = $data['project_id'] ?? null;
$name = $data['project_name'] ?? '';
$description = $data['project_description'] ?? '';

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing project ID']);
    exit;
}

// Prepare SQL to prevent SQL injection
$stmt = $conn->prepare("UPDATE projects SET name = ?, description = ? WHERE id = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: '.$conn->error]);
    exit;
}

$stmt->bind_param("ssi", $name, $description, $id);

// Execute update
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

// Close statement and connection
$stmt->close();
$conn->close();
exit;
?>
