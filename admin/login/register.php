<?php
session_start();
require_once 'connect.php';

if (isset($_POST['SignUp'])) {
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location.href='index.php';</script>";
        exit();
    }
    
    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = ?";
    $checkStmt = $conn->prepare($checkEmail);
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='index.php';</script>";
        exit();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user
    $sql = "INSERT INTO users (first_name, last_name, email, password) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        echo "<script>
            alert('Registration successful! Please sign in.');
            window.location.href='index.php';
        </script>";
        exit();
    } else {
        echo "<script>alert('Error: " . $conn->error . "'); window.location.href='index.php';</script>";
    }
    
    $stmt->close();
    $checkStmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>