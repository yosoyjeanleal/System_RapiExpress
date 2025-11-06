
  document.querySelectorAll('.toggle-password').forEach(toggle => {
    toggle.addEventListener('click', function () {
      const inputGroup = this.closest('.input-group'); // Encuentra el grupo contenedor
      const input = inputGroup.querySelector('.password-input'); // Busca el input dentro del grupo
      const icon = this.querySelector('i');

      if (input) {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        icon.classList.toggle('fa-eye', !isPassword);
        icon.classList.toggle('fa-eye-slash', isPassword);
      }
    });
  });
