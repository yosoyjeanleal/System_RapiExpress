<?php
use RapiExpress\Models\Paquete;
use RapiExpress\Models\Cliente;
use RapiExpress\Models\Tienda;
use RapiExpress\Models\Casillero;
use RapiExpress\Models\Categoria;
use RapiExpress\Models\Sucursal;
use RapiExpress\Models\Courier;

session_start();

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

/** 
 * Mostrar lista de paquetes (VISTA)
 */
function paquete_index()
{
    try {
        // Obtener datos para la vista
        $clientes   = (new Cliente())->obtenerTodos();
        $tiendas    = (new Tienda())->obtenerTodas();
        $casilleros = (new Casillero())->obtenerTodos();
        $sucursales = (new Sucursal())->obtenerTodas();
        $categorias = (new Categoria())->obtenerTodos();
        $couriers   = (new Courier())->obtenerTodos();
        
        $model = new Paquete();
        $paquetes = $model->obtenerTodos();

        // Incluir la vista
        include __DIR__ . '/../../views/paquete/paquete.php';
        
    } catch (Exception $e) {
        echo "Error al cargar la vista: " . $e->getMessage();
        error_log("Error en paquete_index: " . $e->getMessage());
    }
}

/** 
 * Registrar paquete (AJAX)
 */
function paquete_registrar()
{
    try {
        if (empty($_POST)) {
            jsonResponse(false, 'No se recibieron datos');
        }

        $data = $_POST;
        $data['ID_Usuario'] = $_SESSION['ID_Usuario'] ?? null;

        // Validar cliente
        if (empty($data['ID_Cliente'])) {
            jsonResponse(false, 'Debe seleccionar un cliente.');
        }

        $modelCliente = new Cliente();
        $cliente = $modelCliente->obtenerPorId($data['ID_Cliente']);
        if (!$cliente) {
            jsonResponse(false, 'El cliente no existe.');
        }

        $data['Nombre_Cliente'] = $cliente['Nombres_Cliente'] . ' ' . $cliente['Apellidos_Cliente'];

        $model = new Paquete();

        // Generar tracking si no existe
        if (empty($data['Tracking'])) {
            $data['Tracking'] = $model->generarTracking();
        }

        $resultado = $model->registrar($data);

        // El modelo devuelve un array con 'success' y posiblemente 'errores'
        if (is_array($resultado)) {
            if ($resultado['success']) {
                jsonResponse(true, 'Paquete registrado correctamente.');
            } else {
                $errores = $resultado['errores'] ?? ['Error desconocido'];
                jsonResponse(false, implode(', ', $errores), ['errores' => $errores]);
            }
        } elseif ($resultado) {
            jsonResponse(true, 'Paquete registrado correctamente.');
        } else {
            jsonResponse(false, 'Error al registrar el paquete.');
        }

    } catch (PDOException $e) {
        error_log("Error PDO en paquete_registrar: " . $e->getMessage());
        jsonResponse(false, 'Error en la base de datos: ' . $e->getMessage());
    } catch (Exception $e) {
        error_log("Error en paquete_registrar: " . $e->getMessage());
        jsonResponse(false, 'Error inesperado: ' . $e->getMessage());
    }
}

/** 
 * Editar paquete (AJAX)
 */
function paquete_editar()
{
    try {
        if (empty($_POST['ID_Paquete'])) {
            jsonResponse(false, 'ID del paquete no proporcionado.');
        }

        $data = $_POST;
        $model = new Paquete();

        $resultado = $model->editar($data['ID_Paquete'], $data);
        
        // El modelo devuelve un array con 'success' y posiblemente 'errores'
        if (is_array($resultado)) {
            if ($resultado['success']) {
                jsonResponse(true, 'Paquete actualizado correctamente.');
            } else {
                $errores = $resultado['errores'] ?? ['Error desconocido'];
                jsonResponse(false, implode(', ', $errores), ['errores' => $errores]);
            }
        } elseif ($resultado) {
            jsonResponse(true, 'Paquete actualizado correctamente.');
        } else {
            jsonResponse(false, 'No se realizaron cambios en el paquete.');
        }

    } catch (PDOException $e) {
        error_log("Error PDO en paquete_editar: " . $e->getMessage());
        jsonResponse(false, 'Error en la base de datos: ' . $e->getMessage());
    } catch (Exception $e) {
        error_log("Error en paquete_editar: " . $e->getMessage());
        jsonResponse(false, 'Error inesperado: ' . $e->getMessage());
    }
}

/** 
 * Eliminar paquete (AJAX)
 */
function paquete_eliminar()
{
    try {
        if (empty($_POST['ID_Paquete'])) {
            jsonResponse(false, 'ID del paquete no proporcionado.');
        }

        $model = new Paquete();
        $resultado = $model->eliminar($_POST['ID_Paquete']);

        // Si el modelo devuelve un array con errores
        if (is_array($resultado)) {
            if (isset($resultado['success']) && !$resultado['success']) {
                $errores = $resultado['errores'] ?? ['Error desconocido'];
                jsonResponse(false, implode(', ', $errores));
            }
        }
        
        // Si devuelve true (eliminación exitosa)
        if ($resultado === true) {
            jsonResponse(true, 'Paquete eliminado correctamente.');
        } else {
            jsonResponse(false, 'No se pudo eliminar el paquete.');
        }

    } catch (PDOException $e) {
        error_log("Error PDO en paquete_eliminar: " . $e->getMessage());
        
        // Caso típico de restricción por clave foránea
        if ($e->getCode() == '23000') {
            jsonResponse(false, 'No se puede eliminar este paquete porque está relacionado con otros registros (sacas, seguimientos o manifiestos).');
        }
        jsonResponse(false, 'Error en la base de datos: ' . $e->getMessage());
        
    } catch (Exception $e) {
        error_log("Error en paquete_eliminar: " . $e->getMessage());
        jsonResponse(false, 'Error inesperado: ' . $e->getMessage());
    }
    
}

