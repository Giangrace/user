<?php
include 'db_conn.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM projects ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result) {
    $projects = array();
    
    while ($row = mysqli_fetch_assoc($result)) {
        $projects[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'projects' => $projects
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching projects: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>