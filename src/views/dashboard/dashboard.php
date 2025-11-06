<?php
use RapiExpress\Helpers\Lang;
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">
<head>
    <meta charset="utf-8" />
    <title>RapiExpress - Dashboard</title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>
<body>

    <?php include 'src/views/partels/barras.php'; ?>

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">

           

            <!-- Widgets -->
            <div class="row pb-10">
                <?php
                $widgets = [
                    ['valor' => $totalClientes, 'label' => Lang::get('dashboard_clients'), 'icon' => 'bi-people-fill', 'color' => '#00eccf'],
                    ['valor' => $totalUsuarios, 'label' => Lang::get('dashboard_employees'), 'icon' => 'bi-person-square', 'color' => '#ff5b5b'],
                    ['valor' => '000', 'label' => Lang::get('dashboard_deliveries'), 'icon' => 'bi-box-arrow-up', 'color' => '#6c757d'],
                    ['valor' => '00', 'label' => Lang::get('dashboard_failed'), 'icon' => 'bi-x-octagon', 'color' => '#09cc06'],
                    ['valor' => $totalTiendas ?? '0', 'label' => Lang::get('dashboard_stores'), 'icon' => 'bi-shop-window', 'color' => '#ff9f00'],
                    ['valor' => $totalCouriers ?? '0', 'label' => Lang::get('dashboard_couriers'), 'icon' => 'bi-truck', 'color' => '#3b7ddd'],
                    ['valor' => $totalPaquetes ?? '0', 'label' => Lang::get('dashboard_packages'), 'icon' => 'bi-box-seam', 'color' => '#6c757d'],
                    ['valor' => $totalReportes ?? '0', 'label' => Lang::get('dashboard_reports'), 'icon' => 'bi-bar-chart-line-fill', 'color' => '#dc3545'],
                ];
                foreach ($widgets as $w): ?>
                    <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                        <div class="card-box height-100-p widget-style3">
                            <div class="d-flex flex-wrap">
                                <div class="widget-data">
                                    <div class="weight-700 font-24 text-dark"><?= $w['valor'] ?></div>
                                    <div class="font-14 text-secondary weight-500"><?= $w['label'] ?></div>
                                </div>
                                <div class="widget-icon">
                                    <div class="icon" data-color="<?= $w['color'] ?>">
                                        <i class="micon bi <?= $w['icon'] ?>"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Lista de usuarios -->
            <div class="title pb-20">
                <h2 class="h3 mb-0"><?= Lang::get('dashboard_user_registry') ?></h2>
            </div>

            <div class="card-box mb-30">
                <div class="pd-20">
                    <h4 class="text-blue h4"><?= Lang::get('dashboard_user_list') ?></h4>
                </div>
                <div class="pb-20">
                    <table class="data-table table stripe hover nowrap" id="usuariosTable">
                        <thead>
                            <tr>
                                <th><?= Lang::get('user_document') ?></th>
                                <th><?= Lang::get('user_username') ?></th>
                                <th><?= Lang::get('user_fullname') ?></th>
                                <th><?= Lang::get('user_email') ?></th>
                                <th><?= Lang::get('user_phone') ?></th>
                                <th><?= Lang::get('user_branch') ?></th>
                                <th><?= Lang::get('user_position') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['ID_Usuario']) ?></td>
                                <td><?= htmlspecialchars($usuario['Username']) ?></td>
                                <td><?= htmlspecialchars($usuario['Nombres_Usuario'] . ' ' . $usuario['Apellidos_Usuario']) ?></td>
                                <td><?= htmlspecialchars($usuario['Correo_Usuario']) ?></td>
                                <td><?= htmlspecialchars($usuario['Telefono_Usuario']) ?></td>
                                <td><?= htmlspecialchars($usuario['Sucursal_Nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['Cargo_Nombre']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>


   <!-- Footer -->
    <div class="footer-wrap pd-20 mb-20 card-box">
        <?= Lang::get('footer_text') ?>
    </div>
        </div>
    </div>

</body>
</html>
