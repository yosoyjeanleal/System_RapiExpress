<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>RapiExpress - Sacas</title>
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
                        <h4>Sacas</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php?c=dashboard">RapiExpress</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Sacas</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-30 d-flex justify-content-between align-items-center">
                <h4 class="text-blue h4">Lista de Sacas</h4>

                <div>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#sacaModal">
                        <i class="fa fa-plus"></i> Agregar Saca
                    </button>

                    <button id="btnDetalle" class="btn btn-info btn-sm" disabled>
                        <i class="fa fa-eye"></i> Ver Detalle
                    </button>

                    <button id="btnImprimirSaca" class="btn btn-success btn-sm" disabled>
                        <i class="fa fa-print"></i> Imprimir Etiqueta
                    </button>
                </div>
            </div>

            <!-- ✅ NOTIFICACIONES ESTILO SUCURSAL -->
            <?php if (isset($_SESSION['mensaje'])): ?>
                <?php 
                    $mensaje = $_SESSION['mensaje'];
                    $tipo = is_array($mensaje) ? $mensaje['tipo'] : 'info';
                    $texto = is_array($mensaje) ? $mensaje['texto'] : $mensaje;
                    
                    $iconos = [
                        'success' => 'fa-check-circle',
                        'error' => 'fa-times-circle',
                        'warning' => 'fa-exclamation-triangle',
                        'info' => 'fa-info-circle'
                    ];
                    
                    $colores = [
                        'success' => 'alert-success',
                        'error' => 'alert-danger',
                        'warning' => 'alert-warning',
                        'info' => 'alert-info'
                    ];
                    
                    $icono = $iconos[$tipo] ?? 'fa-info-circle';
                    $color = $colores[$tipo] ?? 'alert-info';
                ?>
                <div class="alert <?= $color ?> alert-dismissible fade show" role="alert">
                    <i class="fa <?= $icono ?>" style="margin-right: 8px;"></i>
                    <strong><?= ucfirst($tipo) ?>:</strong> <?= htmlspecialchars($texto) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <div class="pb-30">
                <table id="tablaSacas" class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th></th>
                            <th>ID</th>
                            <th>Código</th>
                            <th>Usuario</th>
                            <th>Sucursal</th>
                            <th>Estado</th>
                            <th>Peso Total (KG)</th>
                            <th class="datatable-nosort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sacas as $saca): ?>
                            <tr>
                                <td>
                                    <input type="radio" name="selectSaca" value="<?= $saca['ID_Saca'] ?>">
                                </td>
                                <td><?= $saca['ID_Saca'] ?></td>
                                <td><strong><?= htmlspecialchars($saca['Codigo_Saca']) ?></strong></td>
                                <td><?= htmlspecialchars($saca['Nombres_Usuario'] ?? 'Sin asignar') ?></td>
                                <td><?= htmlspecialchars($saca['Sucursal_Nombre'] ?? 'Sin asignar') ?></td>
                                <td>
                                    <?php 
                                    $badgeClass = match($saca['Estado']) {
                                        'Pendiente' => 'badge-warning',
                                        'En tránsito' => 'badge-info',
                                        'Entregada' => 'badge-success',
                                        default => 'badge-secondary'
                                    };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($saca['Estado']) ?></span>
                                </td>
                                <td><?= number_format($saca['Peso_Total'], 2) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="index.php?c=saca&a=generarQR&id=<?= $saca['ID_Saca'] ?>"
                                            class="btn btn-sm btn-primary"
                                            target="_blank"
                                            title="Ver QR">
                                            <i class="bi bi-qr-code"></i>
                                        </a>

                                        <a href="#"
                                            data-color="#265ed7"
                                            data-toggle="modal"
                                            data-target="#edit-saca-modal-<?= $saca['ID_Saca'] ?>"
                                            title="Editar">
                                            <i class="icon-copy dw dw-edit2"></i>
                                        </a>

                                        <form method="POST" action="index.php?c=saca&a=eliminar" style="display:inline-block; margin:0;">
                                            <input type="hidden" name="ID_Saca" value="<?= $saca['ID_Saca'] ?>">
                                            <button type="submit"
                                                class="btn btn-link p-0"
                                                style="color:#e95959;"
                                                title="Eliminar">
                                                <i class="icon-copy dw dw-delete-3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ✅ Modal Registrar Saca (CAMPOS AUTOMÁTICOS) -->
        <div class="modal fade" id="sacaModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <form method="POST" action="index.php?c=saca&a=registrar" id="formRegistrarSaca" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Nueva Saca</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-6">
                            <label>Código de Saca</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($codigoSaca) ?>" readonly>
                            <small class="form-text text-muted">Se genera automáticamente</small>
                        </div>
                        
                        <!-- ✅ Usuario automático (no editable) -->
                        <div class="col-md-6">
                            <label>Usuario Responsable</label>
                            <input type="text" class="form-control" 
                                   value="<?= htmlspecialchars($_SESSION['usuario']['Nombres_Usuario'] ?? '') . ' ' . htmlspecialchars($_SESSION['usuario']['Apellidos_Usuario'] ?? '') ?>" 
                                   readonly>
                            <small class="form-text text-muted">Usuario logueado</small>
                        </div>
                        
                        <div class="col-md-6 mt-2">
                            <label>Sucursal Destino <span class="text-danger">*</span></label>
                            <select name="ID_Sucursal" class="form-control" required>
                                <option value="">Seleccione sucursal</option>
                                <?php foreach ($sucursales as $sucursal): ?>
                                    <option value="<?= $sucursal['ID_Sucursal'] ?>"><?= htmlspecialchars($sucursal['Sucursal_Nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mt-2">
                            <label>Estado</label>
                            <select name="Estado" class="form-control">
                                <option value="Pendiente" selected>Pendiente</option>
                                <option value="En tránsito">En tránsito</option>
                                <option value="Entregada">Entregada</option>
                            </select>
                        </div>
                        
                        <!-- ✅ Peso total (readonly, se calcula automático) -->
                        <div class="col-md-12 mt-2">
                            <label>Peso Total (KG)</label>
                            <input type="number" step="0.01" class="form-control" value="0.00" readonly>
                            <small class="form-text text-muted">Se calculará automáticamente al agregar paquetes</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                        <button class="btn btn-primary" type="submit">Registrar Saca</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modales Editar -->
        <?php foreach ($sacas as $saca): ?>
            <div class="modal fade" id="edit-saca-modal-<?= $saca['ID_Saca'] ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <form method="POST" action="index.php?c=saca&a=editar" class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Saca</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body row">
                            <input type="hidden" name="ID_Saca" value="<?= $saca['ID_Saca'] ?>">

                            <div class="col-md-6">
                                <label>Código de Saca</label>
                                <input type="text" name="Codigo_Saca" class="form-control"
                                    value="<?= htmlspecialchars($saca['Codigo_Saca']) ?>" readonly>
                            </div>

                            <div class="col-md-6">
                                <label>Usuario Responsable</label>
                                <input type="text" class="form-control" 
                                       value="<?= htmlspecialchars($_SESSION['usuario']['Nombres_Usuario'] ?? '') . ' ' . htmlspecialchars($_SESSION['usuario']['Apellidos_Usuario'] ?? '') ?>" 
                                       readonly>
                                <small class="form-text text-muted">Usuario logueado</small>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Sucursal <span class="text-danger">*</span></label>
                                <select name="ID_Sucursal" class="form-control" required>
                                    <?php foreach ($sucursales as $sucursal): ?>
                                        <option value="<?= $sucursal['ID_Sucursal'] ?>"
                                            <?= $sucursal['ID_Sucursal'] == $saca['ID_Sucursal'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($sucursal['Sucursal_Nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Estado</label>
                                <select name="Estado" class="form-control">
                                    <option value="Pendiente" <?= $saca['Estado'] == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                                    <option value="En tránsito" <?= $saca['Estado'] == 'En tránsito' ? 'selected' : '' ?>>En tránsito</option>
                                    <option value="Entregada" <?= $saca['Estado'] == 'Entregada' ? 'selected' : '' ?>>Entregada</option>
                                </select>
                            </div>

                            <div class="col-md-6 mt-2">
                                <label>Peso Total (KG)</label>
                                <input type="number" step="0.01" name="Peso_Total" class="form-control"
                                    value="<?= $saca['Peso_Total'] ?>" readonly>
                                <small class="form-text text-muted">Calculado automáticamente</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                            <button class="btn btn-primary" type="submit">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Modal para vista previa de impresión -->
        <div class="modal fade" id="imprimirSacaModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vista Previa - Etiqueta de Saca</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Código:</strong> <span id="detalleSacaCodigo">-</span></p>
                                <p><strong>Usuario:</strong> <span id="detalleSacaUsuario">-</span></p>
                                <p><strong>Sucursal:</strong> <span id="detalleSacaSucursal">-</span></p>
                                <p><strong>Estado:</strong> <span id="detalleSacaEstado">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Peso Total:</strong> <span id="detalleSacaPeso">-</span> KG</p>
                                <p><strong>Cantidad Paquetes:</strong> <span id="detalleSacaCantidad">-</span></p>
                                <p><strong>Fecha:</strong> <span id="detalleSacaFecha">-</span></p>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <strong>Código QR:</strong>
                            <div id="detalleSacaQR" class="mt-2"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="imprimirSaca()">
                            <i class="fa fa-print"></i> Generar Etiqueta
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para mostrar etiqueta completa -->
        <div class="modal fade" id="modalEtiquetaSaca" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Etiqueta de Saca</h5>
                        <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body p-0">
                        <iframe id="etiquetaSacaFrame" style="width:100%; height:600px; border:none;"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-primary" onclick="imprimirEtiquetaSaca()">
                            <i class="fa fa-print"></i> Imprimir
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="assets/js/Modulos/saca_ajax.js"></script>

    <script>
        let selectedSaca = null;

        document.querySelectorAll('input[name="selectSaca"]').forEach(radio => {
            radio.addEventListener('change', function() {
                selectedSaca = this.value;
                document.getElementById('btnDetalle').disabled = false;
            });
        });

        document.getElementById('btnDetalle').addEventListener('click', function() {
            if (selectedSaca) {
                window.location.href = `index.php?c=detallesaca&a=index&id=${selectedSaca}`;
            }
        });
    </script>

</body>
</html>