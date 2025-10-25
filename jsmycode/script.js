// Toggle between Sign In and Sign Up forms
const signUpButton = document.getElementById('signUpButton');
const signInButton = document.getElementById('signInButton');
const signUpForm = document.getElementById('signUp');
const signInForm = document.getElementById('signIn');

signInButton.addEventListener('click', () => {
  signUpForm.style.display = 'none';
  signInForm.style.display = 'block';
});

signUpButton.addEventListener('click', () => {
  signInForm.style.display = 'none';
  signUpForm.style.display = 'block';
});

// Toggle password visibility
const togglePasswordButtons = document.querySelectorAll('.togglePassword');

togglePasswordButtons.forEach(button => {
  button.addEventListener('click', function() {
    const passwordInput = this.previousElementSibling.previousElementSibling;
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);
    this.classList.toggle('fa-eye');
    this.classList.toggle('fa-eye-slash');
  });
});