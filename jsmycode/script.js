// Toggle between Sign Up and Sign In forms
const signUpButton = document.getElementById('signUpButton');
const signInButton = document.getElementById('signInButton');
const signUpForm = document.getElementById('signUp');
const signInForm = document.getElementById('signIn');

// Switch to Sign In form
signInButton.addEventListener('click', function() {
  signUpForm.style.display = 'none';
  signInForm.style.display = 'block';
});

// Switch to Sign Up form
signUpButton.addEventListener('click', function() {
  signInForm.style.display = 'none';
  signUpForm.style.display = 'block';
});

// Password visibility toggle
const togglePasswordIcons = document.querySelectorAll('.togglePassword');

togglePasswordIcons.forEach(icon => {
  icon.addEventListener('click', function() {
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

// Form validation (optional enhancement)
document.querySelectorAll('form').forEach(form => {
  form.addEventListener('submit', function(e) {
    const inputs = this.querySelectorAll('input[required]');
    let isValid = true;
    
    inputs.forEach(input => {
      if (!input.value.trim()) {
        isValid = false;
        input.style.borderColor = '#f44336';
      } else {
        input.style.borderColor = '#e0e0e0';
      }
    });
    
    if (!isValid) {
      e.preventDefault();
      alert('Please fill in all required fields');
    }
  });
});