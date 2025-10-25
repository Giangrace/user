<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connect.php';

if (isset($_POST['SignUp'])) {
    $firstName = trim($_POST['fName']);
    $lastName = trim($_POST['lName']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location.href='index.html';</script>";
        exit();
    }
    
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT email FROM user WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();
    
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='index.html';</script>";
    } else {
        // Insert new user
        $stmt = $conn->prepare("INSERT INTO user (firstName, lastName, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);
        
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please login.'); window.location.href='index.html';</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "'); window.location.href='index.html';</script>";
        }
        $stmt->close();
    }
    $checkEmail->close();
} else {
    echo "This page should only be accessed via form submission.";
}
$conn->close();
?>