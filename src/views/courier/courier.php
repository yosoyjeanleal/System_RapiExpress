<?php

use RapiExpress\Helpers\Lang;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>RapiExpress</title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>

<body>
    <?php include 'src/views/partels/barras.php'; ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <div class="title pb-20">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <h4><?= Lang::get('couriers_title') ?></h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="index.php?c=dashboard&a=index"><?= Lang::get('breadcrumb_home') ?></a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <?= Lang::get('couriers_title') ?>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="card-box mb-30">
                    <div class="pd-30">
                        <h4 class="text-blue h4"><?= Lang::get('couriers_list_title') ?></h4>
                        <?php include 'src/views/partels/notificaciones.php'; ?>

                        <div class="pull-right">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#courierModal">
                                <i class="fa fa-truck"></i> <?= Lang::get('add_courier') ?>
                            </button>
                        </div>
                    </div>
                    <div class="pb-30">
                        <table class="data-table table stripe hover nowrap" id="couriersTable">
                            <thead>
                                <tr>
                                    <th><?= Lang::get('rif') ?></th>
                                    <th><?= Lang::get('name') ?></th>
                                    <th><?= Lang::get('address') ?></th>
                                    <th><?= Lang::get('phone') ?></th>
                                    <th><?= Lang::get('email') ?></th>
                                    <th class="datatable-nosort"><?= Lang::get('actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($couriers as $courier): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($courier['RIF_Courier']) ?></td>
                                        <td><?= htmlspecialchars($courier['Courier_Nombre']) ?></td>
                                        <td><?= htmlspecialchars($courier['Courier_Direccion']) ?></td>
                                        <td><?= htmlspecialchars($courier['Courier_Telefono']) ?></td>
                                        <td><?= htmlspecialchars($courier['Courier_Correo']) ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <!-- Botón Editar -->
                                                <a href="#"
                                                    data-color="#265ed7"
                                                    data-toggle="modal"
                                                    data-target="#edit-courier-modal-<?= $courier['ID_Courier'] ?>"
                                                    title="<?= Lang::get('edit_courier_modal_title') ?>">
                                                    <i class="icon-copy dw dw-edit2"></i>
                                                </a>

                                                <!-- Botón Eliminar -->
                                                <a href="#"
                                                    data-color="#e95959"
                                                    data-toggle="modal"
                                                    data-target="#delete-courier-modal"
                                                    onclick="setDeleteId(<?= $courier['ID_Courier'] ?>)"
                                                    title="<?= Lang::get('delete_courier_modal_title') ?>">
                                                    <i class="icon-copy dw dw-delete-3"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal Agregar Courier -->
                <div class="modal fade" id="courierModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="formRegistrarCourier" method="POST" action="index.php?c=courier&a=registrar">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?= Lang::get('register_courier_modal_title') ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= Lang::get('close') ?>">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('rif') ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="RIF_Courier" pattern="^[JGVEP]-\d{8}-\d$" title="Formato válido: J-12345678-9" maxlength="12" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('name') ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="Courier_Nombre" pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$" title="Solo letras y espacios (3-50 caracteres)" maxlength="50" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('address') ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="Courier_Direccion" maxlength="150" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('phone') ?></label>
                                        <div class="col-sm-9">
                                            <input type="tel" class="form-control" name="Courier_Telefono" pattern="^(\+?\d{1,3})?\d{7,15}$" title="Formato: 04121234567 o +584121234567" maxlength="15" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('email') ?></label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" name="Courier_Correo" maxlength="100" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('cancel') ?></button>
                                    <button type="submit" class="btn btn-primary"><?= Lang::get('register') ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <?php foreach ($couriers as $courier): ?>
                    <!-- Modal Editar -->
                    <div class="modal fade" id="edit-courier-modal-<?= $courier['ID_Courier'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form id="formEditarCourier-<?= $courier['ID_Courier'] ?>" method="POST" action="index.php?c=courier&a=editar">
                                    <div class="modal-header">
                                        <h4 class="modal-title"><?= Lang::get('edit_courier_modal_title') ?></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Lang::get('close') ?>">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <input type="hidden" name="ID_Courier" value="<?= $courier['ID_Courier'] ?>">
                                        
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('rif') ?></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="RIF_Courier"
                                                    value="<?= htmlspecialchars($courier['RIF_Courier']) ?>"
                                                    pattern="^[JGVEP]-\d{8}-\d$" maxlength="12" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('name') ?></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="Courier_Nombre"
                                                    value="<?= htmlspecialchars($courier['Courier_Nombre']) ?>"
                                                    pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,50}$" maxlength="50" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('address') ?></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="Courier_Direccion"
                                                    value="<?= htmlspecialchars($courier['Courier_Direccion']) ?>"
                                                    maxlength="150" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('phone') ?></label>
                                            <div class="col-sm-9">
                                                <input type="tel" class="form-control" name="Courier_Telefono"
                                                    value="<?= htmlspecialchars($courier['Courier_Telefono']) ?>"
                                                    pattern="^(\+?\d{1,3})?\d{7,15}$" maxlength="15" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('email') ?></label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" name="Courier_Correo"
                                                    value="<?= htmlspecialchars($courier['Courier_Correo']) ?>"
                                                    maxlength="100" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('cancel') ?></button>
                                        <button type="submit" class="btn btn-primary"><?= Lang::get('save_changes') ?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Modal Eliminar -->
                <div class="modal fade" id="delete-courier-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-center p-4">
                            <div class="modal-body">
                                <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                                <h4 class="mb-20 font-weight-bold text-danger"><?= Lang::get('delete_courier_modal_title') ?></h4>
                                <p class="mb-30 text-muted"><?= Lang::get('delete_courier_modal_text') ?></p>

                                <form id="formEliminarCourier" method="POST" action="index.php?c=courier&a=eliminar">
                                    <input type="hidden" name="ID_Courier" id="delete_courier_id">
                                    <div class="row justify-content-center" style="max-width: 200px; margin: 0 auto;">
                                        <div class="col-6 px-1">
                                            <button type="button" class="btn btn-secondary btn-lg btn-block" data-dismiss="modal">
                                                <i class="fa fa-times"></i> <?= Lang::get('no') ?>
                                            </button>
                                        </div>
                                        <div class="col-6 px-1">
                                            <button type="submit" class="btn btn-danger btn-lg btn-block">
                                                <i class="fa fa-check"></i> <?= Lang::get('yes') ?>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

<script src="assets/js/Modulos/Courier/Courier_Ajax.js"></script>
<script src="assets/js/Modulos/Courier/Courier_AUX.js"></script>

            </div>
        </div>
</body>

</html>