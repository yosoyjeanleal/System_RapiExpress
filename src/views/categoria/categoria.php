<?php
use RapiExpress\Helpers\Lang;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>RapiExpress - <?= Lang::get('categorias_title'); ?></title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
</head>
<body>
<?php include 'src/views/partels/barras.php'; ?>

<div class="mobile-menu-overlay"></div>

<div class="main-container">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="title">
                    <h4><?= Lang::get('categorias_title'); ?></h4>
                </div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.php?c=dashboard"><?= Lang::get('breadcrumb_home'); ?></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?= Lang::get('categorias_title'); ?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card-box mb-30">
        <div class="pd-30">
            <h4 class="text-blue h4"><?= Lang::get('categorias_list_title'); ?></h4>
            <?php include 'src/views/partels/notificaciones.php'; ?>
            <div class="pull-right">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#categoriaModal">
                    <i class="fa fa-plus"></i> <?= Lang::get('add_categoria'); ?>
                </button>
            </div>
        </div>

        <div class="pb-30">
            <table id="categoriasTable" class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th><?= Lang::get('name'); ?></th>
                        <th><?= Lang::get('height'); ?> x <?= Lang::get('length'); ?> x <?= Lang::get('width'); ?></th>
                        <th><?= Lang::get('weight'); ?></th>
                        <th><?= Lang::get('pieces'); ?></th>
                        <th><?= Lang::get('price'); ?></th>
                        <th class="datatable-nosort"><?= Lang::get('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categorias as $categoria): ?>
                        <tr>                            
                            <td><?= htmlspecialchars($categoria['Categoria_Nombre']) ?></td>
                            <td><?= "{$categoria['Categoria_Altura']} x {$categoria['Categoria_Largo']} x {$categoria['Categoria_Ancho']}" ?></td>
                            <td><?= $categoria['Categoria_Peso'] ?></td>
                            <td><?= $categoria['Categoria_Piezas'] ?></td>
                            <td>$<?= number_format($categoria['Categoria_Precio'], 2) ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="#"
                                        data-color="#265ed7"
                                        data-toggle="modal"
                                        data-target="#edit-categoria-modal-<?= $categoria['ID_Categoria'] ?>">
                                        <i class="icon-copy dw dw-edit2"></i>
                                    </a>
                                    <a href="#"
                                        data-color="#e95959"
                                        data-toggle="modal"
                                        data-target="#delete-categoria-modal"
                                        onclick="setDeleteId(<?= $categoria['ID_Categoria'] ?>)">
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
    <div class="modal fade" id="categoriaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg">
            <form id="formRegistrarCategoria" method="POST" action="index.php?c=categoria&a=registrar" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= Lang::get('register_categoria_modal_title'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body row">
                    <div class="col-md-6">
                        <label><?= Lang::get('name'); ?></label>
                        <input type="text" class="form-control" name="nombre" required>
                    </div>
                    <div class="col-md-6">
                        <label><?= Lang::get('price'); ?> ($)</label>
                        <input type="number" class="form-control" name="precio" required min="0" step="0.01">
                    </div>
                    <div class="col-md-3">
                        <label><?= Lang::get('height'); ?> (cm)</label>
                        <input type="number" class="form-control" name="altura" required min="0" step="0.01">
                    </div>
                    <div class="col-md-3">
                        <label><?= Lang::get('length'); ?> (cm)</label>
                        <input type="number" class="form-control" name="largo" required min="0" step="0.01">
                    </div>
                    <div class="col-md-3">
                        <label><?= Lang::get('width'); ?> (cm)</label>
                        <input type="number" class="form-control" name="ancho" required min="0" step="0.01">
                    </div>
                    <div class="col-md-3">
                        <label><?= Lang::get('weight'); ?> (kg)</label>
                        <input type="number" class="form-control" name="peso" required min="0" step="0.01">
                    </div>
                    <div class="col-md-3">
                        <label><?= Lang::get('pieces'); ?></label>
                        <input type="number" class="form-control" name="piezas" required min="1" step="1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('cancel'); ?></button>
                    <button class="btn btn-primary" type="submit"><?= Lang::get('register_cargo'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modales Editar -->
    <?php foreach ($categorias as $categoria): ?>
        <div class="modal fade" id="edit-categoria-modal-<?= $categoria['ID_Categoria'] ?>" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form method="POST" action="index.php?c=categoria&a=editar" class="modal-content formEditarCategoria">
                    <div class="modal-header">
                        <h4 class="modal-title"><?= Lang::get('edit_categoria_modal_title'); ?></h4>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body row">
                        <input type="hidden" name="ID_Categoria" value="<?= $categoria['ID_Categoria'] ?>">
                        <div class="col-md-6">
                            <label><?= Lang::get('name'); ?></label>
                            <input class="form-control" name="nombre" value="<?= htmlspecialchars($categoria['Categoria_Nombre']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label><?= Lang::get('price'); ?></label>
                            <input class="form-control" name="precio" type="number" step="0.01" value="<?= $categoria['Categoria_Precio'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label><?= Lang::get('height'); ?></label>
                            <input class="form-control" name="altura" type="number" step="0.01" value="<?= $categoria['Categoria_Altura'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label><?= Lang::get('length'); ?></label>
                            <input class="form-control" name="largo" type="number" step="0.01" value="<?= $categoria['Categoria_Largo'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label><?= Lang::get('width'); ?></label>
                            <input class="form-control" name="ancho" type="number" step="0.01" value="<?= $categoria['Categoria_Ancho'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label><?= Lang::get('weight'); ?></label>
                            <input class="form-control" name="peso" type="number" step="0.01" value="<?= $categoria['Categoria_Peso'] ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label><?= Lang::get('pieces'); ?></label>
                            <input class="form-control" name="piezas" type="number" value="<?= $categoria['Categoria_Piezas'] ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('cancel'); ?></button>
                        <button class="btn btn-primary" type="submit"><?= Lang::get('save_changes'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Modal Eliminar -->
    <div class="modal fade" id="delete-categoria-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form id="formEliminarCategoria" method="POST" action="index.php?c=categoria&a=eliminar" class="modal-content text-center p-4">
                <div class="modal-body">
                    <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                    <h4 class="mb-20 font-weight-bold text-danger"><?= Lang::get('delete_categoria_modal_title'); ?></h4>
                    <p class="mb-30 text-muted"><?= Lang::get('delete_categoria_modal_text'); ?></p>
                    <input type="hidden" name="id" id="delete_categoria_id">
                    <div class="row justify-content-center gap-2">
                        <div class="col-6 px-1">
                            <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal"><?= Lang::get('no'); ?></button>
                        </div>
                        <div class="col-6 px-1">
                            <button type="submit" class="btn btn-danger btn-block"><?= Lang::get('yes'); ?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

<script src="assets\js\Modulos\Categorias\Categorias_Ajax.js"></script>
<script src="assets\js\Modulos\Categorias\Categorias_AUXt.js"></script>
</body>
</html>