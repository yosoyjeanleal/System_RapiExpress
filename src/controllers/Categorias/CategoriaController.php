<?php
use RapiExpress\Models\Categoria;

function categoria_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $categoriaModel = new Categoria();
    $categorias = $categoriaModel->obtenerTodos();
    include __DIR__ . '/../../views/categoria/categoria.php';
}

function categoria_registrar() {
    header('Content-Type: application/json');
    if (!isset($_SESSION['usuario'])) exit(json_encode(['success'=>false,'mensaje'=>'No autorizado']));

    $categoriaModel = new Categoria();

    $data = [
        'nombre'   => trim($_POST['nombre'] ?? ''),
        'altura'   => floatval($_POST['altura'] ?? 0),
        'largo'    => floatval($_POST['largo'] ?? 0),
        'ancho'    => floatval($_POST['ancho'] ?? 0),
        'peso'     => floatval($_POST['peso'] ?? 0),
        'piezas'   => intval($_POST['piezas'] ?? 0),
        'precio'   => floatval($_POST['precio'] ?? 0)
    ];

    $resultado = $categoriaModel->registrar($data);
    echo json_encode($resultado);
    exit();
}

function categoria_editar() {
    header('Content-Type: application/json');
    if (!isset($_SESSION['usuario'])) exit(json_encode(['success'=>false,'mensaje'=>'No autorizado']));

    $categoriaModel = new Categoria();

    $data = [
        'ID_Categoria' => intval($_POST['ID_Categoria'] ?? 0),
        'nombre'       => trim($_POST['nombre'] ?? ''),
        'altura'       => floatval($_POST['altura'] ?? 0),
        'largo'        => floatval($_POST['largo'] ?? 0),
        'ancho'        => floatval($_POST['ancho'] ?? 0),
        'peso'         => floatval($_POST['peso'] ?? 0),
        'piezas'       => intval($_POST['piezas'] ?? 0),
        'precio'       => floatval($_POST['precio'] ?? 0)
    ];

    $resultado = $categoriaModel->actualizar($data);
    echo json_encode($resultado);
    exit();
}

function categoria_eliminar() {
    header('Content-Type: application/json');
    if (!isset($_SESSION['usuario'])) exit(json_encode(['success'=>false,'mensaje'=>'No autorizado']));

    $categoriaModel = new Categoria();
    $id = intval($_POST['id'] ?? 0);

    $resultado = $categoriaModel->eliminar($id);
    echo json_encode($resultado);
    exit();
}

function categoria_obtener() {
    header('Content-Type: application/json');
    if (!isset($_SESSION['usuario'])) exit(json_encode(['success'=>false,'mensaje'=>'No autorizado']));

    $categoriaModel = new Categoria();
    $id = intval($_GET['id'] ?? 0);
    if (!$id) exit(json_encode(['success'=>false,'mensaje'=>'ID no proporcionado']));

    $categoria = $categoriaModel->obtenerPorId($id);
    if ($categoria) echo json_encode($categoria);
    else echo json_encode(['success'=>false,'mensaje'=>'Categoría no encontrada']);
    exit();
}
