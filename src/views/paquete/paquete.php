<?php

use RapiExpress\Models\Paquete;

$model = new Paquete();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>RapiExpress - Paquetes</title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    
</head>

<body>
    <?php include 'src/views/partels/barras.php'; ?>
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12">
                    <div class="title">
                        <h4>Paquetes</h4>
                    </div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php?c=dashboard&a=index">RapiExpress</a></li>
                            <li class="breadcrumb-item active">Paquetes</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-30">
                <h4 class="text-blue h4">Lista de Paquetes</h4>
                <?php include 'src/views/partels/notificaciones.php'; ?>
                <div class="pull-right">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#paqueteModal">
                        <i class="fa fa-plus"></i> Agregar Paquete
                    </button>
                    <button class="btn btn-success btn-sm" id="btnImprimirSeleccionado" disabled>
                        <i class="fa fa-print"></i> Imprimir Paquete
                    </button>
                </div>
            </div>
            <div class="pb-30">
                <table class="data-table table stripe hover nowrap" id="paquetesTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>Tracking</th>
                            <th>Cliente</th>
                            <th>Nombre Instrumento</th>
                            <th>Categoría</th>
                            <th>Sucursal</th>
                            <th>Courier</th>
                            <th>Descripción</th>
                            <th>Piezas</th>
                            <th>Peso</th>
                            <th>QR</th>
                            <th>Estado</th>
                            <th class="datatable-nosort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paquetes as $p): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="paquete-check"
                                        data-tracking="<?= htmlspecialchars($p['Tracking']) ?>"
                                        data-cliente="<?= htmlspecialchars($p['Nombres_Cliente'] . ' ' . $p['Apellidos_Cliente']) ?>"
                                        data-instrumento="<?= htmlspecialchars($p['Nombre_Instrumento'] ?? '-') ?>"
                                        data-categoria="<?= htmlspecialchars($p['Categoria_Nombre'] ?? '-') ?>"
                                        data-sucursal="<?= htmlspecialchars($p['Sucursal_Nombre'] ?? '-') ?>"
                                        data-courier="<?= htmlspecialchars($p['Courier_Nombre'] ?? '-') ?>"
                                        data-descripcion="<?= htmlspecialchars($p['Prealerta_Descripcion']) ?>"
                                        data-piezas="<?= htmlspecialchars($p['Paquete_Piezas'] ?? 1) ?>"
                                        data-peso="<?= htmlspecialchars($p['Paquete_Peso'] ?? '-') ?>"
                                        data-estado="<?= htmlspecialchars($p['Estado']) ?>"
                                        data-qr="<?= htmlspecialchars($p['Qr_code']) ?>">
                                </td>
                                <td><?= htmlspecialchars($p['Tracking']) ?></td>
                                <td><?= htmlspecialchars($p['Nombres_Cliente'] . ' ' . $p['Apellidos_Cliente']) ?></td>
                                <td><?= htmlspecialchars($p['Nombre_Instrumento'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($p['Categoria_Nombre'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($p['Sucursal_Nombre'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($p['Courier_Nombre'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($p['Prealerta_Descripcion']) ?></td>
                                <td><?= htmlspecialchars($p['Paquete_Piezas'] ?? 1) ?></td>
                                <td><?= htmlspecialchars($p['Paquete_Peso'] ?? '-') ?> Kg</td>
                                <td>
                                    <?php if (!empty($p['Qr_code'])): ?>
                                        <img src="<?= 'src/storage/qr/' . htmlspecialchars($p['Qr_code']) ?>" width="60" alt="QR">
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($p['Estado']) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <!-- Editar -->
                                        <a href="#" data-color="#265ed7" data-toggle="modal"
                                            data-target="#edit-paquete-<?= $p['ID_Paquete'] ?>">
                                            <i class="icon-copy dw dw-edit2"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        <a href="#" data-color="#e95959" data-toggle="modal"
                                            data-target="#delete-paquete-modal"
                                            onclick="document.getElementById('delete_paquete_id').value = <?= $p['ID_Paquete'] ?>;">
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

        <!-- Modal Registrar Paquete -->
        <div class="modal fade" id="paqueteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <form method="POST" action="index.php?c=paquete&a=registrar" class="modal-content" id="formRegistrarPaquete">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Paquete</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tracking</label>
                            <input type="text" name="Tracking" class="form-control" value="<?= $model->generarTracking() ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Cliente <span class="text-danger">*</span></label>
                            <select name="ID_Cliente" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['ID_Cliente'] ?>">
                                        <?= htmlspecialchars($c['Nombres_Cliente'] . ' ' . $c['Apellidos_Cliente']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nombre del Instrumento</label>
                            <input type="text" name="Nombre_Instrumento" class="form-control" placeholder="Ingrese el nombre del instrumento">
                        </div>
                        <div class="form-group">
                            <label>Categoría <span class="text-danger">*</span></label>
                            <select name="ID_Categoria" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['ID_Categoria'] ?>"><?= htmlspecialchars($cat['Categoria_Nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Sucursal <span class="text-danger">*</span></label>
                            <select name="ID_Sucursal" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($sucursales as $s): ?>
                                    <option value="<?= $s['ID_Sucursal'] ?>"><?= htmlspecialchars($s['Sucursal_Nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Courier <span class="text-danger">*</span></label>
                            <select name="ID_Courier" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($couriers as $co): ?>
                                    <option value="<?= $co['ID_Courier'] ?>"><?= htmlspecialchars($co['Courier_Nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Piezas <span class="text-danger">*</span></label>
                                    <input type="number" name="Paquete_Piezas" class="form-control" value="1" min="1" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Peso (Kg) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="Paquete_Peso" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="Prealerta_Descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="Estado" class="form-control">
                                <option value="En tránsito">En tránsito</option>
                                <option value="Entregado">Entregado</option>
                                <option value="Fallido">Fallido</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modales Editar Paquete -->
        <?php foreach ($paquetes as $p): ?>
            <div class="modal fade" id="edit-paquete-<?= $p['ID_Paquete'] ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <form method="POST" action="index.php?c=paquete&a=editar" class="modal-content" id="formEditarPaquete-<?= $p['ID_Paquete'] ?>">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Paquete</h5>
                            <button type="button" class="close" data-dismiss="modal">×</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="ID_Paquete" value="<?= $p['ID_Paquete'] ?>">
                            <div class="form-group">
                                <label>Tracking</label>
                                <input type="text" name="Tracking" class="form-control" value="<?= htmlspecialchars($p['Tracking']) ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label>Cliente <span class="text-danger">*</span></label>
                                <select name="ID_Cliente" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($clientes as $c): ?>
                                        <option value="<?= $c['ID_Cliente'] ?>" <?= $c['ID_Cliente'] == $p['ID_Cliente'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['Nombres_Cliente'] . ' ' . $c['Apellidos_Cliente']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nombre del Instrumento</label>
                                <input type="text" name="Nombre_Instrumento" class="form-control" value="<?= htmlspecialchars($p['Nombre_Instrumento'] ?? '') ?>" placeholder="Ingrese el nombre del instrumento">
                            </div>
                            <div class="form-group">
                                <label>Categoría <span class="text-danger">*</span></label>
                                <select name="ID_Categoria" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['ID_Categoria'] ?>" <?= $cat['ID_Categoria'] == $p['ID_Categoria'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['Categoria_Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Sucursal <span class="text-danger">*</span></label>
                                <select name="ID_Sucursal" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($sucursales as $s): ?>
                                        <option value="<?= $s['ID_Sucursal'] ?>" <?= $s['ID_Sucursal'] == $p['ID_Sucursal'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($s['Sucursal_Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Courier <span class="text-danger">*</span></label>
                                <select name="ID_Courier" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    <?php foreach ($couriers as $co): ?>
                                        <option value="<?= $co['ID_Courier'] ?>" <?= $co['ID_Courier'] == $p['ID_Courier'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($co['Courier_Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Piezas <span class="text-danger">*</span></label>
                                        <input type="number" name="Paquete_Piezas" class="form-control" value="<?= htmlspecialchars($p['Paquete_Piezas'] ?? 1) ?>" min="1" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Peso (Kg) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" name="Paquete_Peso" class="form-control" value="<?= htmlspecialchars($p['Paquete_Peso'] ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Descripción</label>
                                <textarea name="Prealerta_Descripcion" class="form-control" rows="3"><?= htmlspecialchars($p['Prealerta_Descripcion']) ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Estado</label>
                                <select name="Estado" class="form-control">
                                    <option value="En tránsito" <?= $p['Estado'] == 'En tránsito' ? 'selected' : '' ?>>En tránsito</option>
                                    <option value="Entregado" <?= $p['Estado'] == 'Entregado' ? 'selected' : '' ?>>Entregado</option>
                                    <option value="Fallido" <?= $p['Estado'] == 'Fallido' ? 'selected' : '' ?>>Fallido</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Modal Eliminar -->
        <div class="modal fade" id="delete-paquete-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <form method="POST" action="index.php?c=paquete&a=eliminar" class="modal-content text-center p-4" id="formEliminarPaquete">
                    <div class="modal-body">
                        <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                        <h4 class="mb-20 font-weight-bold text-danger">¿Eliminar Paquete?</h4>
                        <input type="hidden" name="ID_Paquete" id="delete_paquete_id">
                        <p class="mb-30 text-muted">Esta acción no se puede deshacer.</p>
                        <div class="row justify-content-center gap-2">
                            <div class="col-6 px-1">
                                <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">No</button>
                            </div>
                            <div class="col-6 px-1">
                                <button type="submit" class="btn btn-danger btn-block">Sí</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Imprimir -->
        <div class="modal fade" id="imprimirPaqueteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detalles del Paquete</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body" id="paqueteDetalle">
                        <p><b>Tracking:</b> <span id="detalleTracking"></span></p>
                        <p><b>Cliente:</b> <span id="detalleCliente"></span></p>
                        <p><b>Instrumento:</b> <span id="detalleInstrumento"></span></p>
                        <p><b>Categoría:</b> <span id="detalleCategoria"></span></p>
                        <p><b>Sucursal:</b> <span id="detalleSucursal"></span></p>
                        <p><b>Courier:</b> <span id="detalleCourier"></span></p>
                        <p><b>Piezas:</b> <span id="detallePiezas"></span></p>
                        <p><b>Peso:</b> <span id="detallePeso"></span> Kg</p>
                        <p><b>Descripción:</b> <span id="detalleDescripcion"></span></p>
                        <div id="detalleQR"></div>
                    </div>
                    <div class="modal-footer no-print">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" onclick="imprimirPaquete()">Imprimir</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Etiqueta -->
        <div class="modal fade" id="modalEtiqueta" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document" style="max-width: 11cm;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Etiqueta del Paquete</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="etiquetaFrame" src="" style="width:100%; height:16cm; border:none;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-success" onclick="imprimirEtiqueta()">Imprimir</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <script src="assets\js\Modulos\Paquetes\Paquetes_Ajax.js"></script>
<script src="assets/js/Modulos/Paquetes/Paquetes_AUX.js"></script>

</body>

</html>