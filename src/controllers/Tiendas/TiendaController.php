<?php
use RapiExpress\Models\Tienda;

function tienda_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $tiendaModel = new Tienda();
    $tiendas = $tiendaModel->obtenerTodas();
    include __DIR__ . '/../../views/tienda/tienda.php';
}

function tienda_registrar() {
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['estado'=>'error','mensaje'=>'Método no permitido']);
        exit();
    }

    $nombre = filter_var(trim($_POST['nombre_tienda'] ?? ''), FILTER_SANITIZE_STRING);
    $direccion = filter_var(trim($_POST['direccion_tienda'] ?? ''), FILTER_SANITIZE_STRING);
    $telefono = trim($_POST['telefono_tienda'] ?? '');
    $correo = filter_var(trim($_POST['correo_tienda'] ?? ''), FILTER_SANITIZE_EMAIL);

    if (!$nombre || !$direccion || !$telefono || !$correo) {
        echo json_encode(['estado'=>'error','mensaje'=>'Todos los campos son obligatorios']);
        exit();
    }

    $tiendaModel = new Tienda();
    $resultado = $tiendaModel->registrar([
        'nombre_tienda' => $nombre,
        'direccion_tienda' => $direccion,
        'telefono_tienda' => $telefono,
        'correo_tienda' => $correo
    ]);

    $mensajes = [
        'registro_exitoso' => ['estado'=>'success','mensaje'=>'Tienda registrada exitosamente'],
        'nombre_existente' => ['estado'=>'error','mensaje'=>'Ya existe una tienda con ese nombre'],
        'direccion_existente' => ['estado'=>'error','mensaje'=>'Ya existe una tienda con esa dirección'],
        'telefono_existente' => ['estado'=>'error','mensaje'=>'Ya existe una tienda con ese teléfono'],
        'correo_existente' => ['estado'=>'error','mensaje'=>'Ya existe una tienda con ese correo'],
        'nombre_invalido' => ['estado'=>'error','mensaje'=>'Nombre de tienda inválido'],
        'direccion_invalida' => ['estado'=>'error','mensaje'=>'Dirección inválida'],
        'telefono_invalido' => ['estado'=>'error','mensaje'=>'Teléfono inválido'],
        'correo_invalido' => ['estado'=>'error','mensaje'=>'Correo inválido'],
        'error_registro' => ['estado'=>'error','mensaje'=>'Error al registrar la tienda'],
        'error_bd' => ['estado'=>'error','mensaje'=>'Error de base de datos']
    ];

    echo json_encode($mensajes[$resultado] ?? ['estado'=>'error','mensaje'=>'Error desconocido']);
    exit();
}

function tienda_editar() {
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['estado'=>'error','mensaje'=>'Método no permitido']);
        exit();
    }

    $id = (int)($_POST['id_tienda'] ?? 0);
    $nombre = filter_var(trim($_POST['nombre_tienda'] ?? ''), FILTER_SANITIZE_STRING);
    $direccion = filter_var(trim($_POST['direccion_tienda'] ?? ''), FILTER_SANITIZE_STRING);
    $telefono = trim($_POST['telefono_tienda'] ?? '');
    $correo = filter_var(trim($_POST['correo_tienda'] ?? ''), FILTER_SANITIZE_EMAIL);

    if ($id <= 0 || !$nombre || !$direccion || !$telefono || !$correo) {
        echo json_encode(['estado'=>'error','mensaje'=>'Faltan campos obligatorios o ID inválido']);
        exit();
    }

    $tiendaModel = new Tienda();
    $resultado = $tiendaModel->actualizar([
        'id_tienda' => $id,
        'nombre_tienda' => $nombre,
        'direccion_tienda' => $direccion,
        'telefono_tienda' => $telefono,
        'correo_tienda' => $correo
    ]);

    $mensajes = [
        true => ['estado'=>'success','mensaje'=>'Tienda actualizada exitosamente'],
        'nombre_existente' => ['estado'=>'error','mensaje'=>'Ya existe una tienda con ese nombre'],
        'direccion_existente' => ['estado'=>'error','mensaje'=>'Ya existe una tienda con esa dirección'],
        'telefono_existente' => ['estado'=>'error','mensaje'=>'Ya existe un teléfono igual'],
        'correo_existente' => ['estado'=>'error','mensaje'=>'Ya existe un correo igual'],
        'nombre_invalido' => ['estado'=>'error','mensaje'=>'Nombre de tienda inválido'],
        'direccion_invalida' => ['estado'=>'error','mensaje'=>'Dirección inválida'],
        'telefono_invalido' => ['estado'=>'error','mensaje'=>'Teléfono inválido'],
        'correo_invalido' => ['estado'=>'error','mensaje'=>'Correo inválido'],
        'error_actualizar' => ['estado'=>'error','mensaje'=>'Error al actualizar la tienda'],
        'error_bd' => ['estado'=>'error','mensaje'=>'Error de base de datos']
    ];

    echo json_encode($mensajes[$resultado] ?? ['estado'=>'error','mensaje'=>'Error desconocido']);
    exit();
}

function tienda_eliminar() {
    header('Content-Type: application/json');
    $id = (int)($_POST['id_tienda'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['estado'=>'error','mensaje'=>'ID de tienda no proporcionado']);
        exit();
    }

    $tiendaModel = new Tienda();
    $resultado = $tiendaModel->eliminar($id);

    if ($resultado === true) {
        echo json_encode(['estado'=>'success','mensaje'=>'Tienda eliminada correctamente']);
    } elseif (is_string($resultado) && str_starts_with($resultado, 'en_uso:')) {
        $mensaje = str_replace('en_uso:', '', $resultado);
        echo json_encode(['estado'=>'error', 'mensaje'=>$mensaje]);
    } else {
        echo json_encode(['estado'=>'error','mensaje'=>'Error al eliminar la tienda']);
    }
    exit();
}

function tienda_obtenerTienda() {
    header('Content-Type: application/json');
    $id = (int)($_GET['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode([]);
        exit();
    }

    $tiendaModel = new Tienda();
    $tienda = $tiendaModel->obtenerPorId($id);

    echo json_encode($tienda ?: []);
    exit();
}
