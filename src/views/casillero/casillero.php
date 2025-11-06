<?php
use RapiExpress\Helpers\Lang;
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">

<head>
    <meta charset="utf-8" />
    <title>RapiExpress - <?= Lang::get('casilleros_title') ?></title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>

<body>
    <?php include 'src/views/partels/barras.php'; ?>

    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">

            <!-- Header -->
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4><?= Lang::get('casilleros_title') ?></h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="index.php?c=dashboard&a=index"><?= Lang::get('breadcrumb_home') ?></a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page"><?= Lang::get('casilleros_title') ?></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Card principal -->
            <div class="card-box mb-30">
                <div class="pd-30">
                    <h4 class="text-blue h4"><?= Lang::get('casilleros_list_title') ?></h4>
                    <?php include 'src/views/partels/notificaciones.php'; ?>
                    <div class="pull-right">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#casilleroModal">
                            <i class="fa fa-plus"></i> <?= Lang::get('add_casillero') ?>
                        </button>
                    </div>
                </div>

                <div class="pb-30">
                    <table class="data-table table stripe hover nowrap" id="casillerosTable">
                        <thead>
                            <tr>
                                <th><?= Lang::get('name') ?></th>
                                <th><?= Lang::get('address') ?></th>
                                <th class="datatable-nosort"><?= Lang::get('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($casilleros as $casillero): ?>
                                <tr>
                                    <td><?= htmlspecialchars($casillero['Casillero_Nombre']) ?></td>
                                    <td><?= htmlspecialchars($casillero['Direccion']) ?></td>
                                    <td>
                                        <div class="table-actions">
                                            <!-- Botón Editar -->
                                            <a href="#"
                                                data-color="#265ed7"
                                                data-toggle="modal"
                                                data-target="#edit-casillero-modal-<?= $casillero['ID_Casillero'] ?>"
                                                title="<?= Lang::get('edit_casillero_modal_title') ?>">
                                                <i class="icon-copy dw dw-edit2"></i>
                                            </a>

                                            <!-- Botón Eliminar -->
                                            <a href="#"
                                                data-color="#e95959"
                                                data-toggle="modal"
                                                data-target="#delete-casillero-modal"
                                                onclick="setDeleteId(<?= $casillero['ID_Casillero'] ?>)"
                                                title="<?= Lang::get('delete_casillero_modal_title') ?>">
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

            <!-- Modal Registrar Casillero -->
            <div class="modal fade" id="casilleroModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form id="formRegistrarCasillero" class="modal-content" method="POST" action="index.php?c=casillero&a=registrar">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= Lang::get('register_casillero_modal_title') ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="<?= Lang::get('close') ?>">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-md-6 form-group">
                                <label><?= Lang::get('name') ?> <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="Casillero_Nombre"
                                    class="form-control"
                                    placeholder="<?= Lang::get('casillero_name_placeholder') ?>"
                                    required
                                    maxlength="50"
                                    autocomplete="off">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6 form-group">
                                <label><?= Lang::get('address') ?> <span class="text-danger">*</span></label>
                                <input type="text"
                                    name="Direccion"
                                    class="form-control"
                                    placeholder="<?= Lang::get('casillero_address_placeholder') ?>"
                                    required
                                    maxlength="100"
                                    autocomplete="off">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('cancel') ?></button>
                            <button type="submit" class="btn btn-primary"><?= Lang::get('register') ?></button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Modal Editar Casillero -->
            <?php foreach ($casilleros as $casillero): ?>
                <div class="modal fade" id="edit-casillero-modal-<?= $casillero['ID_Casillero'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <form class="modal-content formEditarCasillero" method="POST" action="index.php?c=casillero&a=editar">
                            <div class="modal-header">
                                <h5 class="modal-title"><?= Lang::get('edit_casillero_modal_title') ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="<?= Lang::get('close') ?>">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body row">
                                <input type="hidden" name="ID_Casillero" value="<?= $casillero['ID_Casillero'] ?>">
                                <div class="col-md-6 form-group">
                                    <label><?= Lang::get('name') ?> <span class="text-danger">*</span></label>
                                    <input type="text"
                                        name="Casillero_Nombre"
                                        class="form-control"
                                        placeholder="<?= Lang::get('casillero_name_placeholder') ?>"
                                        value="<?= htmlspecialchars($casillero['Casillero_Nombre']) ?>"
                                        required
                                        maxlength="50"
                                        autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label><?= Lang::get('address') ?> <span class="text-danger">*</span></label>
                                    <input type="text"
                                        name="Direccion"
                                        class="form-control"
                                        placeholder="<?= Lang::get('casillero_address_placeholder') ?>"
                                        value="<?= htmlspecialchars($casillero['Direccion']) ?>"
                                        required
                                        maxlength="100"
                                        autocomplete="off">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('cancel') ?></button>
                                <button type="submit" class="btn btn-primary"><?= Lang::get('save_changes') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Modal Eliminar -->
            <div class="modal fade" id="delete-casillero-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-center p-4">
                        <div class="modal-body">
                            <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                            <h4 class="mb-20 font-weight-bold text-danger"><?= Lang::get('delete_casillero_modal_title') ?></h4>
                            <p class="mb-30 text-muted"><?= Lang::get('delete_casillero_modal_text') ?></p>
                            <form id="formEliminarCasillero" method="POST" action="index.php?c=casillero&a=eliminar">
                                <input type="hidden" name="ID_Casillero" id="delete_casillero_id">
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

        </div>
    </div>

    <script src="assets/js/ajax_utils.js"></script>
    <script src="assets/js\Modulos/Casilleros/Casilleros_Ajax.js"></script>
     <script src="assets/js/Modulos/Casilleros/Casilleros_AUX.js"></script>

</body>

</html>