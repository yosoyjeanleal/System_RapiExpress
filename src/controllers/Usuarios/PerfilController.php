<?php
// src/controllers/Perfil/perfilController.php

use RapiExpress\Models\Usuario;
use RapiExpress\Models\Sucursal;
use RapiExpress\Models\Cargo;

function perfil_index() {
    if (!isset($_SESSION['ID_Usuario'])) {
        header('Location: index.php?c=auth&a=login');
        exit;
    }

    $usuarioModel = new Usuario();
    $sucursalModel = new Sucursal();
    $cargoModel = new Cargo();

    $usuario = $usuarioModel->obtenerPorId($_SESSION['ID_Usuario']);
    $imagenes = $usuarioModel->obtenerTodasImagenes();
    $sucursales = $sucursalModel->obtenerTodas();
    $cargos = $cargoModel->obtenerTodos();
    $usuarios = $usuarioModel->obtenerTodos();

    if (!$usuario) {
        $_SESSION['mensaje'] = 'Usuario no encontrado';
        $_SESSION['tipo_mensaje'] = 'error';
        header('Location: index.php?c=dashboard&a=index');
        exit;
    }

    // Asegurar que tenga imagen por defecto si no tiene ninguna
    if (!isset($usuario['imagen_archivo']) || empty($usuario['imagen_archivo'])) {
        $usuario['imagen_archivo'] = 'default.png';
    }

    include __DIR__ . '/../../views/usuario/perfil.php';
}
function actualizarSesion() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_completo'])) {
        session_start();
        $_SESSION['nombre_completo'] = trim($_POST['nombre_completo']);
        echo json_encode(['estado' => 'ok']);
    } else {
        echo json_encode(['estado' => 'error']);
    }
    exit;
}


function perfil_actualizar() {
    header('Content-Type: application/json; charset=utf-8');
    
    try {
        // Validar sesión
        if (!isset($_SESSION['ID_Usuario'])) {
            echo json_encode([
                'estado' => 'error', 
                'mensaje' => 'Sesión no válida. Por favor, inicie sesión nuevamente.'
            ]);
            exit;
        }

        $usuarioModel = new Usuario();
        $ID_Imagen = null;

        // PRIORIDAD 1: Si se subió un archivo nuevo, procesarlo
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $res = $usuarioModel->subirImagenPerfil($_FILES['imagen']);
            
            if ($res['estado'] === 'success') {
                $ID_Imagen = $res['ID_Imagen'];
                
                // Actualizar sesión con nueva imagen
                $nombreArchivo = $usuarioModel->obtenerNombreArchivoPorIdImagen($ID_Imagen);
                if ($nombreArchivo) {
                    $_SESSION['imagen_usuario'] = $nombreArchivo;
                }
            } else {
                echo json_encode($res);
                exit;
            }
        }
        // PRIORIDAD 2: Si no hay archivo nuevo, usar imagen seleccionada del select
        elseif (isset($_POST['ID_Imagen']) && !empty($_POST['ID_Imagen'])) {
            $ID_Imagen = intval($_POST['ID_Imagen']);
        }
        // PRIORIDAD 3: Mantener la imagen actual si no se cambió nada
        else {
            $usuarioActual = $usuarioModel->obtenerPorId($_SESSION['ID_Usuario']);
            $ID_Imagen = $usuarioActual['ID_Imagen'] ?? null;
        }

        // Preparar datos para actualizar
        $data = [
            'Nombres_Usuario' => trim($_POST['Nombres_Usuario'] ?? ''),
            'Apellidos_Usuario' => trim($_POST['Apellidos_Usuario'] ?? ''),
            'Telefono_Usuario' => trim($_POST['Telefono_Usuario'] ?? ''),
            'Correo_Usuario' => trim($_POST['Correo_Usuario'] ?? ''),
            'Direccion_Usuario' => trim($_POST['Direccion_Usuario'] ?? ''),
            'ID_Imagen' => $ID_Imagen
        ];

        // Validaciones del lado del servidor
        $errores = [];

        // Validar nombres (obligatorio)
        if (empty($data['Nombres_Usuario'])) {
            $errores[] = 'El campo Nombres es obligatorio';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/u', $data['Nombres_Usuario'])) {
            $errores[] = 'Los Nombres solo pueden contener letras y espacios (2-50 caracteres)';
        }

        // Validar apellidos (obligatorio)
        if (empty($data['Apellidos_Usuario'])) {
            $errores[] = 'El campo Apellidos es obligatorio';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{2,50}$/u', $data['Apellidos_Usuario'])) {
            $errores[] = 'Los Apellidos solo pueden contener letras y espacios (2-50 caracteres)';
        }

        // Validar email (opcional pero debe ser válido si se proporciona)
        if (!empty($data['Correo_Usuario'])) {
            if (!filter_var($data['Correo_Usuario'], FILTER_VALIDATE_EMAIL)) {
                $errores[] = 'El formato del correo electrónico no es válido';
            } elseif (strlen($data['Correo_Usuario']) > 100) {
                $errores[] = 'El correo electrónico no puede exceder 100 caracteres';
            }
        }

        // Validar teléfono (opcional pero debe ser válido si se proporciona)
        if (!empty($data['Telefono_Usuario'])) {
            if (!preg_match('/^\d{7,15}$/', $data['Telefono_Usuario'])) {
                $errores[] = 'El teléfono debe contener solo números (7-15 dígitos)';
            }
        }

        // Validar dirección (opcional pero con límite de caracteres)
        if (!empty($data['Direccion_Usuario']) && strlen($data['Direccion_Usuario']) > 255) {
            $errores[] = 'La dirección no puede exceder 255 caracteres';
        }

        // Si hay errores, retornarlos
        if (!empty($errores)) {
            echo json_encode([
                'estado' => 'error',
                'mensaje' => implode('<br>', $errores)
            ]);
            exit;
        }

        // Actualizar perfil
        $res = $usuarioModel->actualizarPerfil($_SESSION['ID_Usuario'], $data);
        
        // Actualizar datos de sesión si fue exitoso
        if ($res['estado'] === 'success') {
            $_SESSION['Nombres_Usuario'] = $data['Nombres_Usuario'];
            $_SESSION['Apellidos_Usuario'] = $data['Apellidos_Usuario'];
            
            if ($ID_Imagen) {
                $_SESSION['ID_Imagen'] = $ID_Imagen;
            }
        }
        
        echo json_encode($res);
        
    } catch (Exception $e) {
        // Manejo de errores inesperados
        error_log("Error en perfil_actualizar: " . $e->getMessage());
        
        echo json_encode([
            'estado' => 'error',
            'mensaje' => 'Ocurrió un error inesperado. Por favor, intente nuevamente.'
        ]);
    }
    
    exit;
}