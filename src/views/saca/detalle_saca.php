<?php
use RapiExpress\Helpers\Lang;

// Mensajes de sesión
$mensaje = $_SESSION['mensaje'] ?? '';
$tipo_mensaje = $_SESSION['tipo_mensaje'] ?? '';
unset($_SESSION['mensaje'], $_SESSION['tipo_mensaje']);
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">
<head>
    <meta charset="utf-8" />
    <title>RapiExpress - Detalle Saca <?= isset($saca['ID_Saca']) ? '#'.$saca['ID_Saca'] : '' ?></title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include 'src/views/partels/barras.php'; ?>
<div class="mobile-menu-overlay"></div>
<div class="main-container">

    <div class="page-header">
        <div class="row">
            <div class="col-md-12 col-sm-12 d-flex justify-content-between align-items-center">
                <div class="title">
                    <h4>Detalle de Saca <?= isset($saca['ID_Saca']) ? '#'.$saca['ID_Saca'] : '' ?></h4>
                </div>
                <div>
                    <a href="index.php?c=saca&a=index" class="btn btn-secondary">Volver a Sacas</a>
                </div>
                
            </div>
        </div>
    </div>

    <?php if ($mensaje): ?>
        <script>
            Swal.fire({
                icon: '<?= $tipo_mensaje ?>',
                title: '<?= $mensaje ?>',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    <?php endif; ?>

    <!-- 🔹 Formulario de búsqueda siempre visible -->
    <div class="card-box mb-30">
        <div class="pd-30">
            <h4 class="text-blue h4">Buscar Saca</h4>
            <p>Ingrese el ID o código de la saca que desea consultar.</p>
            <form method="GET" action="index.php">
                <input type="hidden" name="c" value="detallesaca">
                <input type="hidden" name="a" value="index">
                <div class="input-group mb-3" style="max-width: 400px;">
                    <input type="text" name="id" class="form-control" placeholder="Ej: 12 o SAC-0012" required>
                    <button class="btn btn-primary" type="submit">Buscar</button>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($saca)): ?>
        <!-- MODO: Detalle de la Saca -->
        <!-- Paquetes en la Saca -->
        <div class="card-box mb-30">
            <div class="pd-30 d-flex justify-content-between align-items-center">
                <h4 class="text-blue h4 mb-0">Paquetes en la Saca</h4>
                <span class="badge bg-info">Peso Total: <?= htmlspecialchars($saca['Saca_Peso'] ?? '0') ?> Kg</span>
            </div>
            <div class="pb-30">
                <?php if (!empty($paquetesEnSaca)): ?>
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tracking</th>
                            <th>Cliente</th>
                            <th>Peso</th>
                            <th>Estado</th>
                            <th class="datatable-nosort">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paquetesEnSaca as $paquete): ?>
                            <tr>
                                <td><?= $paquete['ID_Paquete'] ?></td>
                                <td><?= htmlspecialchars($paquete['Tracking']) ?></td>
                                <td><?= htmlspecialchars(($paquete['Nombres_Cliente'] ?? '') . ' ' . ($paquete['Apellidos_Cliente'] ?? '')) ?></td>
                                <td><?= $paquete['Paquete_Peso'] ?? 0 ?></td>
                                <td><?= htmlspecialchars($paquete['Estado'] ?? '') ?></td>
                                <td>
                                    <form method="POST" action="index.php?c=detallesaca&a=quitar">
                                        <input type="hidden" name="ID_Saca" value="<?= $saca['ID_Saca'] ?>">
                                        <input type="hidden" name="ID_Paquete" value="<?= $paquete['ID_Paquete'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Quitar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="text-muted px-3">No hay paquetes en esta saca.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Paquetes disponibles para agregar -->
        <div class="card-box mb-30">
            <div class="pd-30">
                <h4 class="text-blue h4">Agregar Paquetes a la Saca</h4>
            </div>
            <div class="pb-30">
                <?php if (!empty($paquetesDisponibles)): ?>
                <table class="data-table table stripe hover nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tracking</th>
                            <th>Cliente</th>
                            <th>Peso</th>
                            <th class="datatable-nosort">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paquetesDisponibles as $paquete): ?>
                            <tr>
                                <td><?= $paquete['ID_Paquete'] ?></td>
                                <td><?= htmlspecialchars($paquete['Tracking']) ?></td>
                                <td><?= htmlspecialchars(($paquete['Nombres_Cliente'] ?? '') . ' ' . ($paquete['Apellidos_Cliente'] ?? '')) ?></td>
                                <td><?= $paquete['Paquete_Peso'] ?? 0 ?></td>
                                <td>
                                    <form action="index.php?c=detallesaca&a=agregar" method="POST" style="display:inline;">
                                        <input type="hidden" name="ID_Saca" value="<?= $saca['ID_Saca'] ?>">
                                        <input type="hidden" name="ID_Paquete" value="<?= $paquete['ID_Paquete'] ?>">
                                        <button type="submit" class="btn btn-success btn-sm">Agregar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <p class="text-muted px-3">No hay paquetes disponibles para agregar.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    
</div>
</body>
</html>
