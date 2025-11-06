<?php 
use RapiExpress\Helpers\Lang;
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">
<head>
    <meta charset="utf-8" />
    <title>RapiExpress - <?= Lang::get('login_title') ?></title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    
    <!-- Fonts & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/Temple/vendors/styles/core.css" />
    <link rel="stylesheet" href="assets/Temple/vendors/styles/icon-font.min.css" />
    <link rel="stylesheet" href="assets/Temple/vendors/styles/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .toggle-password {
            cursor: pointer;
            user-select: none;
        }
        .toggle-password:hover {
            background-color: #f0f0f0;
        }
        .login-wrap {
            min-height: 100vh;
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
                <img src="assets/img/login-page-img.svg" alt="<?= Lang::get('login_image_alt') ?>" />
            </div>
            <div class="col-md-6 col-lg-5">
                <div class="login-box bg-white box-shadow border-radius-10">
                    <div class="login-title">
                        <h2 class="text-center text-primary"><?= Lang::get('login_title') ?></h2>
                    </div>

                    <form id="formLogin" method="POST">
                        <div class="input-group custom">
                            <input name="Username" id="Username" class="form-control form-control-lg"
                                   placeholder="<?= Lang::get('username') ?>" required>
                            <div class="input-group-append custom">
                                <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                            </div>
                        </div>

                        <div class="input-group custom mb-4">
                            <input name="Password" id="Password" type="password" class="form-control form-control-lg"
                                   placeholder="<?= Lang::get('password') ?>" required>
                            <div class="input-group-append custom toggle-password">
                                <span class="input-group-text"><i class="fa fa-eye" id="toggleIcon"></i></span>
                            </div>
                        </div>

                        <div class="row pb-30">
                            <div class="col-6"></div>
                            <div class="col-6 text-right">
                                <a href="index.php?c=auth&a=recoverPassword"><?= Lang::get('forgot_password') ?></a>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <div class="input-group mb-0">
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="submitBtn">
                                        <?= Lang::get('enter') ?>
                                    </button>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formLogin');
    const passwordInput = document.getElementById('Password');
    const togglePassword = document.querySelector('.toggle-password');
    const toggleIcon = document.getElementById('toggleIcon');
    const submitBtn = document.getElementById('submitBtn');

    // Toggle mostrar/ocultar contraseña
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    });

    // Envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Deshabilitar botón
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <?= Lang::get("processing") ?>';

        const formData = new FormData(form);

        fetch('index.php?c=auth&a=login', {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: '<?= Lang::get("welcome") ?>',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false,
                    allowOutsideClick: false
                }).then(() => window.location.href = data.redirect);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '<?= Lang::get("error") ?>',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                passwordInput.value = '';
                passwordInput.focus();
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<?= Lang::get("enter") ?>';
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: '<?= Lang::get("error") ?>',
                text: '<?= Lang::get("unexpected_error") ?>',
                showConfirmButton: false,
                timer: 2000
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<?= Lang::get("enter") ?>';
        });
    });
});
</script>

</body>
</html>