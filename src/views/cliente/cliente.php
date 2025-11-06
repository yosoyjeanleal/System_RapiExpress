<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>RapiExpress - Clientes</title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Librerías necesarias -->


</head>

<body>
    <?php include 'src\views\partels\barras.php'; ?>

    <div class="main-container">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="title">
                        <h4>Clientes</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="index.php?c=dashboard&a=index">RapiExpress</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Clientes
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-30">
                <h4 class="text-blue h4">Lista de Clientes</h4>
                <?php include 'src\views\partels\notificaciones.php'; ?>

                <div class="pull-right mb-2">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#clienteModal">
                        <i class="fa fa-user-plus"></i> Agregar Cliente
                    </button>
                </div>
            </div>

            <div class="pb-30">
                <table class="data-table table stripe hover nowrap" id="clientesTable">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Sucursal</th>
                            <th>Casillero</th>
                            <th>Nombre y Apellido</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Correo</th>
                            <th>Fecha Registro</th>
                            <th class="datatable-nosort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="clientesBody">
                        <?php foreach ($clientes as $cliente): ?>
                            <tr id="cliente-row-<?= $cliente['ID_Cliente'] ?>">
                                <td><?= htmlspecialchars($cliente['Cedula_Identidad']) ?></td>
                                <td><?= htmlspecialchars($cliente['Sucursal_Nombre'] ?? 'Sin sucursal') ?></td>
                                <td><?= htmlspecialchars($cliente['Casillero_Nombre'] ?? 'Sin casillero') ?></td>
                                <td><?= htmlspecialchars($cliente['Nombres_Cliente'] . ' ' . $cliente['Apellidos_Cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['Direccion_Cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['Telefono_Cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['Correo_Cliente']) ?></td>
                                <td><?= date('d/m/Y', strtotime($cliente['Fecha_Registro'])) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <!-- Botón Editar -->
                                        <a href="#"
                                            data-color="#265ed7"
                                            data-toggle="modal"
                                            data-target="#edit-cliente-modal-<?= $cliente['ID_Cliente'] ?>">
                                            <i class="icon-copy dw dw-edit2"></i>
                                        </a>

                                        <!-- Botón Eliminar -->
                                        <a href="#"
                                            data-color="#e95959"
                                            data-toggle="modal"
                                            data-target="#delete-cliente-modal"
                                            onclick="setDeleteId(<?= $cliente['ID_Cliente'] ?>)">
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
    </div>

    <!-- Modal Registrar Cliente -->
    <div class="modal fade" id="clienteModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <form id="formRegistrarCliente" action="index.php?c=cliente&a=registrar">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Nuevo Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal">×</button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Cédula</label>
                                <input type="text" class="form-control" name="Cedula_Identidad" pattern="\d{6,23}" required maxlength="23">
                            </div>
                            <div class="col-md-6">
                                <label>Sucursal</label>
                                <select class="form-control" name="ID_Sucursal" required>
                                    <option value="">Seleccione sucursal</option>
                                    <?php foreach ($sucursales as $sucursal): ?>
                                        <option value="<?= $sucursal['ID_Sucursal'] ?>"><?= htmlspecialchars($sucursal['Sucursal_Nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Casillero</label>
                                <select class="form-control" name="ID_Casillero" required>
                                    <option value="">Seleccione casillero</option>
                                    <?php foreach ($casilleros as $casillero): ?>
                                        <option value="<?= $casillero['ID_Casillero'] ?>"><?= htmlspecialchars($casillero['Casillero_Nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Nombres</label>
                                <input type="text" class="form-control" name="Nombres_Cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" required maxlength="20">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Apellidos</label>
                                <input type="text" class="form-control" name="Apellidos_Cliente" pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" required maxlength="20">
                            </div>
                            <div class="col-md-6">
                                <label>Correo Electrónico</label>
                                <input type="email" class="form-control" name="Correo_Cliente" required maxlength="100">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <label>Teléfono</label>
                                <input type="text" class="form-control" name="Telefono_Cliente" pattern="\d{7,15}" required maxlength="20">
                            </div>
                            <div class="col-md-6">
                                <label>Dirección</label>
                                <textarea class="form-control" name="Direccion_Cliente" rows="3" required maxlength="255"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <?php foreach ($clientes as $cli): ?>
        <div class="modal fade" id="edit-cliente-modal-<?= $cli['ID_Cliente'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form id="formEditarCliente-<?= $cli['ID_Cliente'] ?>" method="POST" action="index.php?c=cliente&a=editar">

                        <div class="modal-header">
                            <h4 class="modal-title">Editar Cliente</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="ID_Cliente" value="<?= htmlspecialchars($cli['ID_Cliente']) ?>">

                            <div class="row">
                                <div class="col-md-6">
                                    <label>Cédula de Identidad</label>
                                    <input type="text" class="form-control" name="Cedula_Identidad"
                                        value="<?= htmlspecialchars($cli['Cedula_Identidad']) ?>"
                                        pattern="\d{6,10}" title="La cédula debe tener entre 6 y 10 dígitos" required maxlength="23">
                                </div>
                                <div class="col-md-6">
                                    <label>Correo Electrónico</label>
                                    <input type="email" class="form-control" name="Correo_Cliente"
                                        value="<?= htmlspecialchars($cli['Correo_Cliente']) ?>"
                                        title="Ingresa un correo válido" required>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label>Nombres</label>
                                    <input type="text" class="form-control" name="Nombres_Cliente"
                                        value="<?= htmlspecialchars($cli['Nombres_Cliente']) ?>"
                                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios" required maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label>Apellidos</label>
                                    <input type="text" class="form-control" name="Apellidos_Cliente"
                                        value="<?= htmlspecialchars($cli['Apellidos_Cliente']) ?>"
                                        pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+" title="Solo letras y espacios" required maxlength="20">
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <label>Dirección</label>
                                    <textarea class="form-control" name="Direccion_Cliente" rows="3"
                                        maxlength="100"
                                        title="Máximo 100 caracteres. Se permiten letras, números y (,.-())"
                                        required maxlength="255"><?= htmlspecialchars($cli['Direccion_Cliente']) ?></textarea>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label>Teléfono</label>
                                    <input type="text" class="form-control" name="Telefono_Cliente"
                                        value="<?= htmlspecialchars($cli['Telefono_Cliente']) ?>"
                                        pattern="\d{7,15}" title="Solo dígitos (mínimo 7, máximo 15)" required maxlength="20">
                                </div>
                                <div class="col-md-6">
                                    <label>Sucursal</label>
                                    <select class="form-control" name="ID_Sucursal" required>
                                        <option value="">Seleccione sucursal</option>
                                        <?php foreach ($sucursales as $sucursal): ?>
                                            <option value="<?= $sucursal['ID_Sucursal'] ?>"
                                                <?= $sucursal['Sucursal_Nombre'] == $cli['Sucursal_Nombre'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($sucursal['Sucursal_Nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <label>Casillero</label>
                                    <select class="form-control" name="ID_Casillero" required>
                                        <option value="">Seleccione casillero</option>
                                        <?php foreach ($casilleros as $casillero): ?>
                                            <option value="<?= $casillero['ID_Casillero'] ?>"
                                                <?= $casillero['Casillero_Nombre'] == $cli['Casillero_Nombre'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($casillero['Casillero_Nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label>Fecha de Registro</label>
                                    <input type="text" class="form-control"
                                        value="<?= date('d/m/Y H:i', strtotime($cli['Fecha_Registro'])) ?>" readonly>
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


    <!-- Modal Eliminar Cliente Mejorado -->
    <div class="modal fade" id="delete-cliente-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body text-center p-5">
                    <!-- Icono de advertencia -->
                    <div class="mb-3">
                        <i class="fa fa-exclamation-triangle fa-4x text-danger"></i>
                    </div>
                    <h4 class="mb-3 fw-bold text-danger">¿Estás seguro?</h4>
                    <p class="text-muted mb-4">Esta acción eliminará al cliente de manera permanente y no se puede deshacer.</p>
                    <form id="formEliminarCliente" action="index.php?c=cliente&a=eliminar">
                        <input type="hidden" name="id" id="delete_cliente_id">
                        <div class="d-flex justify-content-center gap-3">
                            <button type="submit" class="btn btn-danger px-4">
                                <i class="fa fa-trash mr-1"></i> Sí, eliminar
                            </button>
                            <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets\js\Modulos\Clientes\Clientes_Ajax.js"></script>
    <script src="assets/js/Modulos/Clientes/Clientes_AUX.js"></script>

</body>

</html>