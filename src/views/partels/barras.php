<?php

use RapiExpress\Helpers\Lang;

$imagenUsuario = $_SESSION['imagen_usuario'] ?? 'default.png';
$rutaImagen = file_exists("uploads/{$imagenUsuario}")
    ? "uploads/{$imagenUsuario}"
    : "assets/img/default.png";
?>

<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">
<html>

<head>

    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>RapiExpress - Dashboard</title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- CSS -->
    <!-- Google Font -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="assets\css/preloader.css" />
    <link rel="stylesheet" type="text/css" href="assets/Temple/vendors/styles/core.css" />
    <link rel="stylesheet" type="text/css" href="assets/Temple/vendors/styles/icon-font.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/Temple/src/plugins/datatables/css/dataTables.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/Temple/src/plugins/datatables/css/responsive.bootstrap4.min.css" />
    <link rel="stylesheet" type="text/css" href="assets/Temple/vendors/styles/style.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/styles.css" />
    

    <link rel="stylesheet" href="assets\css/password-validation.css" />


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<body>
    <div id="preloader">
        <div class="loader"></div>
    </div>

    <div class="header">
        <div class="header-left">
            <div class="menu-icon bi bi-list"></div>

        </div>

        <div class="header-right">

            <div class="dashboard-setting user-notification">
                <div class="dropdown">
                    <a class="dropdown-toggle no-arrow" href="javascript:;" data-toggle="right-sidebar">
                        <i class="dw dw-settings2"></i>
                    </a>
                </div>
            </div>
            <div class="user-info-dropdown">
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <span class="user-icon">
                            <img src="<?= htmlspecialchars($rutaImagen) ?>" alt="Foto de perfil" class="rounded-circle" width="52" height="52" style="object-fit: cover;">
                        </span>
                        <span class="user-name">
                            <?= isset($_SESSION['nombre_completo'])
                                ? htmlspecialchars($_SESSION['nombre_completo'])
                                : (isset($_SESSION['usuario'])
                                    ? htmlspecialchars($_SESSION['usuario'])
                                    : 'Invitado') ?>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                        <?php if (isset($_SESSION['usuario'])): ?>
                            <a class="dropdown-item" href="index.php?c=perfil&a=index">
                                <i class="dw dw-user1"></i> <?php echo Lang::get('profile'); ?>
                            </a>
                            <a class="dropdown-item" href="">
                                <i class="bi bi-question-circle"></i> <?php echo Lang::get('info'); ?>
                            </a>
                            <a class="dropdown-item" href="index.php?c=auth&a=logout">
                                <i class="dw dw-logout"></i> <?php echo Lang::get('logout'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="right-sidebar">
        <div class="sidebar-title">
            <h3 class="weight-600 font-16 text-blue">
                configuración de diseño
                <span class="btn-block font-weight-400 font-12">Configuración de la interfaz de usuario</span>
            </h3>
            <div class="close-sidebar" data-toggle="right-sidebar-close">
                <i class="icon-copy ion-close-round"></i>
            </div>
        </div>
        <div class="right-sidebar-body customscroll">

            <div class="right-sidebar-body-content">
                <h4 class="weight-600 font-18 pb-10">Idioma🌐</h4>
                <div class="sidebar-btn-group pb-30 mb-10">
                    <a href="index.php?c=lang&a=cambiar&lang=es" class="btn btn-outline-primary ">ES</a>
                    <a href="index.php?c=lang&a=cambiar&lang=en" class="btn btn-outline-primary ">EN</a>
                </div>
                <h4 class="weight-600 font-18 pb-10">Fondo del encabezado</h4>
                <div class="sidebar-btn-group pb-30 mb-10">
                    <a href="javascript:void(0);" class="btn btn-outline-primary header-white active">Claro</a>
                    <a href="javascript:void(0);" class="btn btn-outline-primary header-dark">Oscuro</a>
                </div>
                <h4 class="weight-600 font-18 pb-10">Fondo de la barra lateral</h4>
                <div class="sidebar-btn-group pb-30 mb-10">
                    <a href="javascript:void(0);" class="btn btn-outline-primary sidebar-light">Claro</a>
                    <a href="javascript:void(0);" class="btn btn-outline-primary sidebar-dark active">Oscuro</a>
                </div>

                <div class="reset-options pt-30 text-center">
                    <button class="btn btn-danger" id="reset-settings">
                        Restablecer configuración
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="left-side-bar">
        <div class="brand-logo">
            <a href="index.php?c=dashboard&a=index">
                <img src="assets/img/logo.png" alt="" class="dark-logo" />
                <img src="assets/img/logo.png" alt="" class="light-logo" />
            </a>
            <div class="close-sidebar" data-toggle="left-sidebar-close">
                <i class="ion-close-round"></i>
            </div>
        </div>
        <div class="menu-block customscroll">
            <div class="sidebar-menu">
                <ul id="accordion-menu">
                    <?php if (isset($_SESSION['ID_Cargo']) && $_SESSION['ID_Cargo'] === 1): ?>
                        <li>
                            <a href="index.php?c=dashboard&a=index" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-speedometer2"></span>
                                <span class="mtext"><?= Lang::get('menu_dashboard') ?></span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon bi bi-box2-heart"></span>
                                <span class="mtext"><?= Lang::get('Gestion') ?></span>
                            </a>
                            <ul class="submenu">
                                <li> <a href="index.php?c=cargo&a=index" class="dropdown-toggle no-arrow">

                                        <span class="mtext"><?= Lang::get('menu_positions') ?></span>
                                    </a></li>
                                <li> <a href="index.php?c=casillero&a=index" class="dropdown-toggle no-arrow">
                                        <span class="mtext"><?= Lang::get('menu_lockers') ?></span>
                                    </a></li>
                                <li> <a href="index.php?c=sucursal&a=index" class="dropdown-toggle no-arrow">
                                        <span class="mtext"><?= Lang::get('menu_branches') ?></span>
                                    </a></li>
                                <li> <a href="index.php?c=categoria&a=index" class="dropdown-toggle no-arrow">
                                        <span class="mtext"><?= Lang::get('menu_categories') ?></span>
                                    </a></li>
                                <li> <a href="index.php?c=tienda&a=index" class="dropdown-toggle no-arrow">
                                        <span class="mtext"><?= Lang::get('menu_stores') ?></span>
                                    </a></li>
                                <li> <a href="index.php?c=courier&a=index" class="dropdown-toggle no-arrow">
                                        <span class="mtext"><?= Lang::get('menu_couriers') ?></span>
                                    </a></li>
                                <li> <a href="index.php?c=usuario&a=index" class="dropdown-toggle no-arrow">
                                        <span class="mtext"><?= Lang::get('menu_employees') ?></span>
                                    </a></li>
                            </ul>
                        </li>
                        <li>

                        </li>
                        <li>
                            <a href="index.php?c=cliente&a=index" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-people-fill"></span>
                                <span class="mtext"><?= Lang::get('menu_clients') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?c=seguimiento&a=index" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-geo-alt"></span>
                                <span class="mtext"><?= Lang::get('menu_tracking') ?></span>
                            </a>
                        </li>

                        <li class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon bi bi-box2-heart"></span>
                                <span class="mtext"><?= Lang::get('menu_packages') ?></span>
                            </a>
                            <ul class="submenu">
                                <li><a href="index.php?c=prealerta&a=index"><?= Lang::get('submenu_prealert') ?></a></li>
                                <li><a href="index.php?c=paquete&a=index"><?= Lang::get('submenu_consolidated') ?></a></li>
                            </ul>
                        </li>                        
                        <li class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon bi bi-bag-dash"></span>
                                <span class="mtext"><?= Lang::get('menu_sacks') ?></span>
                            </a>
                            <ul class="submenu">
                                <li><a href="index.php?c=saca&a=index"><?= Lang::get('submenu_sack_registry') ?></a></li>
                                <li><a href="index.php?c=detallesaca&a=index"><?= Lang::get('submenu_sack_packages') ?></a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="index.php?c=manifiesto&a=index" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-clipboard2-data"></span>
                                <span class="mtext"><?= Lang::get('menu_manifest') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-receipt-cutoff"></span>
                                <span class="mtext"><?= Lang::get('menu_reports') ?></span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="index.php?c=dashboard&a=index" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-speedometer2"></span>
                                <span class="mtext"><?= Lang::get('menu_dashboard') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?c=cliente&a=index" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-people-fill"></span>
                                <span class="mtext"><?= Lang::get('menu_clients') ?></span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon bi bi-box2-heart"></span>
                                <span class="mtext"><?= Lang::get('menu_packages') ?></span>
                            </a>
                            <ul class="submenu">
                                <li><a href="index.php?c=prealerta&a=index"><?= Lang::get('submenu_prealert') ?></a></li>
                                <li><a href="index.php?c=paquete&a=index"><?= Lang::get('submenu_consolidated') ?></a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="index.php?c=seguimiento&a=index" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-geo-alt"></span>
                                <span class="mtext"><?= Lang::get('menu_tracking') ?></span>
                            </a>
                        </li>
                        <li class="dropdown">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon bi bi-bag-dash"></span>
                                <span class="mtext"><?= Lang::get('menu_sacks') ?></span>
                            </a>
                            <ul class="submenu">
                                <li><a href="index.php?c=entrada&a=index"><?= Lang::get('submenu_sack_registry') ?></a></li>
                                <li><a href="#"><?= Lang::get('submenu_sack_packages') ?></a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="index.php?c=manifiesto&a=index" class="dropdown-toggle no-arrow">
                                <span class="micon bi bi-clipboard2-data"></span>
                                <span class="mtext"><?= Lang::get('menu_manifest') ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>






    </script>



    <!-- js -->
    <script src="assets/Temple/vendors/scripts/core.js"></script>
    <script src="assets/Temple/vendors/scripts/script.min.js"></script>
    <script src="assets/Temple/vendors/scripts/process.js"></script>
    <script src="assets/Temple/vendors/scripts/layout-settings.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/dataTables.responsive.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
    <!-- buttons for Export datatable -->
    <script src="assets/Temple/src/plugins/datatables/js/dataTables.buttons.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/buttons.print.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/buttons.html5.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/buttons.flash.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/pdfmake.min.js"></script>
    <script src="assets/Temple/src/plugins/datatables/js/vfs_fonts.js"></script>
    <script src="assets/Temple/src/plugins/sweetalert2/sweetalert2.js"></script>
    <!-- Datatable Setting js -->
    <script src="assets/Temple/vendors/scripts/datatable-setting.js"></script>
    <!-- JavaScript -->

    <script src="assets\js\loading.js"></script>

    <script src="assets\js\password-validation.js"></script>
    <script src="assets\js\password_view.js"></script>





</body>

</html>