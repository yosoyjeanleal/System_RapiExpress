<?php 
use RapiExpress\Models\Saca;
use RapiExpress\Helpers\Lang;
?>
<!DOCTYPE html>
<html lang="<?= Lang::current() ?>">
<head>
    <meta charset="utf-8" />
    <title>RapiExpress - <?= Lang::get('manifiestos_title') ?? 'Manifiestos' ?></title>
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
                    <h4><?= Lang::get('manifiestos_title') ?? 'Manifiestos' ?></h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php?c=dashboard&a=index">RapiExpress</a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?= Lang::get('manifiestos_title') ?? 'Manifiestos' ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <div class="card-box mb-30">
        <div class="pd-30">
            <h4 class="text-blue h4"><?= Lang::get('manifiestos_list') ?? 'Lista de Manifiestos' ?></h4>
            <?php include 'src/views/partels/notificaciones.php'; ?>
            <div class="pull-right">
                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#generarManifiestoModal">
                    <i class="fa fa-file-pdf"></i> <?= Lang::get('generar_manifiesto') ?? 'Generar Manifiesto' ?>
                </button>
            </div>
        </div>

        <div class="pb-30">
            <table class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Saca</th>
                        <th>Usuario</th>
                        <th>PDF</th>
                        <th>Fecha</th>
                        <th class="datatable-nosort"><?= Lang::get('actions') ?? 'Acciones' ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($manifiestos as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['ID_Manifiesto']) ?></td>
                        <td><?= htmlspecialchars($m['Codigo_Saca']) ?></td>
                        <td><?= htmlspecialchars($m['Nombres_Usuario'].' '.$m['Apellidos_Usuario']) ?></td>
                        <td><a class="btn btn-info btn-sm" href="<?= $m['Ruta_PDF'] ?>" target="_blank">
                            <i class="fa fa-file-pdf"></i> Ver PDF
                        </a></td>
                        <td><?= htmlspecialchars($m['Fecha_Creacion']) ?></td>
                        <td>
                            <div class="dropdown">
                                <a class="btn btn-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                    <i class="dw dw-more"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete-manifiesto-modal"
                                       onclick="document.getElementById('delete_manifiesto_id').value = <?= $m['ID_Manifiesto'] ?>">
                                        <i class="dw dw-delete-3"></i> <?= Lang::get('delete') ?? 'Eliminar' ?>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Generar Manifiesto -->
    <div class="modal fade" id="generarManifiestoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <form method="POST" action="index.php?c=manifiesto&a=generar" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= Lang::get('generar_manifiesto') ?? 'Generar Manifiesto' ?></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <label for="ID_Saca"><?= Lang::get('select_saca') ?? 'Seleccionar Saca' ?></label>
                    <select name="ID_Saca" id="ID_Saca" class="form-control" required>
                        <?php 
                        $sacaModel = new Saca();
                        $sacas = $sacaModel->obtenerTodas();
                        foreach($sacas as $s): ?>
                            <option value="<?= $s['ID_Saca'] ?>"><?= $s['Codigo_Saca'] ?> (<?= $s['Estado'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal"><?= Lang::get('cancel') ?? 'Cancelar' ?></button>
                    <button class="btn btn-primary" type="submit"><?= Lang::get('generar') ?? 'Generar' ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Eliminar Manifiesto -->
    <div class="modal fade" id="delete-manifiesto-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form method="POST" action="index.php?c=manifiesto&a=eliminar" class="modal-content text-center p-4">
                <div class="modal-body">
                    <i class="bi bi-exclamation-triangle-fill text-danger mb-3" style="font-size: 3rem;"></i>
                    <h4 class="mb-20 font-weight-bold text-danger"><?= Lang::get('delete_manifiesto') ?? 'Eliminar Manifiesto' ?></h4>
                    <p class="mb-30 text-muted"><?= Lang::get('delete_manifiesto_confirm') ?? '¿Está seguro que desea eliminar este manifiesto?' ?></p>
                    <input type="hidden" name="ID_Manifiesto" id="delete_manifiesto_id">
                    <div class="row justify-content-center gap-2">
                        <div class="col-6 px-1">
                            <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">
                                <i class="fa fa-times"></i> <?= Lang::get('no') ?? 'No' ?>
                            </button>
                        </div>
                        <div class="col-6 px-1">
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fa fa-check"></i> <?= Lang::get('yes') ?? 'Sí' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

  
</div>
</body>
</html>
