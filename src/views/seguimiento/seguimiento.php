<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Seguimiento RapiExpress</title>
    <link rel="icon" href="assets/img/logo-rapi.ico" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    
    <!-- Fonts y estilos -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="vendors/styles/core.css">
    <link rel="stylesheet" type="text/css" href="vendors/styles/icon-font.min.css">
    <link rel="stylesheet" type="text/css" href="vendors/styles/style.css">
    
    <!-- Scripts -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
</head>
<body>
    <?php include 'src\views\partels\barras.php'; ?>
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="title">
                        <h4>Seguimiento de Paquetes</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="index.php?c=dashboard&a=index">RapiExpress</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Seguimiento</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
<div class="card-box mb-30">
    <div class="pd-20">
        <h4 class="text-blue h4">Buscar Paquete o Prealerta</h4>
        <?php include 'src/views/partels/notificaciones.php'; ?>
        
        <form id="formBuscarTracking" class="mt-3">
            <div class="row">
                <div class="col-md-10 col-sm-12">
                    <input type="text" name="tracking" class="form-control" placeholder="Ingrese código de tracking" required>
                </div>
                <div class="col-md-2 col-sm-12">
                    <button type="submit" class="btn btn-primary btn-block">Buscar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Contenedor para resultados -->
<div id="resultadoTracking"></div>

        
    </div>

    <script src="assets\js\Modulos\Tracking\Tracking_Ajax.js">
</script>

</body>
</html>