<?php
use RapiExpress\Models\Prealerta;
use RapiExpress\Models\Paquete;

/**
 * ✅ Mostrar vista de seguimiento
 */
function seguimiento_index() {
    if (!isset($_SESSION['ID_Usuario'])) {
        header('Location: index.php?c=auth&a=login');
        exit;
    }

    include __DIR__ . '/../../views/seguimiento/seguimiento.php';
}

/**
 * ✅ Endpoint AJAX para buscar tracking
 */
function seguimiento_buscar() {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['ID_Usuario'])) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'No autorizado']);
        exit;
    }

    $tracking = trim($_POST['tracking'] ?? '');
    
    if (empty($tracking)) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Debe ingresar un código de tracking']);
        exit;
    }

    $resultado = ['prealerta' => null, 'paquete' => null];

    try {
        $prealertaModel = new Prealerta();
        $paqueteModel = new Paquete();

        // Buscar en prealertas (por Tracking_Tienda)
        $prealerta = $prealertaModel->obtenerPorTrackingTienda($tracking);
        if ($prealerta) {
            $resultado['prealerta'] = $prealerta;
        }

        // Buscar en paquetes (por Tracking)
        $paquete = $paqueteModel->obtenerPorTracking($tracking);
        if ($paquete) {
            $resultado['paquete'] = $paquete;
        }

        // Si no se encuentra nada
        if (!$prealerta && !$paquete) {
            echo json_encode([
                'estado' => 'no_encontrado',
                'mensaje' => "No se encontró ningún registro con el tracking: $tracking"
            ]);
            exit;
        }

        echo json_encode($resultado);

    } catch (Exception $e) {
        error_log("Error en seguimiento_buscar: " . $e->getMessage());
        echo json_encode([
            'estado' => 'error',
            'mensaje' => 'Error al buscar el tracking: ' . $e->getMessage()
        ]);
    }
    exit;
}