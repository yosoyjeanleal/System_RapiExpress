<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>RapiExpress</title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <?php include 'src/views/partels/barras.php'; ?>
</head>

<body>
    <div class="mobile-menu-overlay"></div>

    <div class="main-container">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="title">
                        <h4>Empleados</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="index.php?c=dashboard&a=index">RapiExpress</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Empleados
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <div class="card-box mb-30">
            <div class="pd-30">
                <h4 class="text-blue h4">Gestión de Usuarios</h4>
                <?php include 'src/views/partels/notificaciones.php'; ?>
                <div class="pull-right">
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#usuarioModal">
                        <i class="fa fa-user-plus"></i> Agregar Usuario
                    </button>
                </div>
            </div>

            <div class="pb-30">
                <table class="data-table table stripe hover nowrap" id="usuariosTable">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Usuario</th>
                            <th>Nombre Completo</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Sucursal</th>
                            <th>Cargo</th>
                            <th class="datatable-nosort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr>
                                <td><?= htmlspecialchars($usuario['Cedula_Identidad']) ?></td>
                                <td><?= htmlspecialchars($usuario['Username']) ?></td>
                                <td><?= htmlspecialchars($usuario['Nombres_Usuario'] . ' ' . $usuario['Apellidos_Usuario']) ?></td>
                                <td><?= htmlspecialchars($usuario['Correo_Usuario']) ?></td>
                                <td><?= htmlspecialchars($usuario['Telefono_Usuario']) ?></td>
                                <td><?= htmlspecialchars($usuario['Sucursal_Nombre']) ?></td>
                                <td><?= htmlspecialchars($usuario['Cargo_Nombre']) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <!-- Editar -->
                                        <a href="#"
                                            data-color="#265ed7"
                                            data-toggle="modal"
                                            data-target="#edit-usuario-modal-<?= $usuario['ID_Usuario'] ?>">
                                            <i class="icon-copy dw dw-edit2"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        <?php if ($usuario['Username'] !== $_SESSION['usuario']): ?>
                                            <a href="#"
                                                data-color="#e95959"
                                                data-toggle="modal"
                                                data-target="#delete-usuario-modal"
                                                onclick="setDeleteUsuarioId(<?= $usuario['ID_Usuario'] ?>)">
                                                <i class="icon-copy dw dw-delete-3"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal para agregar usuario -->
        <div class="modal fade" id="usuarioModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form id="formRegistrarUsuario" method="POST" action="index.php?c=usuario&a=registrar">
                        <div class="modal-header">
                            <h5 class="modal-title">Registrar Nuevo Usuario</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Cedula_Identidad">Cédula de Identidad <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Cedula_Identidad" required maxlength="23" placeholder="Ej: 1234567890">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Username">Nombre de Usuario <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Username" required maxlength="20" placeholder="Ej: juanperez">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Nombres_Usuario">Nombres <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Nombres_Usuario" required maxlength="50" placeholder="Ej: Juan Carlos">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Apellidos_Usuario">Apellidos <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="Apellidos_Usuario" required maxlength="50" placeholder="Ej: Pérez García">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Correo_Usuario">Correo Electrónico <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="Correo_Usuario" required maxlength="100" placeholder="ejemplo@correo.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="Telefono_Usuario">Teléfono</label>
                                        <input type="tel" class="form-control" name="Telefono_Usuario" maxlength="20" placeholder="Ej: 0987654321">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="Direccion_Usuario">Dirección</label>
                                        <input type="text" class="form-control" name="Direccion_Usuario" maxlength="255" placeholder="Ej: Av. Principal #123">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ID_Sucursal">Sucursal <span class="text-danger">*</span></label>
                                        <select class="form-control" name="ID_Sucursal" required>
                                            <option value="">Seleccione sucursal</option>
                                            <?php foreach ($sucursales as $sucursal): ?>
                                                <option value="<?= $sucursal['ID_Sucursal'] ?>">
                                                    <?= htmlspecialchars($sucursal['Sucursal_Nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="ID_Cargo">Cargo <span class="text-danger">*</span></label>
                                        <select class="form-control" name="ID_Cargo" required>
                                            <option value="">Seleccione un cargo</option>
                                            <?php foreach ($cargos as $cargo): ?>
                                                <option value="<?= $cargo['ID_Cargo'] ?>">
                                                    <?= htmlspecialchars($cargo['Cargo_Nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="Password">Contraseña <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input name="Password" type="password" class="form-control" placeholder="Mínimo 6 caracteres" required>
                                            <div class="input-group-append toggle-password" style="cursor: pointer;">
                                                <span class="input-group-text"><i class="fa fa-eye"></i></span>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Mínimo 6 caracteres, al menos una letra y un número</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para editar -->
        <?php foreach ($usuarios as $usuario): ?>
            <div class="modal fade" id="edit-usuario-modal-<?= $usuario['ID_Usuario'] ?>" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <form id="formEditarUsuario-<?= $usuario['ID_Usuario'] ?>" method="POST" action="index.php?c=usuario&a=editar">
                            <div class="modal-header">
                                <h4 class="modal-title">Editar Usuario</h4>
                                <button type="button" class="close" data-dismiss="modal">×</button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="ID_Usuario" value="<?= $usuario['ID_Usuario'] ?>">

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cédula de Identidad <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="Cedula_Identidad"
                                                value="<?= htmlspecialchars($usuario['Cedula_Identidad']) ?>"
                                                required maxlength="23">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nombre de Usuario <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="Username"
                                                value="<?= htmlspecialchars($usuario['Username']) ?>"
                                                required maxlength="20">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nombres <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="Nombres_Usuario"
                                                value="<?= htmlspecialchars($usuario['Nombres_Usuario']) ?>"
                                                required maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Apellidos <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="Apellidos_Usuario"
                                                value="<?= htmlspecialchars($usuario['Apellidos_Usuario']) ?>"
                                                required maxlength="50">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Correo Electrónico <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control" name="Correo_Usuario"
                                                value="<?= htmlspecialchars($usuario['Correo_Usuario']) ?>"
                                                required maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Teléfono</label>
                                            <input type="tel" class="form-control" name="Telefono_Usuario"
                                                value="<?= htmlspecialchars($usuario['Telefono_Usuario']) ?>"
                                                maxlength="20">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sucursal <span class="text-danger">*</span></label>
                                            <select class="form-control" name="ID_Sucursal" required>
                                                <option value="">Seleccione una sucursal</option>
                                                <?php foreach ($sucursales as $sucursal): ?>
                                                    <option value="<?= $sucursal['ID_Sucursal'] ?>"
                                                        <?= $sucursal['Sucursal_Nombre'] == $usuario['Sucursal_Nombre'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($sucursal['Sucursal_Nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Cargo <span class="text-danger">*</span></label>
                                            <select class="form-control" name="ID_Cargo" required>
                                                <option value="">Seleccione un cargo</option>
                                                <?php foreach ($cargos as $cargo): ?>
                                                    <option value="<?= $cargo['ID_Cargo'] ?>"
                                                        <?= $cargo['Cargo_Nombre'] == $usuario['Cargo_Nombre'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cargo['Cargo_Nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Dirección</label>
                                            <input type="text" class="form-control" name="Direccion_Usuario"
                                                value="<?= htmlspecialchars($usuario['Direccion_Usuario']) ?>"
                                                maxlength="255">
                                        </div>
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

        <!-- Modal para Eliminar -->
        <div class="modal fade" id="delete-usuario-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-center p-4">
                    <div class="modal-body">
                        <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                        <h4 class="mb-20 font-weight-bold text-danger">¿Eliminar Usuario?</h4>
                        <p class="mb-30 text-muted">Esta acción no se puede deshacer. <br>¿Está seguro que desea eliminar este usuario?</p>

                        <form id="formEliminarUsuario" method="POST" action="index.php?c=usuario&a=eliminar">
                            <input type="hidden" name="ID_Usuario" id="delete_usuario_id">
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

    </div>

<!-- ✅ SCRIPTS CORREGIDOS -->
<script src="assets/js/Modulos/Usuarios/Usuarios_Ajax.js"></script>

</body>

</html>