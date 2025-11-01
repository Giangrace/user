<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login & Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <div class="wrapper">
      <!-- REGISTER FORM -->
      <div class="container" id="signUp">
        <h1 class="form-title">Register</h1>
        
        <form method="post" action="register.php">
          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="fName" id="fName" placeholder=" " required>
            <label for="fName">First Name</label>
          </div>

          <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="lName" id="lName" placeholder=" " required>
            <label for="lName">Last Name</label>
          </div>

          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email" placeholder=" " required>
            <label for="email">Email</label>
          </div>

          <div class="input-group password-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="password" placeholder=" " required>
            <label for="password">Password</label>
            <i class="fas fa-eye togglePassword"></i>
          </div>

          <input type="submit" class="btn" value="Sign Up" name="SignUp">
        </form>

        <p class="or">-------- or --------</p>
        
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-google"></i></a>
        </div>

        <div class="links">
          <p>Already have an account?</p>
          <button id="signInButton">Sign In</button>
        </div>
      </div>

      <!-- SIGN IN FORM -->
      <div class="container" id="signIn" style="display:none;">
        <h1 class="form-title">Sign In</h1>
        
        <form method="post" action="login.php">
          <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" id="email-login" placeholder=" " required>
            <label for="email-login">Email</label>
          </div>

          <div class="input-group password-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" id="password-login" placeholder=" " required>
            <label for="password-login">Password</label>
            <i class="fas fa-eye togglePassword"></i>
          </div>

          <input type="submit" class="btn" value="Sign In" name="SignIn">
        </form>

        <p class="or">-------- or --------</p>
        
        <div class="social-icons">
          <a href="#"><i class="fab fa-facebook-f"></i></a>
          <a href="#"><i class="fab fa-google"></i></a>
        </div>

        <div class="links">
          <p>Don't have an account yet?</p>
          <button id="signUpButton">Sign Up</button>
        </div>
      </div>
    </div>

    <script src="script.js"></script>
  </body>
</html>