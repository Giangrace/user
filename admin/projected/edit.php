<?php
include 'db_conn.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $caption = mysqli_real_escape_string($conn, $_POST['caption']);
    
    $updateSql = "UPDATE projects SET caption = '$caption' WHERE id = '$id'";
    
    if (mysqli_query($conn, $updateSql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($conn)]);
    }
}
?>