<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); // redirect to login if not logged in
    exit();
}
?>
