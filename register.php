<?php
require_once 'connect.php';

if (isset($_POST['SignUp'])) {
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (first_name, last_name, email, password) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?> 