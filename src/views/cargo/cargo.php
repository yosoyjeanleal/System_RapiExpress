<?php

use RapiExpress\Helpers\Lang;
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">

<head>
    <meta charset="utf-8" />
    <title>RapiExpress - <?= Lang::get('cargos_title') ?></title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>

<body>
    <?php include 'src/views/partels/barras.php'; ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">

            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4><?= Lang::get('cargos_title') ?></h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php?c=dashboard&a=index"><?= Lang::get('breadcrumb_home') ?></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><?= Lang::get('cargos_title') ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>



            <!-- Tabla -->
            <div class="card-box mb-30">
                <div class="pd-30">
                    <h4 class="text-blue h4"><?= Lang::get('cargos_list') ?></h4>


                    <div class="pull-right">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#cargoModal">
                            <i class="fa fa-briefcase"></i> <?= Lang::get('add_cargo') ?>
                        </button>
                    </div>
                </div>

                <div class="pb-30">
                    <table class="data-table table stripe hover nowrap" id="cargosTable">
                        <thead>
                            <tr>
                                <th><?= Lang::get('cargo_name') ?></th>
                                <th class="datatable-nosort"><?= Lang::get('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cargos as $cargo): ?>
                                <tr>
                                    <td><?= htmlspecialchars($cargo['Cargo_Nombre']) ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <!-- Botón Editar -->
                                            <a href="#"
                                                data-color="#265ed7"
                                                data-toggle="modal"
                                                data-target="#edit-cargo-modal-<?= $cargo['ID_Cargo'] ?>">
                                                <i class="icon-copy dw dw-edit2"></i>
                                            </a>

                                            <!-- Botón Eliminar -->
                                            <a href="#"
                                                data-color="#e95959"
                                                data-toggle="modal"
                                                data-target="#delete-cargo-modal"
                                                onclick="setDeleteId(<?= $cargo['ID_Cargo'] ?>)">
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

            <!-- Modal Registrar -->
            <div class="modal fade" id="cargoModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form id="formRegistrarCargo" method="POST" action="index.php?c=cargo&a=registrar">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= Lang::get('register_cargo') ?></h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="form-group row">
                                    <label class="col-sm-3 col-form-label"><?= Lang::get('cargo_name') ?><span class="text-danger">*</span></label>
                                    <div class="col-sm-9">
                                        <input type="text"
                                            name="Cargo_Nombre"
                                            class="form-control"
                                            placeholder="Ej: Gerente, Contador, Operador..."
                                            required
                                            maxlength="20"
                                            pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                                            title="Solo letras y espacios. Máximo 20 caracteres.">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('cancel') ?></button>
                                <button type="submit" class="btn btn-primary"><?= Lang::get('register_cargo') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal Editar -->
            <?php foreach ($cargos as $cargo): ?>
                <div class="modal fade" id="edit-cargo-modal-<?= $cargo['ID_Cargo'] ?>" tabindex="-1" role="dialog">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <form id="formEditarCargo-<?= $cargo['ID_Cargo'] ?>" method="POST" action="index.php?c=cargo&a=editar">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?= Lang::get('edit_cargo') ?></h5>
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="ID_Cargo" value="<?= $cargo['ID_Cargo'] ?>">
                                    <div class="form-group row">
                                        <label class="col-sm-3 col-form-label"><?= Lang::get('cargo_name') ?></label>
                                        <div class="col-sm-9">
                                            <input type="text"
                                                name="Cargo_Nombre"
                                                class="form-control"
                                                placeholder="Ej: Gerente, Contador, Operador..."
                                                value="<?= htmlspecialchars($cargo['Cargo_Nombre']) ?>"
                                                required
                                                maxlength="20"
                                                pattern="^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$"
                                                title="Solo se permiten letras y espacios.">
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
            <div class="modal fade" id="delete-cargo-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                        <div class="modal-body">
                            <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                            <h4 class="mb-20 font-weight-bold text-danger"><?= Lang::get('delete_cargo') ?></h4>
                            <p class="mb-30 text-muted"><?= Lang::get('delete_cargo_confirm') ?></p>

                            <form id="formEliminarCargo" method="POST" action="index.php?c=cargo&a=eliminar">
                                <input type="hidden" name="delete_cargo_id" id="delete_cargo_id">
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


            <!-- Scripts -->
             <script src="assets/js/ajax_utils.js"></script>
            <script src="assets\js\Modulos\Cargos\Cargos_Ajax.js"></script>
            <script src="assets\js\Modulos\Cargos\Cargos_VaInput.js"></script>


        </div>
    </div>
</body>

</html>