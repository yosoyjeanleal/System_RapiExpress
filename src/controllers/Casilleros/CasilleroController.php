<?php
use RapiExpress\Models\Casillero;
use RapiExpress\Helpers\Lang;

function casillero_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $casilleroModel = new Casillero();
    $casilleros = $casilleroModel->obtenerTodos();
    include __DIR__ . '/../../views/casillero/casillero.php';
}

function casillero_registrar() {
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'estado' => 'error',
            'mensaje' => Lang::get('method_not_allowed')
        ]);
        exit();
    }

    $casilleroModel = new Casillero();
    $data = [
        'Casillero_Nombre' => trim($_POST['Casillero_Nombre'] ?? ''),
        'Direccion' => trim($_POST['Direccion'] ?? '')
    ];

    $resultado = $casilleroModel->registrar($data);

    switch ($resultado) {
        case 'registro_exitoso':
            $respuesta = [
                'estado' => 'success',
                'mensaje' => Lang::get('casillero_registered_success')
            ];
            break;
        case 'casillero_existente':
            $respuesta = [
                'estado' => 'info',
                'mensaje' => Lang::get('casillero_exists')
            ];
            break;
        case 'campos_vacios':
            $respuesta = [
                'estado' => 'warning',
                'mensaje' => Lang::get('fill_required_fields')
            ];
            break;
        case 'nombre_invalido':
            $respuesta = [
                'estado' => 'warning',
                'mensaje' => Lang::get('invalid_name_format')
            ];
            break;
        case 'direccion_invalida':
            $respuesta = [
                'estado' => 'warning',
                'mensaje' => Lang::get('invalid_address_format')
            ];
            break;
        default:
            $respuesta = [
                'estado' => 'error',
                'mensaje' => Lang::get('casillero_register_error')
            ];
    }

    echo json_encode($respuesta);
}

function casillero_editar() {
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'estado' => 'error',
            'mensaje' => Lang::get('method_not_allowed')
        ]);
        exit();
    }

    $casilleroModel = new Casillero();
    $data = [
        'ID_Casillero' => intval($_POST['ID_Casillero'] ?? 0),
        'Casillero_Nombre' => trim($_POST['Casillero_Nombre'] ?? ''),
        'Direccion' => trim($_POST['Direccion'] ?? '')
    ];

    $resultado = $casilleroModel->actualizar($data);

    switch ($resultado) {
        case 'actualizacion_exitosa':
            $respuesta = [
                'estado' => 'success',
                'mensaje' => Lang::get('casillero_updated_success')
            ];
            break;
        case 'sin_cambios':
            $respuesta = [
                'estado' => 'info',
                'mensaje' => Lang::get('no_changes_made')
            ];
            break;
        case 'casillero_existente':
            $respuesta = [
                'estado' => 'warning',
                'mensaje' => Lang::get('casillero_exists')
            ];
            break;
        case 'nombre_invalido':
            $respuesta = [
                'estado' => 'warning',
                'mensaje' => Lang::get('invalid_name_format')
            ];
            break;
        case 'direccion_invalida':
            $respuesta = [
                'estado' => 'warning',
                'mensaje' => Lang::get('invalid_address_format')
            ];
            break;
        default:
            $respuesta = [
                'estado' => 'error',
                'mensaje' => Lang::get('casillero_update_error')
            ];
    }

    echo json_encode($respuesta);
}

function casillero_eliminar() {
    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode([
            'estado' => 'error',
            'mensaje' => Lang::get('method_not_allowed')
        ]);
        exit();
    }

    $casilleroModel = new Casillero();
    $id = intval($_POST['ID_Casillero'] ?? 0);

    if ($id <= 0) {
        echo json_encode([
            'estado' => 'error',
            'mensaje' => Lang::get('invalid_id')
        ]);
        exit();
    }

    $resultado = $casilleroModel->eliminar($id);

    switch ($resultado) {
        case 'eliminado':
            $respuesta = [
                'estado' => 'success',
                'mensaje' => Lang::get('casillero_deleted_success')
            ];
            break;
        case 'no_existente':
            $respuesta = [
                'estado' => 'warning',
                'mensaje' => Lang::get('casillero_not_found')
            ];
            break;
        case 'casillero_asignado':
            $respuesta = [
                'estado' => 'warning',
                'mensaje' => Lang::get('casillero_in_use')
            ];
            break;
        default:
            $respuesta = [
                'estado' => 'error',
                'mensaje' => Lang::get('casillero_delete_error')
            ];
    }

    echo json_encode($respuesta);
}
