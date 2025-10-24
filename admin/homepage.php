<?php
session_start();
include 'connect.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Junard B. Taoy - Portfolio</title>
</head>
<body>
<div style="text-align: center; padding: 15%;">
  <p style="font-size:50px; font-weight:bold;">
    Hello
    <?php
    if (isset($_SESSION['email'])) {
        $email = $_SESSION['email'];
        // ✅ Fixed table name quotes and alias
        $squery = mysqli_query($conn, "SELECT * FROM user WHERE email = '$email'");

        if ($squery && mysqli_num_rows($squery) > 0) {
            while ($row = mysqli_fetch_assoc($squery)) {
                echo ' ' . htmlspecialchars($row['firstName']) . ' ' . htmlspecialchars($row['lastName']);
            }
        }
    }
    ?>
    :)
  </p>

  <p>
    <?php
    if (isset($_SESSION['username'])) {
        echo "Welcome, " . htmlspecialchars($_SESSION['username']) . 
             " | <a href='logout.php'>Logout</a>";
    } else {
        echo "<a href='login.php'>Login</a> | <a href='register.php'>Register</a>";
    }
    ?>
  </p>
</div>
</body>
</html>
