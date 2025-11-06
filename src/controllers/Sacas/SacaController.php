<?php
/**
 * Controlador de Sacas
 * VERSIÓN CON CARGA VERIFICADA DEL HELPER
 */

use RapiExpress\Models\Saca;
use RapiExpress\Models\Usuario;
use RapiExpress\Models\Sucursal;
use RapiExpress\Models\Paquete;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

// ✅ CARGAR EL HELPER DE QR CON RUTA ABSOLUTA
$helperPath = dirname(__DIR__, 1) . '/helpers/qr.php';
if (!file_exists($helperPath)) {
    // Intentar ruta alternativa desde raíz del proyecto
    $helperPath = dirname(__DIR__, 2) . '/helpers/qr.php';
}

if (file_exists($helperPath)) {
    require_once $helperPath;
    error_log("✅ Helper QR cargado desde: $helperPath");
} else {
    error_log("❌ ERROR: No se encontró qr.php. Buscado en: $helperPath");
}

// Verificar que la función existe
if (!function_exists('generar_qr_code')) {
    error_log("❌ CRÍTICO: Función generar_qr_code() no disponible después de incluir helper");
}

/**
 * Función para enviar respuesta JSON uniforme
 */
function jsonResponse($success, $message = '', $data = [])
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

function saca_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }
    
    $sacaModel = new Saca();
    $sacas = $sacaModel->obtenerTodas();
    
    $usuarioModel = new Usuario();
    $usuarios = $usuarioModel->obtenerTodos();

    $sucursalModel = new Sucursal();
    $sucursales = $sucursalModel->obtenerTodas();
    
    $paqueteModel = new Paquete();
    $paquetesDisponibles = $paqueteModel->obtenerSinSaca();

    $codigoSaca = $sacaModel->generarCodigo();
    include __DIR__ . '/../../views/saca/saca.php';
}

function saca_registrar() {
    try {
        if (empty($_POST)) {
            jsonResponse(false, 'No se recibieron datos');
        }

        $sacaModel = new Saca();
        
        $errores = [];
        
        if (empty($_POST['ID_Sucursal'])) {
            $errores[] = 'Debe seleccionar una sucursal.';
        }
        
        if (!empty($errores)) {
            jsonResponse(false, implode(', ', $errores), ['errores' => $errores]);
        }

        $idUsuarioLogueado = $_SESSION['ID_Usuario'] ?? null;
        if (!$idUsuarioLogueado) {
            jsonResponse(false, 'No se pudo identificar el usuario logueado.');
        }

        $codigoGenerado = $sacaModel->generarCodigo();

        $data = [
            'Codigo_Saca' => $codigoGenerado,
            'ID_Usuario'  => $idUsuarioLogueado,
            'ID_Sucursal' => (int)$_POST['ID_Sucursal'],
            'Estado'      => $_POST['Estado'] ?? 'Pendiente',
            'Peso_Total'  => 0
        ];
        
        $resultado = $sacaModel->registrar($data);

        if ($resultado === 'registro_exitoso') {
            jsonResponse(true, 'Saca registrada exitosamente. Código: ' . $codigoGenerado);
        } elseif ($resultado === 'codigo_duplicado') {
            jsonResponse(false, 'El código de saca ya existe.');
        } else {
            jsonResponse(false, 'Error al registrar la saca.');
        }
        
    } catch (Exception $e) {
        error_log("Error en saca_registrar: " . $e->getMessage());
        jsonResponse(false, 'Error inesperado: ' . $e->getMessage());
    }
}

function saca_editar() {
    try {
        if (empty($_POST['ID_Saca'])) {
            jsonResponse(false, 'ID de saca no proporcionado.');
        }

        $errores = [];
        
        if (empty($_POST['Codigo_Saca'])) {
            $errores[] = 'El código de saca es obligatorio.';
        }
        if (empty($_POST['ID_Sucursal'])) {
            $errores[] = 'Debe seleccionar una sucursal.';
        }
        
        if (!empty($errores)) {
            jsonResponse(false, implode(', ', $errores), ['errores' => $errores]);
        }

        $sacaModel = new Saca();
        $idUsuarioLogueado = $_SESSION['ID_Usuario'] ?? null;
        
        $data = [
            'ID_Saca'     => (int)$_POST['ID_Saca'],
            'Codigo_Saca' => $_POST['Codigo_Saca'],
            'ID_Usuario'  => $idUsuarioLogueado,
            'ID_Sucursal' => (int)$_POST['ID_Sucursal'],
            'Estado'      => $_POST['Estado'] ?? 'Pendiente',
            'Peso_Total'  => floatval($_POST['Peso_Total'] ?? 0)
        ];

        $resultado = $sacaModel->actualizar($data);

        if ($resultado === true) {
            jsonResponse(true, 'Saca actualizada exitosamente');
        } elseif ($resultado === 'codigo_duplicado') {
            jsonResponse(false, 'El código de saca ya existe');
        } else {
            jsonResponse(false, 'No se realizaron cambios en la saca.');
        }
        
    } catch (Exception $e) {
        error_log("Error en saca_editar: " . $e->getMessage());
        jsonResponse(false, 'Error inesperado: ' . $e->getMessage());
    }
}

function saca_eliminar() {
    try {
        if (empty($_POST['ID_Saca'])) {
            jsonResponse(false, 'ID de saca no proporcionado.');
        }

        $sacaModel = new Saca();
        $id = (int)$_POST['ID_Saca'];

        $resultado = $sacaModel->eliminar($id);

        if ($resultado === true) {
            jsonResponse(true, 'Saca eliminada correctamente.');
        } elseif ($resultado === 'saca_con_paquetes') {
            jsonResponse(false, 'No se puede eliminar la saca porque tiene paquetes relacionados.');
        } else {
            jsonResponse(false, 'No se pudo eliminar la saca.');
        }
        
    } catch (PDOException $e) {
        error_log("Error PDO en saca_eliminar: " . $e->getMessage());
        
        if ($e->getCode() == '23000') {
            jsonResponse(false, 'No se puede eliminar esta saca porque está relacionada con otros registros.');
        }
        jsonResponse(false, 'Error en la base de datos: ' . $e->getMessage());
        
    } catch (Exception $e) {
        error_log("Error en saca_eliminar: " . $e->getMessage());
        jsonResponse(false, 'Error inesperado: ' . $e->getMessage());
    }
}

function saca_obtenerSaca() {
    try {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            jsonResponse(false, 'ID de saca no válido');
        }

        $id = (int)$_GET['id'];
        $sacaModel = new Saca();
        $saca = $sacaModel->obtenerPorId($id);
        
        if ($saca) {
            jsonResponse(true, 'Saca obtenida correctamente', $saca);
        } else {
            jsonResponse(false, 'Saca no encontrada');
        }
        
    } catch (Exception $e) {
        error_log("Error en saca_obtenerSaca: " . $e->getMessage());
        jsonResponse(false, 'Error al obtener la saca: ' . $e->getMessage());
    }
}

function saca_obtenerDatosImpresion() {
    try {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            jsonResponse(false, 'ID de saca no válido');
        }

        $idSaca = (int)$_GET['id'];
        $sacaModel = new Saca();
        $saca = $sacaModel->obtenerPorId($idSaca);

        if (!$saca) {
            jsonResponse(false, 'Saca no encontrada');
        }

        $usuarioModel  = new Usuario();
        $sucursalModel = new Sucursal();
        $detalleModel  = new \RapiExpress\Models\DetalleSaca();

        $usuario = $usuarioModel->obtenerPorId($saca['ID_Usuario']);
        $sucursal = $sucursalModel->obtenerPorId($saca['ID_Sucursal']);
        $paquetes = $detalleModel->obtenerPorSaca($idSaca);

        $qrFileName = null;
        
        // ✅ Generar QR SOLO si la función existe
        if (empty($saca['Qr_Code']) && function_exists('generar_qr_code')) {
            try {
                $qrPath = generar_qr_code('saca', [
                    'Codigo_Saca' => $saca['Codigo_Saca'],
                    'Usuario' => ($usuario['Nombres_Usuario'] ?? '') . ' ' . ($usuario['Apellidos_Usuario'] ?? ''),
                    'Sucursal' => $sucursal['Sucursal_Nombre'] ?? 'N/A',
                    'Cantidad_Paquetes' => count($paquetes),
                    'Peso_Total' => $saca['Peso_Total'],
                    'Fecha_Creacion' => $saca['Fecha_Creacion'] ?? date('Y-m-d H:i:s')
                ], 'src/storage/sacaqr/');
                
                $qrFileName = basename($qrPath);
                
                if (method_exists($sacaModel, 'actualizarQR')) {
                    $sacaModel->actualizarQR($idSaca, $qrFileName);
                }
                
            } catch (Exception $e) {
                error_log("❌ Error al generar QR para saca {$idSaca}: " . $e->getMessage());
            }
        } else {
            $qrFileName = $saca['Qr_Code'] ?? null;
        }

        $data = [
            'Codigo_Saca' => $saca['Codigo_Saca'],
            'Usuario' => ($usuario['Nombres_Usuario'] ?? '') . ' ' . ($usuario['Apellidos_Usuario'] ?? ''),
            'Sucursal' => $sucursal['Sucursal_Nombre'] ?? 'N/A',
            'Estado' => $saca['Estado'],
            'Peso_Total' => $saca['Peso_Total'],
            'Cantidad_Paquetes' => count($paquetes),
            'Fecha_Creacion' => $saca['Fecha_Creacion'] ?? date('Y-m-d H:i:s'),
            'Qr_Code' => $qrFileName
        ];

        jsonResponse(true, 'Datos obtenidos correctamente', $data);
        
    } catch (Exception $e) {
        error_log("Error en saca_obtenerDatosImpresion: " . $e->getMessage());
        jsonResponse(false, 'Error al obtener datos: ' . $e->getMessage());
    }
}

function saca_generarQR() {
    try {
        // ✅ Verificar que la función existe
        if (!function_exists('generar_qr_code')) {
            // Intentar cargar el helper una vez más
            $helperPath = dirname(__DIR__, 1) . '/helpers/qr.php';
            if (!file_exists($helperPath)) {
                $helperPath = dirname(__DIR__, 2) . '/helpers/qr.php';
            }
            
            if (file_exists($helperPath)) {
                require_once $helperPath;
            }
            
            // Si aún no existe, devolver error
            if (!function_exists('generar_qr_code')) {
                header('Content-Type: text/plain');
                echo 'Error: Función generar_qr_code no disponible. Helper QR no cargado.';
                error_log("❌ saca_generarQR: Helper QR no encontrado en: $helperPath");
                exit();
            }
        }
        
        if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
            header('Content-Type: text/plain');
            echo 'ID de saca no válido';
            exit();
        }

        $idSaca = (int)$_GET['id'];
        $sacaModel = new Saca();
        $saca = $sacaModel->obtenerPorId($idSaca);

        if (!$saca) {
            header('Content-Type: text/plain');
            echo 'Saca no encontrada';
            exit();
        }

        $usuarioModel  = new Usuario();
        $sucursalModel = new Sucursal();
        $detalleModel  = new \RapiExpress\Models\DetalleSaca();

        $usuario = $usuarioModel->obtenerPorId($saca['ID_Usuario']);
        $sucursal = $sucursalModel->obtenerPorId($saca['ID_Sucursal']);
        $paquetes = $detalleModel->obtenerPorSaca($idSaca);
        $cantidadPaquetes = count($paquetes);

        $qrPath = generar_qr_code('saca', [
            'Codigo_Saca' => $saca['Codigo_Saca'],
            'Usuario' => ($usuario['Nombres_Usuario'] ?? '') . ' ' . ($usuario['Apellidos_Usuario'] ?? ''),
            'Sucursal' => $sucursal['Sucursal_Nombre'] ?? 'N/A',
            'Cantidad_Paquetes' => $cantidadPaquetes,
            'Peso_Total' => $saca['Peso_Total'],
            'Fecha_Creacion' => $saca['Fecha_Creacion'] ?? date('Y-m-d H:i:s')
        ], 'src/storage/sacaqr/');

        if (file_exists($qrPath)) {
            if (empty($saca['Qr_Code']) && method_exists($sacaModel, 'actualizarQR')) {
                $sacaModel->actualizarQR($idSaca, basename($qrPath));
            }
            
            header('Content-Type: image/png');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            readfile($qrPath);
        } else {
            header('Content-Type: text/plain');
            echo 'Error: Archivo QR no existe después de generarlo: ' . $qrPath;
        }
        exit();
        
    } catch (Exception $e) {
        error_log("Error en saca_generarQR: " . $e->getMessage());
        header('Content-Type: text/plain');
        echo 'Error: ' . $e->getMessage();
        exit();
    }
}