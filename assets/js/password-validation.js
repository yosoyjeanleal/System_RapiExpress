document.addEventListener('DOMContentLoaded', function () {
    const passwordFields = document.querySelectorAll('.password-input');
    const form = document.getElementById('recoverPasswordForm');

    passwordFields.forEach(passwordInput => {
        const validationContainer = passwordInput.closest('.input-group').nextElementSibling;
        
        if (validationContainer && validationContainer.classList.contains('password-validation-container')) {
            
            passwordInput.addEventListener('focus', function () {
                validationContainer.style.display = 'block';
            });

            passwordInput.addEventListener('blur', function () {
                setTimeout(() => {
                    if (Object.values(validatePassword(passwordInput.value)).every(valid => valid)) {
                        validationContainer.style.display = 'none';
                    }
                }, 200);
            });

            passwordInput.addEventListener('input', function () {
                updateValidationUI(passwordInput.value, validationContainer);
            });
        }
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            let allValid = true;

            passwordFields.forEach(passwordInput => {
                if (form.contains(passwordInput)) {
                    const validationContainer = passwordInput.closest('.input-group').nextElementSibling;
                    const fieldValid = updateValidationUI(passwordInput.value, validationContainer);
                    allValid = allValid && fieldValid;

                    if (!fieldValid) {
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

                Swal.fire({
                    icon: 'warning',
                    title: 'Contraseña inválida',
                    text: 'Por favor, asegúrate de que la contraseña cumpla con todos los requisitos de seguridad.',
                    confirmButtonColor: '#f39c12'
                });
            }
        });
    }

    function updateValidationUI(password, validationContainer) {
        const requirements = validatePassword(password);
        let allValid = true;

        for (const req in requirements) {
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
                    allValid = false;
                }
            }
        }
        return allValid;
    }
});
