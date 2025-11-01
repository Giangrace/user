<?php
session_start();
require_once 'connect.php';

if (isset($_POST['SignIn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Validate input
    if (empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location.href='index.php';</script>";
        exit();
    }
    
    // Check if user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            
            // Redirect to profile
            header("Location: profile.php");
            exit();
        } else {
            echo "<script>alert('Invalid password!'); window.location.href='index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Email not found!'); window.location.href='index.php';</script>";
        exit();
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>