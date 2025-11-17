<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_conn.php';
header('Content-Type: application/json');

// Read raw JSON body
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$id = $data['project_id'];
$name = $data['project_name'];
$description = $data['project_description'];

// Use prepared statement to avoid SQL injection
$stmt = $conn->prepare("UPDATE projects SET name = ?, description = ? WHERE id = ?");
$stmt->bind_param("ssi", $name, $description, $id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>