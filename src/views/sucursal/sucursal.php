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
                                <h4><?= Lang::get('sucursales_title') ?></h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="index.php?c=dashboard&a=index"><?= Lang::get('breadcrumb_home') ?></a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        <?= Lang::get('sucursales_title') ?>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>



                <div class="card-box mb-30">
                    <div class="pd-30">
                        <h4 class="text-blue h4"><?= Lang::get('sucursales_list_title') ?></h4>
                        <?php include 'src/views/partels/notificaciones.php'; ?>

                        <div class="pull-right">
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sucursalModal">
                                <i class="fa fa-building"></i> <?= Lang::get('add_sucursal') ?>
                            </button>
                        </div>
                    </div>
                    <div class="pb-30">
                        <table class="data-table table stripe hover nowrap" id="sucursalesTable">
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
                                <?php foreach ($sucursales as $sucursal): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($sucursal['RIF_Sucursal']) ?></td>
                                        <td><?= htmlspecialchars($sucursal['Sucursal_Nombre']) ?></td>
                                        <td><?= htmlspecialchars($sucursal['Sucursal_Direccion']) ?></td>
                                        <td><?= htmlspecialchars($sucursal['Sucursal_Telefono']) ?></td>
                                        <td><?= htmlspecialchars($sucursal['Sucursal_Correo']) ?></td>
                                        <td>
                                            <div class="table-actions">
                                                <!-- Botón Editar -->
                                                <a href="#"
                                                    data-color="#265ed7"
                                                    data-toggle="modal"
                                                    data-target="#edit-sucursal-modal-<?= $sucursal['ID_Sucursal'] ?>"
                                                    title="<?= Lang::get('edit_sucursal_modal_title') ?>">
                                                    <i class="icon-copy dw dw-edit2"></i>
                                                </a>

                                                <!-- Botón Eliminar -->
                                                <a href="#"
                                                    data-color="#e95959"
                                                    data-toggle="modal"
                                                    data-target="#delete-sucursal-modal"
                                                    onclick="setDeleteId(<?= $sucursal['ID_Sucursal'] ?>)"
                                                    title="<?= Lang::get('delete_sucursal_modal_title') ?>">
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

                <!-- Modal Agregar Sucursal -->

                <div class="modal fade" id="sucursalModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form id="formRegistrarSucursal" method="POST" action="index.php?c=sucursal&a=registrar">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?= Lang::get('register_sucursal_modal_title') ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= Lang::get('close') ?>">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>

                                <div class="modal-body">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('rif') ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="RIF_Sucursal" pattern="^[JGVEP]-\d{8}-\d$" title="Formato válido: J-12345678-9" maxlength="23" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('name') ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="Sucursal_Nombre" pattern="^[a-zA-Z0-9\sáéíóúÁÉÍÓÚñÑ.,\-()_]+$" title="Solo letras, números y caracteres válidos (,.-())" maxlength="20" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('address') ?></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" name="Sucursal_Direccion" maxlength="100" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('phone') ?></label>
                                        <div class="col-sm-9">
                                            <input type="tel" class="form-control" name="Sucursal_Telefono" pattern="^\d{7,20}$" maxlength="20" required>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('email') ?></label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" name="Sucursal_Correo" maxlength="100" required>
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

                <?php foreach ($sucursales as $suc): ?>
                    <!-- Modal Ver Detalles -->
                    <div class="modal fade" id="view-sucursal-modal-<?= $suc['ID_Sucursal'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title"><?= Lang::get('view_sucursal_modal_title') ?></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="<?= Lang::get('close') ?>">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $campos = [
                                        Lang::get('rif') => $suc['RIF_Sucursal'],
                                        'ID' => $suc['ID_Sucursal'],
                                        Lang::get('name') => $suc['Sucursal_Nombre'],
                                        Lang::get('address') => $suc['Sucursal_Direccion'],
                                        Lang::get('phone') => $suc['Sucursal_Telefono'],
                                        Lang::get('email') => $suc['Sucursal_Correo']
                                    ];
                                    foreach ($campos as $label => $valor):
                                    ?>
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= $label ?></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" value="<?= htmlspecialchars($valor) ?>" readonly>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('close') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Editar -->


                    <div class="modal fade" id="edit-sucursal-modal-<?= $suc['ID_Sucursal'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <form id="formEditarSucursal-<?= $suc['ID_Sucursal'] ?>" method="POST" action="index.php?c=sucursal&a=editar">
                                    <div class="modal-header">
                                        <h4 class="modal-title"><?= Lang::get('edit_sucursal_modal_title') ?></h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="<?= Lang::get('close') ?>">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <input type="hidden" name="ID_Sucursal" value="<?= $suc['ID_Sucursal'] ?>">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('name') ?></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="Sucursal_Nombre"
                                                    value="<?= htmlspecialchars($suc['Sucursal_Nombre']) ?>"
                                                    maxlength="20" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('rif') ?></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="RIF_Sucursal"
                                                    value="<?= htmlspecialchars($suc['RIF_Sucursal']) ?>"
                                                    maxlength="23" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('address') ?></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="Sucursal_Direccion"
                                                    value="<?= htmlspecialchars($suc['Sucursal_Direccion']) ?>"
                                                    maxlength="100" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('phone') ?></label>
                                            <div class="col-sm-9">
                                                <input type="tel" class="form-control" name="Sucursal_Telefono"
                                                    value="<?= htmlspecialchars($suc['Sucursal_Telefono']) ?>"
                                                    maxlength="20" required>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label"><?= Lang::get('email') ?></label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" name="Sucursal_Correo"
                                                    value="<?= htmlspecialchars($suc['Sucursal_Correo']) ?>"
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
                <div class="modal fade" id="delete-sucursal-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content text-center p-4">
                            <div class="modal-body">
                                <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                                <h4 class="mb-20 font-weight-bold text-danger"><?= Lang::get('delete_sucursal_modal_title') ?></h4>
                                <p class="mb-30 text-muted"><?= Lang::get('delete_sucursal_modal_text') ?></p>

                                <form id="formEliminarSucursal" method="POST" action="index.php?c=sucursal&a=eliminar">
                                    <input type="hidden" name="delete_sucursal_id" id="delete_sucursal_id">
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

   

<script src="assets/js/Modulos/Sucursales/Sucursales_Ajax.js"></script>
<script src="assets/js/Modulos/Sucursales/Sucursales_AUX.js"></script>



            </div>
        </div>
</body>

</html>