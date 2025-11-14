<?php
session_start();
?>
<h1>Session Information</h1>
<p>User ID: <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET'; ?></p>
<p>First Name: <?php echo isset($_SESSION['first_name']) ? $_SESSION['first_name'] : 'NOT SET'; ?></p>
<p>Last Name: <?php echo isset($_SESSION['last_name']) ? $_SESSION['last_name'] : 'NOT SET'; ?></p>