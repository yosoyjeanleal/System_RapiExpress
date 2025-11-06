<?php
use RapiExpress\Models\Sucursal;

/**
 * ========================================
 * CONTROLADOR: SUCURSAL
 * ========================================
 * Gestiona CRUD de sucursales con validaciones
 * y respuestas JSON para peticiones AJAX.
 */

// ✅ Listar todas las sucursales
function sucursal_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $sucursalModel = new Sucursal();
    $sucursales = $sucursalModel->obtenerTodas();

    include __DIR__ . '/../../views/sucursal/sucursal.php';
}

// ✅ Registrar nueva sucursal
function sucursal_registrar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sucursalModel = new Sucursal();

        $data = [
            'RIF_Sucursal'       => trim($_POST['RIF_Sucursal']),
            'Sucursal_Nombre'    => trim($_POST['Sucursal_Nombre']),
            'Sucursal_Direccion' => trim($_POST['Sucursal_Direccion']),
            'Sucursal_Telefono'  => trim($_POST['Sucursal_Telefono']),
            'Sucursal_Correo'    => trim($_POST['Sucursal_Correo'])
        ];

        $resultado = $sucursalModel->registrar($data);

        switch ($resultado) {
            case 'registro_exitoso':
                $respuesta = ['estado' => 'success', 'mensaje' => 'Sucursal registrada exitosamente.'];
                break;
            case 'rif_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El RIF ya está registrado.'];
                break;
            case 'nombre_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El nombre de sucursal ya existe.'];
                break;
            case 'telefono_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El teléfono ya está registrado.'];
                break;
            case 'correo_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El correo ya está registrado.'];
                break;
            case 'rif_invalido':
                $respuesta = ['estado' => 'error', 'mensaje' => 'RIF inválido. Formato esperado: J-12345678-9.'];
                break;
            case 'nombre_invalido':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Nombre de sucursal inválido o demasiado corto.'];
                break;
            case 'direccion_invalida':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Dirección inválida o demasiado corta.'];
                break;
            case 'telefono_invalido':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Teléfono inválido. Debe tener entre 7 y 20 dígitos.'];
                break;
            case 'correo_invalido':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Correo electrónico inválido.'];
                break;
            default:
                $respuesta = ['estado' => 'error', 'mensaje' => 'Error al registrar la sucursal.'];
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
}

// ✅ Editar sucursal existente
function sucursal_editar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $sucursalModel = new Sucursal();

        $data = [
            'ID_Sucursal'        => intval($_POST['ID_Sucursal']),
            'RIF_Sucursal'       => trim($_POST['RIF_Sucursal']),
            'Sucursal_Nombre'    => trim($_POST['Sucursal_Nombre']),
            'Sucursal_Direccion' => trim($_POST['Sucursal_Direccion']),
            'Sucursal_Telefono'  => trim($_POST['Sucursal_Telefono']),
            'Sucursal_Correo'    => trim($_POST['Sucursal_Correo'])
        ];

        $resultado = $sucursalModel->actualizar($data);

        switch ($resultado) {
            case 'actualizacion_exitosa':
                $respuesta = ['estado' => 'success', 'mensaje' => 'Sucursal actualizada correctamente.'];
                break;
            case 'rif_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El RIF ya pertenece a otra sucursal.'];
                break;
            case 'nombre_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El nombre ya está en uso por otra sucursal.'];
                break;
            case 'telefono_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El teléfono ya pertenece a otra sucursal.'];
                break;
            case 'correo_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El correo ya pertenece a otra sucursal.'];
                break;
            case 'rif_invalido':
                $respuesta = ['estado' => 'error', 'mensaje' => 'RIF inválido. Formato esperado: J-12345678-9.'];
                break;
            case 'nombre_invalido':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Nombre de sucursal inválido o demasiado corto.'];
                break;
            case 'direccion_invalida':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Dirección inválida o demasiado corta.'];
                break;
            case 'telefono_invalido':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Teléfono inválido. Debe tener entre 7 y 20 dígitos.'];
                break;
            case 'correo_invalido':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Correo electrónico inválido.'];
                break;
            default:
                $respuesta = ['estado' => 'error', 'mensaje' => 'Error al actualizar la sucursal.'];
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
}

// ✅ Eliminar sucursal
function sucursal_eliminar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['delete_sucursal_id']);
        $sucursalModel = new Sucursal();

        $resultado = $sucursalModel->eliminar($id);
        

// Si el resultado es JSON, decodificarlo
$resultadoDecoded = json_decode($resultado, true);

if (is_array($resultadoDecoded) && $resultadoDecoded['codigo'] === 'sucursal_en_uso') {
    $detalles = implode(', ', $resultadoDecoded['tablas']);
    $respuesta = [
        'estado' => 'error', 
        'mensaje' => "No se puede eliminar la sucursal porque tiene registros asociados en: {$detalles}. Primero debe desvincularla o reasignar esos registros."
    ];
} else {
    switch ($resultado) {
        case 'eliminado':
            $respuesta = ['estado' => 'success', 'mensaje' => 'Sucursal eliminada correctamente.'];
            break;
 
            case 'sucursal_en_uso':
                $respuesta = ['estado' => 'error', 'mensaje' => 'No se puede eliminar la sucursal porque está asociada a clientes, paquetes, usuarios u otros registros. Primero debe desvincularla o reasignar esos registros.'];
                break;
            case 'no_existe':
                $respuesta = ['estado' => 'error', 'mensaje' => 'La sucursal no existe o ya fue eliminada.'];
                break;
            case 'error_bd':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Error de base de datos al eliminar la sucursal.'];
                break;
            default:
                $respuesta = ['estado' => 'error', 'mensaje' => 'No se puede eliminar la sucursal porque está asociada a clientes, paquetes, usuarios u otros registros. Primero debe desvincularla o reasignar esos registros.'];
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
     }
}

// ✅ Obtener sucursal por ID (para AJAX / Modal)
function sucursal_obtenerSucursal() {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $sucursalModel = new Sucursal();

        $sucursal = $sucursalModel->obtenerPorId($id);

        header('Content-Type: application/json');
        echo json_encode($sucursal ?: ['error' => 'Sucursal no encontrada.']);
        exit();
    }
}
