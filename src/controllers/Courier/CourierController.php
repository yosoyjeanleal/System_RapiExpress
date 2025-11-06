<?php 
use RapiExpress\Models\Courier;


// =================== LISTAR COURIERS ===================
function courier_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $courierModel = new Courier();
    $couriers = $courierModel->obtenerTodos();

    if (isset($_GET['ajax']) && $_GET['ajax'] == 1) {
        header('Content-Type: application/json');
        echo json_encode($couriers);
        exit();
    }

    include __DIR__ . '/../../views/courier/courier.php';
}

// =================== REGISTRAR COURIER ===================
function courier_registrar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $courierModel = new Courier();

        $data = [
            'RIF_Courier'       => trim($_POST['RIF_Courier']),
            'Courier_Nombre'    => trim($_POST['Courier_Nombre']),
            'Courier_Direccion' => trim($_POST['Courier_Direccion']),
            'Courier_Telefono'  => trim($_POST['Courier_Telefono']),
            'Courier_Correo'    => trim($_POST['Courier_Correo']),
        ];

        $resultado = $courierModel->registrar($data);

        $response = match ($resultado) {
            'registro_exitoso'   => ['estado' => 'success', 'mensaje' => 'Courier registrado exitosamente.'],
            'rif_duplicado'      => ['estado' => 'error', 'mensaje' => 'El RIF ingresado ya está registrado.'],
            'correo_duplicado'   => ['estado' => 'error', 'mensaje' => 'El correo ingresado ya está registrado.'],
            'telefono_duplicado' => ['estado' => 'error', 'mensaje' => 'El teléfono ingresado ya está registrado.'],
            'rif_invalido'       => ['estado' => 'error', 'mensaje' => 'El formato del RIF no es válido.'],
            'nombre_invalido'    => ['estado' => 'error', 'mensaje' => 'El nombre contiene caracteres inválidos.'],
            'direccion_invalida' => ['estado' => 'error', 'mensaje' => 'La dirección tiene un formato inválido.'],
            'telefono_invalido'  => ['estado' => 'error', 'mensaje' => 'El número de teléfono no es válido.'],
            'correo_invalido'    => ['estado' => 'error', 'mensaje' => 'El correo electrónico no es válido.'],
            'error_bd'           => ['estado' => 'error', 'mensaje' => 'Error en la base de datos al registrar el courier.'],
            default              => ['estado' => 'error', 'mensaje' => 'Error inesperado al registrar el courier.'],
        };

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

// =================== EDITAR COURIER ===================
function courier_editar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $courierModel = new Courier();

        $data = [
            'ID_Courier'        => (int)$_POST['ID_Courier'],
            'RIF_Courier'       => trim($_POST['RIF_Courier']),
            'Courier_Nombre'    => trim($_POST['Courier_Nombre']),
            'Courier_Direccion' => trim($_POST['Courier_Direccion']),
            'Courier_Telefono'  => trim($_POST['Courier_Telefono']),
            'Courier_Correo'    => trim($_POST['Courier_Correo']),
        ];

        $resultado = $courierModel->actualizar($data);

        $response = match ($resultado) {
            'registro_exitoso'   => ['estado' => 'success', 'mensaje' => 'Courier actualizado correctamente.'],
            'rif_duplicado'      => ['estado' => 'error', 'mensaje' => 'El RIF ingresado ya está registrado.'],
            'correo_duplicado'   => ['estado' => 'error', 'mensaje' => 'El correo ingresado ya está registrado.'],
            'telefono_duplicado' => ['estado' => 'error', 'mensaje' => 'El teléfono ingresado ya está registrado.'],
            'rif_invalido'       => ['estado' => 'error', 'mensaje' => 'El formato del RIF no es válido.'],
            'nombre_invalido'    => ['estado' => 'error', 'mensaje' => 'El nombre contiene caracteres inválidos.'],
            'direccion_invalida' => ['estado' => 'error', 'mensaje' => 'La dirección tiene un formato inválido.'],
            'telefono_invalido'  => ['estado' => 'error', 'mensaje' => 'El número de teléfono no es válido.'],
            'correo_invalido'    => ['estado' => 'error', 'mensaje' => 'El correo electrónico no es válido.'],
            'error_bd'           => ['estado' => 'error', 'mensaje' => 'Error en la base de datos al actualizar el courier.'],
            default              => ['estado' => 'error', 'mensaje' => 'Error inesperado al actualizar el courier.'],
        };

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

// =================== ELIMINAR COURIER ===================
function courier_eliminar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID_Courier']) && is_numeric($_POST['ID_Courier'])) {
        $courierModel = new Courier();
        $id = (int)$_POST['ID_Courier'];
        $resultado = $courierModel->eliminar($id);

        $response = match ($resultado) {
            true                => ['estado' => 'success', 'mensaje' => 'Courier eliminado exitosamente.'],
            'error_restriccion' => ['estado' => 'error', 'mensaje' => 'No se puede eliminar el courier porque tiene paquetes asociados.'],
            'error_bd'          => ['estado' => 'error', 'mensaje' => 'Error en la base de datos al intentar eliminar el courier.'],
            default             => ['estado' => 'error', 'mensaje' => 'Error inesperado al eliminar el courier.'],
        };

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}

// =================== OBTENER COURIER POR ID ===================
function courier_obtenerCourier() {
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $courierModel = new Courier();
        $id = (int)$_GET['id'];
        $courier = $courierModel->obtenerPorId($id);

        if (!$courier) {
            $response = ['estado' => 'error', 'mensaje' => 'Courier no encontrado o error en la base de datos.'];
        } else {
            $response = $courier;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }
}
