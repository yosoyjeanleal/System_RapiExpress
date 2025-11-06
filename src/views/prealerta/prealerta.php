<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>RapiExpress - Prealertas</title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css">
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
</head>
<body>
<?php include 'src/views/partels/barras.php'; ?>
<div class="mobile-menu-overlay"></div>
<div class="main-container">
    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12">
                <div class="title"><h4>Prealertas</h4></div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php?c=dashboard&a=index">RapiExpress</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Prealertas</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card-box mb-30">
        <div class="pd-30">
            <h4 class="text-blue h4">Lista de Prealertas</h4>
            <?php include 'src/views/partels/notificaciones.php'; ?>
            <div class="pull-right">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#prealertaModal">
                    <i class="fa fa-plus"></i> Agregar Prealerta
                </button>
            </div>
        </div>
        <div class="pb-30">
            <table class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Tienda</th>
                        <th>Usuario</th>
                        <th>Casillero</th>
                        <th>Sucursal</th>
                        <th>Tracking</th>
                        <th>Piezas</th>
                        <th>Peso</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th class="datatable-nosort">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prealertas as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['Nombres_Cliente'].' '.$p['Apellidos_Cliente']) ?></td>
                        <td><?= htmlspecialchars($p['Tienda_Nombre'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['Nombres_Usuario'].' '.$p['Apellidos_Usuario']) ?></td>
                        <td><?= htmlspecialchars($p['Casillero_Nombre'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['Sucursal_Nombre'] ?? '-') ?></td>
                        <td><?= htmlspecialchars($p['Tracking_Tienda']) ?></td>
                        <td><?= htmlspecialchars($p['Prealerta_Piezas']) ?></td>
                        <td><?= htmlspecialchars($p['Prealerta_Peso']) ?></td>
                        <td><?= htmlspecialchars($p['Prealerta_Descripcion']) ?></td>
                        <td>
                            <?php
                                $badge = 'badge-secondary';
                                if ($p['Estado'] == 'Prealerta') $badge = 'badge-info';
                                if ($p['Estado'] == 'Consolidado') $badge = 'badge-success';
                            ?>
                            <span class="badge <?= $badge ?>"><?= htmlspecialchars($p['Estado']) ?></span>
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="#" data-color="#265ed7" data-toggle="modal" 
                                   data-target="#edit-prealerta-<?= $p['ID_Prealerta'] ?>">
                                    <i class="icon-copy dw dw-edit2"></i>
                                </a>
                                <a href="#" data-color="#e95959" data-toggle="modal" 
                                   data-target="#delete-prealerta-modal"
                                   onclick="document.getElementById('delete_prealerta_id').value = <?= $p['ID_Prealerta'] ?>;">
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

<!-- Modal Registrar Prealerta -->
<div class="modal fade" id="prealertaModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="POST" action="#" id="form-registrar-prealerta">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Nueva Prealerta</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Cliente</label>
                                <select class="form-control" name="ID_Cliente" required>
                                    <option value="">Seleccione cliente</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <option value="<?= $c['ID_Cliente'] ?>">
                                            <?= htmlspecialchars($c['Nombres_Cliente'].' '.$c['Apellidos_Cliente']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tienda</label>
                                <select class="form-control" name="ID_Tienda" required>
                                    <option value="">Seleccione tienda</option>
                                    <?php foreach ($tiendas as $t): ?>
                                        <option value="<?= $t['ID_Tienda'] ?>">
                                            <?= htmlspecialchars($t['Tienda_Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Casillero</label>
                                <select class="form-control" name="ID_Casillero" required>
                                    <option value="">Seleccione casillero</option>
                                    <?php foreach ($casilleros as $cs): ?>
                                        <option value="<?= $cs['ID_Casillero'] ?>">
                                            <?= htmlspecialchars($cs['Casillero_Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Sucursal</label>
                                <select class="form-control" name="ID_Sucursal" required>
                                    <option value="">Seleccione sucursal</option>
                                    <?php foreach ($sucursales as $s): ?>
                                        <option value="<?= $s['ID_Sucursal'] ?>">
                                            <?= htmlspecialchars($s['Sucursal_Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tracking Tienda</label>
                                <input type="text" class="form-control" name="Tracking_Tienda" maxlength="50" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Piezas</label>
                                <input type="number" class="form-control" name="Prealerta_Piezas" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Peso (kg)</label>
                                <input type="number" step="0.01" class="form-control" name="Prealerta_Peso" min="0.01" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea class="form-control" name="Prealerta_Descripcion" rows="3" maxlength="255"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar Prealerta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modales Editar Prealerta -->
<?php foreach ($prealertas as $p): ?>
<div class="modal fade" id="edit-prealerta-<?= $p['ID_Prealerta'] ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="#" class="form-editar-prealerta" data-id="<?= $p['ID_Prealerta'] ?>">
                <div class="modal-header">
                    <h4 class="modal-title">Editar Prealerta</h4>
                    <button type="button" class="close" data-dismiss="modal">×</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="ID_Prealerta" value="<?= htmlspecialchars($p['ID_Prealerta']) ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <label>Cliente</label>
                            <select class="form-control campo-editable" name="ID_Cliente" required>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['ID_Cliente'] ?>" <?= $c['ID_Cliente'] == $p['ID_Cliente'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['Nombres_Cliente'].' '.$c['Apellidos_Cliente']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tienda</label>
                            <select class="form-control campo-editable" name="ID_Tienda" required>
                                <?php foreach ($tiendas as $t): ?>
                                    <option value="<?= $t['ID_Tienda'] ?>" <?= $t['ID_Tienda'] == $p['ID_Tienda'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($t['Tienda_Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-6">
                            <label>Casillero</label>
                            <select class="form-control campo-editable" name="ID_Casillero" required>
                                <?php foreach ($casilleros as $cs): ?>
                                    <option value="<?= $cs['ID_Casillero'] ?>" <?= $cs['ID_Casillero'] == $p['ID_Casillero'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cs['Casillero_Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Sucursal</label>
                            <select class="form-control campo-editable" name="ID_Sucursal" required>
                                <?php foreach ($sucursales as $s): ?>
                                    <option value="<?= $s['ID_Sucursal'] ?>" <?= $s['ID_Sucursal'] == $p['ID_Sucursal'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['Sucursal_Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-4">
                            <label>Tracking</label>
                            <input type="text" class="form-control campo-editable" name="Tracking_Tienda"
                                   value="<?= htmlspecialchars($p['Tracking_Tienda']) ?>" maxlength="50" required>
                        </div>
                        <div class="col-md-4">
                            <label>Piezas</label>
                            <input type="number" class="form-control campo-editable" name="Prealerta_Piezas"
                                   value="<?= htmlspecialchars($p['Prealerta_Piezas']) ?>" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label>Peso (kg)</label>
                            <input type="number" step="0.01" class="form-control campo-editable" name="Prealerta_Peso"
                                   value="<?= htmlspecialchars($p['Prealerta_Peso']) ?>" min="0.01" required>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-md-12">
                            <label>Descripción</label>
                            <textarea class="form-control campo-editable" name="Prealerta_Descripcion" rows="3" maxlength="255"><?= htmlspecialchars($p['Prealerta_Descripcion']) ?></textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Estado</label>
                            <select class="form-control campo-editable estado-select" name="Estado" required>
                                <option value="Prealerta" <?= $p['Estado'] == 'Prealerta' ? 'selected' : '' ?>>Prealerta</option>
                                <option value="Consolidado" <?= $p['Estado'] == 'Consolidado' ? 'selected' : '' ?>>Consolidado</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Fecha de Registro</label>
                            <input type="text" class="form-control" value="<?= date('d/m/Y H:i', strtotime($p['Fecha_Registro'])) ?>" readonly>
                        </div>
                    </div>

                    <!-- ✅ CAMPOS PARA CONSOLIDACIÓN (DENTRO DEL MODAL-BODY) -->
                    <div class="row mt-3 camposConsolidacion" style="display:none;">
                        <div class="col-md-6">
                            <label>Categoría <span class="text-danger">*</span></label>
                            <select class="form-control" name="ID_Categoria">
                                <option value="">Seleccione categoría...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['ID_Categoria'] ?>">
                                        <?= htmlspecialchars($cat['Categoria_Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Courier <span class="text-danger">*</span></label>
                            <select class="form-control" name="ID_Courier">
                                <option value="">Seleccione courier...</option>
                                <?php foreach ($couriers as $co): ?>
                                    <option value="<?= $co['ID_Courier'] ?>">
                                        <?= htmlspecialchars($co['Courier_Nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<!-- Modal Eliminar Prealerta -->
<div class="modal fade" id="delete-prealerta-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content text-center p-4">
            <div class="modal-body">
                <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                <h4 class="mb-20 font-weight-bold text-danger">¿Eliminar Prealerta?</h4>
                <p class="mb-30 text-muted">Esta acción no se puede deshacer.</p>
                <form method="POST" action="#" id="form-eliminar-prealerta">
                    <input type="hidden" name="delete_prealerta_id" id="delete_prealerta_id">
                    <div class="row justify-content-center gap-2" style="max-width: 200px; margin: 0 auto;">
                        <div class="col-6 px-1">
                            <button type="button" class="btn btn-secondary btn-lg btn-block border-radius-100" data-dismiss="modal">
                                <i class="fa fa-times"></i> No
                            </button>
                        </div>
                        <div class="col-6 px-1">
                            <button type="submit" class="btn btn-danger btn-lg btn-block border-radius-100">
                                <i class="fa fa-check"></i> Sí
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="assets\js\Modulos\Prealerta\Prealerta_Ajax.js"></script>
    <script src="assets\js\Modulos\Prealerta\Prealerta_AUX.js"></script>


</div>
</body>
</html>