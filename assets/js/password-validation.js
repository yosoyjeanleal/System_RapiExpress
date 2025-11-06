document.addEventListener('DOMContentLoaded', function () {
    const passwordFields = document.querySelectorAll('.password-input');
    const form = document.getElementById('recoverPasswordForm');

    // Crear contenedor de validación para cada campo de contraseña
    passwordFields.forEach(passwordInput => {
        const validationContainer = passwordInput.closest('.input-group').nextElementSibling;
        
        if (validationContainer && validationContainer.classList.contains('password-validation-container')) {
            
            // Mostrar/ocultar validación al enfocar/desenfocar
            passwordInput.addEventListener('focus', function () {
                validationContainer.style.display = 'block';
            });

            passwordInput.addEventListener('blur', function () {
                setTimeout(() => {
                    validationContainer.style.display = 'none';
                }, 200);
            });

            // Validar en tiempo real
            passwordInput.addEventListener('input', function () {
                const password = passwordInput.value;

                const requirements = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /[0-9]/.test(password),
                    special: /[!@#$%^&*]/.test(password)
                };

                // Actualizar cada requisito
                Object.keys(requirements).forEach(req => {
                    const item = validationContainer.querySelector(`[data-requirement="${req}"]`);
                    if (item) {
                        const icon = item.querySelector('i');
                        if (requirements[req]) {
                            item.classList.add('valid');
                            item.classList.remove('invalid');
                            icon.className = 'fa fa-circle-check';
                        } else {
                            item.classList.add('invalid');
                            item.classList.remove('valid');
                            icon.className = 'fa fa-circle-xmark';
                        }
                    }
                });
            });
        }
    });

    // Validar antes de enviar el formulario
    if (form) {
        form.addEventListener('submit', function (e) {
            let allValid = true;

            passwordFields.forEach(passwordInput => {
                if (form.contains(passwordInput)) {
                    const password = passwordInput.value;
                    const requirements = {
                        length: password.length >= 8,
                        uppercase: /[A-Z]/.test(password),
                        lowercase: /[a-z]/.test(password),
                        number: /[0-9]/.test(password),
                        special: /[!@#$%^&*]/.test(password)
                    };

                    const fieldValid = Object.values(requirements).every(valid => valid);
                    allValid = allValid && fieldValid;

                    if (!fieldValid) {
                        const validationContainer = passwordInput.closest('.input-group').nextElementSibling;
                        if (validationContainer && validationContainer.classList.contains('password-validation-container')) {
                            validationContainer.style.display = 'block';
                            validationContainer.classList.add('active');
                            validationContainer.style.animation = 'shake 0.5s';
                            setTimeout(() => {
                                validationContainer.style.animation = '';
                            }, 500);
                        }
                        passwordInput.focus();
                    }
                }
            });

            if (!allValid) {
                e.preventDefault();

                // Mostrar notificación con SweetAlert2
                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña inválida',
                    text: 'Por favor, asegúrate de que la contraseña cumpla con todos los requisitos de seguridad.',
                    confirmButtonColor: '#f39c12'
                });
            }
        });
    }
});