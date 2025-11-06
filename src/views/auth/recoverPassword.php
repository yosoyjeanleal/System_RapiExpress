<?php 
use RapiExpress\Helpers\Lang;
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">
<head>
    <meta charset="utf-8" />
    <title>RapiExpress - <?= Lang::get('recover_password_title') ?></title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    
    <!-- Fonts & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="assets/Temple/vendors/styles/core.css" />
    <link rel="stylesheet" type="text/css" href="assets/Temple/vendors/styles/icon-font.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/Temple/vendors/styles/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .toggle-password {
            cursor: pointer;
            user-select: none;
        }
        .toggle-password:hover {
            background-color: #f0f0f0;
        }
        .password-validation-container {
            background-color: #f8f9fa;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            margin-top: 8px;
            margin-bottom: 15px;
            display: none;
            transition: all 0.3s ease;
        }
        .password-validation-container.show {
            display: block;
        }
        .password-validation-container.shake {
            animation: shake 0.5s;
            border-color: #f39c12;
            box-shadow: 0 0 8px rgba(243, 156, 18, 0.3);
        }
        .validation-item {
            display: flex;
            align-items: center;
            padding: 6px 0;
            font-size: 13px;
            transition: all 0.3s ease;
        }
        .validation-item i {
            margin-right: 10px;
            font-size: 16px;
        }
        .validation-item.invalid {
            color: #dc3545;
        }
        .validation-item.valid {
            color: #28a745;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        @media (max-width: 768px) {
            .validation-item {
                font-size: 12px;
            }
        }
    </style>
</head>
<body class="login-page">

<div class="login-header box-shadow">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="brand-logo">
            <a href="#"><img src="assets/img/logo.png" alt="RapiExpress Logo" /></a>            
        </div>
        <div class="language-switch">
            <a href="index.php?c=lang&a=cambiar&lang=es">🇪🇸 Español</a> |
            <a href="index.php?c=lang&a=cambiar&lang=en">🇺🇸 English</a>
        </div>
    </div>
</div>

<div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 col-lg-7">
                <img src="assets/img/reset-page-img.svg" alt="<?= Lang::get('login_image_alt') ?>" />
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="login-box bg-white box-shadow border-radius-10">
                    <div class="login-title">
                        <h2 class="text-center text-primary"><?= Lang::get('recover_password_title') ?></h2>
                    </div>

                    <form method="POST" id="recoverPasswordForm">
                        <div class="input-group custom">
                            <input type="text" name="Username" id="Username" class="form-control form-control-lg" 
                                   placeholder="<?= Lang::get('username') ?>" required>
                            <div class="input-group-append custom">
                                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                            </div>
                        </div>

                        <div class="input-group custom mb-2">
                            <input name="Password" type="password" id="Password" class="form-control form-control-lg" 
                                   placeholder="<?= Lang::get('new_password') ?>" required>
                            <div class="input-group-append custom toggle-password">
                                <span class="input-group-text"><i class="fa fa-eye" id="toggleIcon"></i></span>
                            </div>
                        </div>
                        
                        <!-- Validación de contraseña -->
                        <div class="password-validation-container" id="passwordValidation">
                            <div class="validation-item invalid" data-requirement="length">
                                <i class="fa fa-circle-xmark"></i>
                                <span><?= Lang::get('password_min_8_chars') ?></span>
                            </div>
                            <div class="validation-item invalid" data-requirement="uppercase">
                                <i class="fa fa-circle-xmark"></i>
                                <span><?= Lang::get('password_uppercase') ?></span>
                            </div>
                            <div class="validation-item invalid" data-requirement="lowercase">
                                <i class="fa fa-circle-xmark"></i>
                                <span><?= Lang::get('password_lowercase') ?></span>
                            </div>
                            <div class="validation-item invalid" data-requirement="number">
                                <i class="fa fa-circle-xmark"></i>
                                <span><?= Lang::get('password_number') ?></span>
                            </div>
                            <div class="validation-item invalid" data-requirement="special">
                                <i class="fa fa-circle-xmark"></i>
                                <span><?= Lang::get('password_special') ?></span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-group mb-0">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="submitBtn">
                                        <?= Lang::get('reset_password') ?>
                                    </button>
                                </div>
                                <div class="font-16 weight-600 pt-10 pb-10 text-center" data-color="#707373">
                                    <?= Lang::get('or') ?>
                                </div>
                                <div class="input-group mb-0">
                                    <a href="index.php?c=auth&a=login" class="btn btn-outline-primary btn-lg btn-block">
                                        <?= Lang::get('back_to_login') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS -->
<script src="assets/Temple/vendors/scripts/core.js"></script>
<script src="assets/Temple/vendors/scripts/script.min.js"></script>
<script src="assets/Temple/vendors/scripts/layout-settings.js"></script>
<script src="assets/Temple/src/plugins/sweetalert2/sweetalert2.js"></script>
<script src="assets/js/Helpers/validation.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('Password');
    const validationContainer = document.getElementById('passwordValidation');
    const form = document.getElementById('recoverPasswordForm');
    const submitBtn = document.getElementById('submitBtn');
    const togglePassword = document.querySelector('.toggle-password');
    const toggleIcon = document.getElementById('toggleIcon');

    // Toggle mostrar/ocultar contraseña (MISMO CÓDIGO QUE LOGIN)
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    });

    // Mostrar validación al enfocar
    passwordInput.addEventListener('focus', function() {
        validationContainer.classList.add('show');
    });

    // Ocultar si está todo válido
    passwordInput.addEventListener('blur', function() {
        if (validatePassword(passwordInput.value)) {
            setTimeout(() => validationContainer.classList.remove('show'), 300);
        }
    });

    // Validar en tiempo real
    passwordInput.addEventListener('input', function() {
        updateValidationUI(this.value);
    });

    function updateValidationUI(password) {
        const requirements = validatePassword(password);
        let allValid = true;

        for (const req in requirements) {
            const item = validationContainer.querySelector(`[data-requirement="${req}"]`);
            const icon = item.querySelector('i');
            
            if (requirements[req]) {
                item.classList.remove('invalid');
                item.classList.add('valid');
                icon.className = 'fa fa-circle-check';
            } else {
                item.classList.remove('valid');
                item.classList.add('invalid');
                icon.className = 'fa fa-circle-xmark';
                allValid = false;
            }
        }
        return allValid;
    }

    // Envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const password = passwordInput.value;
        const isValid = updateValidationUI(password);

        if (!isValid) {
            validationContainer.classList.add('show', 'shake');
            setTimeout(() => validationContainer.classList.remove('shake'), 500);

            Swal.fire({
                icon: 'warning',
                title: '<?= Lang::get("invalid_password") ?>',
                text: '<?= Lang::get("password_requirements_message") ?>',
                confirmButtonColor: '#f39c12'
            });
            return;
        }

        // Deshabilitar botón
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <?= Lang::get("processing") ?>';

        // Enviar con AJAX
        const formData = new FormData(form);

        fetch('index.php?c=auth&a=recoverPassword', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '<?= Lang::get("success") ?>',
                    text: data.message,
                    confirmButtonColor: '#3085d6'
                }).then(() => {
                    window.location.href = 'index.php?c=auth&a=login';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '<?= Lang::get("error") ?>',
                    text: data.message,
                    confirmButtonColor: '#d33'
                });
                
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<?= Lang::get("reset_password") ?>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: '<?= Lang::get("connection_error") ?>',
                text: '<?= Lang::get("check_connection") ?>',
                confirmButtonColor: '#d33'
            });
            
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<?= Lang::get("reset_password") ?>';
        });
    });
});
</script>

</body>
</html>