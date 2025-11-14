<?php
session_start();
echo "User ID: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET');
echo "<br>First Name: " . (isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'NOT SET');
?>