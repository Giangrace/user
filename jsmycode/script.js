// 🔁 Switch Forms
document.getElementById("SignInButton").addEventListener("click", () => {
  document.getElementById("signUp").style.display = "none";
  document.getElementById("signIn").style.display = "block";
});

document.getElementById("SignUpButton").addEventListener("click", () => {
  document.getElementById("signIn").style.display = "none";
  document.getElementById("signUp").style.display = "block";
});

// 👁️ Toggle Password Visibility
document.querySelectorAll(".togglePassword").forEach(icon => {
  icon.addEventListener("click", () => {
    const input = icon.previousElementSibling;
    if (input.type === "password") {
      input.type = "text";
      icon.classList.replace("fa-eye", "fa-eye-slash");
    } else {
      input.type = "password";
      icon.classList.replace("fa-eye-slash", "fa-eye");
    }
  });
});
