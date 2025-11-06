<?php
use RapiExpress\Models\Prealerta;
use RapiExpress\Models\Paquete;
use RapiExpress\Models\Cliente;
use RapiExpress\Models\Tienda;
use RapiExpress\Models\Casillero;
use RapiExpress\Models\Sucursal;
use RapiExpress\Models\Categoria;
use RapiExpress\Models\Courier;

/**
 * ========================================
 * CONTROLADOR: PREALERTA (Versión AJAX)
 * ========================================
 */

// ✅ Listar todas las prealertas
function prealerta_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $model = new Prealerta();
    $prealertas = $model->obtenerTodos();

    // ✅ CORREGIDO: Agregar categorías y couriers
    $clientes = (new Cliente())->obtenerTodos();
    $tiendas = (new Tienda())->obtenerTodas();
    $casilleros = (new Casillero())->obtenerTodos();
    $sucursales = (new Sucursal())->obtenerTodas();
    $categorias = (new Categoria())->obtenerTodos();
    $couriers = (new Courier())->obtenerTodos();

    include __DIR__ . '/../../views/prealerta/prealerta.php';
}

// ✅ Registrar Prealerta (AJAX)
function prealerta_registrar() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Método no permitido.']);
        exit();
    }

    if (!isset($_SESSION['ID_Usuario'])) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Sesión no iniciada.']);
        exit();
    }

    $data = [
        'ID_Cliente' => $_POST['ID_Cliente'] ?? null,
        'ID_Tienda' => $_POST['ID_Tienda'] ?? null,
        'ID_Casillero' => $_POST['ID_Casillero'] ?? null,
        'ID_Sucursal' => $_POST['ID_Sucursal'] ?? null,
        'Tracking_Tienda' => trim($_POST['Tracking_Tienda'] ?? ''),
        'Prealerta_Piezas' => $_POST['Prealerta_Piezas'] ?? 0,
        'Prealerta_Peso' => $_POST['Prealerta_Peso'] ?? 0,
        'Prealerta_Descripcion' => trim($_POST['Prealerta_Descripcion'] ?? ''),
        'ID_Usuario' => $_SESSION['ID_Usuario'],
        'Estado' => 'Prealerta'
    ];

    $prealertaModel = new Prealerta();
    $resultado = $prealertaModel->registrar($data);

    if ($resultado === 'registro_exitoso') {
        $respuesta = ['estado' => 'success', 'mensaje' => 'Prealerta registrada correctamente.'];
    } else {
        $respuesta = ['estado' => 'error', 'mensaje' => 'Error al registrar la prealerta.'];
    }

    header('Content-Type: application/json');
    echo json_encode($respuesta);
    exit();
}

// ✅ Editar Prealerta (AJAX) - CORREGIDO CON ELIMINACIÓN
function prealerta_editar() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Método no permitido.']);
        exit();
    }

    $id = intval($_POST['ID_Prealerta'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'ID de prealerta inválido.']);
        exit();
    }

    $data = $_POST;
    $prealertaModel = new Prealerta();
    $prealerta = $prealertaModel->obtenerPorId($id);

    if (!$prealerta) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Prealerta no encontrada.']);
        exit();
    }

    $cambiarAConsolidado = ($data['Estado'] ?? $prealerta['Estado']) === 'Consolidado' 
                           && $prealerta['Estado'] !== 'Consolidado';

    // Si NO se está consolidando, solo actualizar
    if (!$cambiarAConsolidado) {
        $resultado = $prealertaModel->editar($id, $data);
        
        if ($resultado === 'actualizacion_exitosa') {
            $respuesta = ['estado' => 'success', 'mensaje' => 'Prealerta actualizada correctamente.'];
        } else {
            $respuesta = ['estado' => 'error', 'mensaje' => 'Error al actualizar la prealerta.'];
        }
        
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }

    // ✅ PROCESO DE CONSOLIDACIÓN CON ELIMINACIÓN
    try {
        // Validar que se hayan enviado categoría y courier
        if (empty($data['ID_Categoria']) || empty($data['ID_Courier'])) {
            throw new Exception('Debe seleccionar categoría y courier para consolidar.');
        }

        $paqueteModel = new Paquete();
        
        // 1. Obtener datos del cliente
        $clienteModel = new Cliente();
        $cliente = $clienteModel->obtenerPorId($prealerta['ID_Cliente']);
        
        if (!$cliente) {
            throw new Exception('Cliente no encontrado.');
        }
        
        $nombreCliente = $cliente['Nombres_Cliente'] . ' ' . $cliente['Apellidos_Cliente'];
        
        // 2. Generar tracking único para el paquete
        $trackingPaquete = $paqueteModel->generarTracking();
        
        // 3. Crear el paquete con los datos de la prealerta (INCLUYENDO PIEZAS)
        $dataPaquete = [
            'ID_Prealerta' => $id,
            'ID_Usuario' => $_SESSION['ID_Usuario'],
            'ID_Cliente' => $prealerta['ID_Cliente'],
            'Nombre_Cliente' => $nombreCliente,
            'Nombre_Instrumento' => $prealerta['Prealerta_Descripcion'],
            'ID_Categoria' => $data['ID_Categoria'],
            'ID_Sucursal' => $prealerta['ID_Sucursal'],
            'Tracking' => $trackingPaquete,
            'ID_Courier' => $data['ID_Courier'],
            'Prealerta_Descripcion' => $prealerta['Prealerta_Descripcion'],
            'Paquete_Peso' => $prealerta['Prealerta_Peso'],
            'Paquete_Piezas' => $prealerta['Prealerta_Piezas'], // ✅ PASAR LAS PIEZAS
            'Estado' => 'En tránsito'
        ];
        
        // 4. Registrar el paquete
        $resultadoPaquete = $paqueteModel->registrar($dataPaquete);
        
        if (!$resultadoPaquete || (is_array($resultadoPaquete) && !$resultadoPaquete['success'])) {
            $errorMsg = 'Error al crear el paquete.';
            if (is_array($resultadoPaquete) && !empty($resultadoPaquete['errores'])) {
                $errorMsg .= ' ' . implode(', ', $resultadoPaquete['errores']);
            }
            throw new Exception($errorMsg);
        }
        
        // 5. ✅ ELIMINAR LA PREALERTA (en lugar de cambiar su estado)
        $eliminado = $prealertaModel->eliminarDespuesDeConsolidar($id);
        
        if (!$eliminado) {
            throw new Exception('El paquete se creó correctamente, pero hubo un problema al eliminar la prealerta.');
        }
        
        $respuesta = [
            'estado' => 'success', 
            'mensaje' => 'Prealerta consolidada exitosamente. Se creó el paquete con tracking: ' . $trackingPaquete . ' y se eliminó la prealerta.',
            'tracking_paquete' => $trackingPaquete
        ];
        
    } catch (Exception $e) {
        error_log("Error en consolidación: " . $e->getMessage());
        $respuesta = [
            'estado' => 'error', 
            'mensaje' => 'Error al consolidar: ' . $e->getMessage()
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($respuesta);
    exit();
}

// ✅ Eliminar Prealerta (AJAX)
function prealerta_eliminar() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Método no permitido.']);
        exit();
    }

    $id = intval($_POST['delete_prealerta_id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'ID inválido.']);
        exit();
    }

    $model = new Prealerta();
    $resultado = $model->eliminar($id);

    switch ($resultado) {
        case 'eliminado':
            $respuesta = ['estado' => 'success', 'mensaje' => 'Prealerta eliminada correctamente.'];
            break;
        case 'no_existe':
            $respuesta = ['estado' => 'error', 'mensaje' => 'La prealerta no existe o ya fue eliminada.'];
            break;
        case 'error_bd':
            $respuesta = ['estado' => 'error', 'mensaje' => 'Error de base de datos al eliminar la prealerta.'];
            break;
        default:
            $respuesta = ['estado' => 'error', 'mensaje' => 'No se pudo eliminar la prealerta.'];
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($respuesta);
    exit();
}

// ✅ Obtener Prealerta por ID (para Modal AJAX)
function prealerta_obtener() {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $prealertaModel = new Prealerta();

        $prealerta = $prealertaModel->obtenerPorId($id);

        header('Content-Type: application/json');
        echo json_encode($prealerta ?: ['error' => 'Prealerta no encontrada.']);
        exit();
    }
}