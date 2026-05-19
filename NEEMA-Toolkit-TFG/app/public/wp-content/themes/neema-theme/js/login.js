document.addEventListener("DOMContentLoaded", function() {
  document.querySelectorAll('.password-toggle-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const targetId = this.getAttribute('data-target');
      const input = document.getElementById(targetId);
      const icon = this.querySelector('i');
      if (!input) return;
      if (input.style.webkitTextSecurity === 'disc' || !input.style.webkitTextSecurity) {
        input.style.webkitTextSecurity = 'none';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.style.webkitTextSecurity = 'disc';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    });
  });
});
