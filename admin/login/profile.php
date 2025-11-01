<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$firstName = $_SESSION['first_name'];
$lastName = $_SESSION['last_name'];
$email = $_SESSION['email'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?> - Portfolio</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="responsive.css">
</head>
<body>
  <video autoplay muted loop id="bg-video">
    <source src="Live Wallpaper 4K Computer CPU.mp4" type="video/mp4">
  </video>
  
  <div class="container">
    <header class="animated">
      <a href="profile.php" class="logo"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></a>
      <nav class="navbar">
    <a href="profile.php" class="active">Home</a>
    <a href="../html/About.html">About</a>
    <a href="../html/portfolio.html">Portfolio</a>
    <a href="../html/service.html">Service</a>
    <a href="../projected/project.php">Add Project</a>
    <a href="../projected/view_projects.php">View Projects</a>
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
        </a>
      </nav>
    </header>
    
    <section class="home animated">
      <div class="home-detail">
        <h1>Hi, I'm <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h1>
        <h3>Frontend Developer</h3>
        <p>Welcome to my portfolio website! I'm passionate about creating beautiful and functional web experiences.</p>
        
        <div class="download-social">
          <a href="#" class="btn">Download CV</a>
          <div class="social-icons">
            <a href="https://www.facebook.com" target="_blank"><i class="fab fa-facebook-f"></i></a>
            <a href="https://twitter.com" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="https://www.linkedin.com" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            <a href="https://github.com" target="_blank"><i class="fab fa-github"></i></a>
          </div>
        </div>
      </div>
      
      <div class="home-img">
        <div class="img-box">
          <img src="viber_image_2025-10-03_13-21-37-385.jpg" alt="Profile">
        </div>
      </div>
    </section>
  </div>
  <script src="script.js"></script>
</body>
</html>