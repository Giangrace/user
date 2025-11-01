// Toggle between Sign In and Sign Up forms
const signInButton = document.getElementById('signInButton');
const signUpButton = document.getElementById('signUpButton');
const signInForm = document.getElementById('signIn');
const signUpForm = document.getElementById('signUp');

signInButton.addEventListener('click', function() {
  signUpForm.style.display = 'none';
  signInForm.style.display = 'block';
});

signUpButton.addEventListener('click', function() {
  signInForm.style.display = 'none';
  signUpForm.style.display = 'block';
});

// Password toggle functionality
const togglePasswords = document.querySelectorAll('.togglePassword');
togglePasswords.forEach(function(toggle) {
  toggle.addEventListener('click', function() {
    const passwordInput = this.previousElementSibling.previousElementSibling;
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
  });
});