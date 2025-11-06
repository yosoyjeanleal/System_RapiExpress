<?php
use RapiExpress\Models\Cargo;
use RapiExpress\Helpers\Lang;

// ✅ Listar cargos
function cargo_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }
    $cargoModel = new Cargo();
    $cargos = $cargoModel->obtenerTodos();
    include __DIR__ . '/../../views/cargo/cargo.php';
}

// ✅ Registrar cargo
function cargo_registrar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cargoModel = new Cargo();
        $nombreCargo = trim($_POST['Cargo_Nombre']);

        if (!$cargoModel->validarNombre($nombreCargo)) {
            $respuesta = ['estado' => 'error', 'mensaje' => Lang::get('cargos.invalid_name')];
        } elseif ($cargoModel->verificarCargoExistente($nombreCargo)) {
            $respuesta = ['estado' => 'error', 'mensaje' => Lang::get('cargos.duplicate_name')];
        } else {
            $resultado = $cargoModel->registrar(['Cargo_Nombre' => $nombreCargo]);
            $respuesta = match ($resultado) {
                'registro_exitoso' => ['estado' => 'success', 'mensaje' => Lang::get('cargos.register_success')],
                'error_validacion' => ['estado' => 'error', 'mensaje' => Lang::get('cargos.model_validation_error')],
                default => ['estado' => 'error', 'mensaje' => Lang::get('cargos.db_error')]
            };
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
}

// ✅ Editar cargo
function cargo_editar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cargoModel = new Cargo();
        $data = [
            'ID_Cargo' => intval($_POST['ID_Cargo']),
            'Cargo_Nombre' => trim($_POST['Cargo_Nombre'])
        ];

        if (!$cargoModel->validarNombre($data['Cargo_Nombre'])) {
            $respuesta = ['estado' => 'error', 'mensaje' => Lang::get('cargos.invalid_name')];
        } elseif ($cargoModel->verificarCargoExistente($data['Cargo_Nombre'], $data['ID_Cargo'])) {
            $respuesta = ['estado' => 'error', 'mensaje' => Lang::get('cargos.duplicate_name')];
        } else {
            $resultado = $cargoModel->actualizar($data);
            $respuesta = match ($resultado) {
                'actualizado' => ['estado' => 'success', 'mensaje' => Lang::get('cargos.update_success')],
                'error_validacion' => ['estado' => 'error', 'mensaje' => Lang::get('cargos.model_validation_error')],
                default => ['estado' => 'error', 'mensaje' => Lang::get('cargos.db_error')]
            };
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
}

// ✅ Eliminar cargo
function cargo_eliminar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['delete_cargo_id']);
        $cargoModel = new Cargo();
        $resultado = $cargoModel->eliminar($id);

        $respuesta = match ($resultado) {
            'eliminado' => ['estado' => 'success', 'mensaje' => Lang::get('cargos.delete_success')],
            'cargo_en_uso' => ['estado' => 'error', 'mensaje' => Lang::get('cargos.in_use')],
            default => ['estado' => 'error', 'mensaje' => Lang::get('cargos.delete_error')]
        };

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
}

// ✅ Obtener cargo por ID
function cargo_obtenerCargo() {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $cargoModel = new Cargo();
        $cargo = $cargoModel->obtenerPorId($id);

        header('Content-Type: application/json');
        echo json_encode($cargo ?: ['error' => Lang::get('cargos.not_found')]);
        exit();
    }
}
