<?php
// Turn off error display to prevent HTML in JSON response
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once 'db_conn.php';

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