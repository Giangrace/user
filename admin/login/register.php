<?php
session_start();
require_once 'connect.php';

if (isset($_POST['SignUp'])) {
    $firstName = trim($_POST['fName']);
    $lastName = trim($_POST['lName']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        echo "<script>alert('All fields are required!'); window.location.href='index.php';</script>";
        exit();
    }

    // Check if email already exists
    $checkEmail = "SELECT * FROM users WHERE email = ?";
    $checkStmt = $conn->prepare($checkEmail);
    if (!$checkStmt) {
        die("Error preparing statement (check email): " . $conn->error);
    }

    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result && $result->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location.href='index.php';</script>";
        $checkStmt->close();
        $conn->close();
        exit();
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $sql = "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement (insert user): " . $conn->error);
    }

    $stmt->bind_param("ssss", $firstName, $lastName, $email, $hashedPassword);

    if ($stmt->execute()) {
        echo "<script>
            alert('Registration successful! Please sign in.');
            window.location.href='index.php';
        </script>";
    } else {
        echo "<script>alert('Database error: " . $stmt->error . "'); window.location.href='index.php';</script>";
    }

    $stmt->close();
    $checkStmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>
