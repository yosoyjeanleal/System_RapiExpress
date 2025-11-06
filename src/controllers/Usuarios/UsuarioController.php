<?php
// src/controllers/Usuarios/usuarioController.php

use RapiExpress\Models\Usuario;

function usuario_index() {
    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php?c=auth&a=login');
        exit();
    }
    

    $sucursalModel = new \RapiExpress\Models\Sucursal();
    $cargoModel = new \RapiExpress\Models\Cargo();
    $usuarioModel = new Usuario();

$usuarios = $usuarioModel->obtenerTodos();
    $sucursales = $sucursalModel->obtenerTodas();
    $cargos = $cargoModel->obtenerTodos();
    
    
    
    include __DIR__ . '/../../views/usuario/usuario.php';
}

function usuario_registrar() {
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'No autorizado']);
        exit();
    }
    
    $usuarioModel = new Usuario();

    $data = [
        'Cedula_Identidad' => trim($_POST['Cedula_Identidad'] ?? ''),
        'Nombres_Usuario' => trim($_POST['Nombres_Usuario'] ?? ''),
        'Apellidos_Usuario' => trim($_POST['Apellidos_Usuario'] ?? ''),
        'Username' => trim($_POST['Username'] ?? ''),
        'Password' => $_POST['Password'] ?? '',
        'Telefono_Usuario' => trim($_POST['Telefono_Usuario'] ?? ''),
        'Correo_Usuario' => trim($_POST['Correo_Usuario'] ?? ''),
        'Direccion_Usuario' => trim($_POST['Direccion_Usuario'] ?? ''),
        'ID_Sucursal' => intval($_POST['ID_Sucursal'] ?? 0),
        'ID_Cargo' => intval($_POST['ID_Cargo'] ?? 0),
        'ID_Imagen' => !empty($_POST['ID_Imagen']) ? intval($_POST['ID_Imagen']) : null
    ];

    // Validaciones básicas
    if (empty($data['Cedula_Identidad']) || empty($data['Username']) || 
        empty($data['Nombres_Usuario']) || empty($data['Password'])) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Campos obligatorios incompletos']);
        exit();
    }

    $result = $usuarioModel->registrar($data);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}

function usuario_editar() {
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'No autorizado']);
        exit();
    }

    $usuarioModel = new Usuario();

    $data = [
        'ID_Usuario' => intval($_POST['ID_Usuario'] ?? 0),
        'Cedula_Identidad' => trim($_POST['Cedula_Identidad'] ?? ''),
        'Username' => trim($_POST['Username'] ?? ''),
        'Nombres_Usuario' => trim($_POST['Nombres_Usuario'] ?? ''),
        'Apellidos_Usuario' => trim($_POST['Apellidos_Usuario'] ?? ''),
        'Correo_Usuario' => trim($_POST['Correo_Usuario'] ?? ''),
        'Telefono_Usuario' => trim($_POST['Telefono_Usuario'] ?? ''),
        'ID_Sucursal' => intval($_POST['ID_Sucursal'] ?? 0),
        'ID_Cargo' => intval($_POST['ID_Cargo'] ?? 0),
        'Direccion_Usuario' => trim($_POST['Direccion_Usuario'] ?? '')
    ];

    if ($data['ID_Usuario'] <= 0) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'ID de usuario inválido']);
        exit();
    }

    $resultado = $usuarioModel->actualizar($data);
    header('Content-Type: application/json');
    echo json_encode($resultado);
    exit();
}

function usuario_eliminar() {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['ID_Usuario'])) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'No autorizado']);
        exit();
    }
    
    $usuarioModel = new Usuario();
    $id = intval($_POST['ID_Usuario'] ?? 0);
    
    if ($id <= 0) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'ID inválido']);
        exit();
    }
    
    // No permitir eliminar el usuario actual
    if ($id == $_SESSION['ID_Usuario']) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'No puedes eliminar tu propio usuario']);
        exit();
    }
    
    $result = $usuarioModel->eliminar($id);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}

function usuario_obtenerUsuario() {
    if (!isset($_SESSION['usuario'])) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'No autorizado']);
        exit();
    }

    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(['estado' => 'error', 'mensaje' => 'ID inválido']);
        exit();
    }

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->obtenerPorId($id);

    if ($usuario) {
        echo json_encode(['estado' => 'success', 'usuario' => $usuario]);
    } else {
        echo json_encode(['estado' => 'error', 'mensaje' => 'Usuario no encontrado']);
    }
    exit();
}