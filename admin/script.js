// Get form containers
const signUpContainer = document.getElementById('signUp');
const signInContainer = document.getElementById('signIn');

// Get toggle buttons
const signInButton = document.getElementById('signInButton');
const signUpButton = document.getElementById('signUpButton');

// Toggle to Sign In form
signInButton.addEventListener('click', () => {
  signUpContainer.style.display = 'none';
  signInContainer.style.display = 'block';
});

// Toggle to Sign Up form
signUpButton.addEventListener('click', () => {
  signInContainer.style.display = 'none';
  signUpContainer.style.display = 'block';
});

// Password toggle functionality
const togglePasswordButtons = document.querySelectorAll('.togglePassword');

togglePasswordButtons.forEach(button => {
  button.addEventListener('click', function() {
    const passwordInput = this.previousElementSibling.previousElementSibling;
    
    if (passwordInput.type === 'password') {
      passwordInput.type = 'text';
      this.classList.remove('fa-eye');
      this.classList.add('fa-eye-slash');
    } else {
      passwordInput.type = 'password';
      this.classList.remove('fa-eye-slash');
      this.classList.add('fa-eye');
    }
  });
});