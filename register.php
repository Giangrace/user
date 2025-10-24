<?php
include 'connect.php';
session_start();

if (isset($_POST['SignUp'])) {
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // encrypted

    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>alert('Email Address Already Exists.');</script>";
    } else {
        $insertQuery = "INSERT INTO users (fName, lName, email, password) VALUES ('$fName', '$lName', '$email', '$password')";
        if ($conn->query($insertQuery) === TRUE) {
            echo "<script>alert('Registration Successful! Please login.'); window.location='register.php';</script>";
        } else {
            echo "Error: " . $conn->error;
        }
    }
}

if (isset($_POST['SignIn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['email'] = $row['email'];
        $_SESSION['username'] = $row['fName'];
        header("Location: index.php");
        exit();
    } else {
        echo "<script>alert('Incorrect Email or Password.');</script>";
    }
}
?>
