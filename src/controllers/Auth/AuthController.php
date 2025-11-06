<?php
use RapiExpress\Models\Auth;
use RapiExpress\Helpers\Lang;

/**
 * Maneja el inicio de sesión
 */
function auth_login() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handleLoginPost();
        return;
    }

    include __DIR__ . '/../../views/auth/login.php';
}

/**
 * Procesa el POST de login
 */
function handleLoginPost(): void {
    $response = ['success' => false, 'message' => '', 'redirect' => ''];

    try {
        $username = trim($_POST['Username'] ?? '');
        $password = trim($_POST['Password'] ?? '');

        $authModel = new Auth();

        // Validaciones
        if (empty($username) || empty($password)) {
            $response['message'] = Lang::get('complete_all_fields');
        } elseif (!$authModel->validarUsername($username)) {
            $response['message'] = Lang::get('invalid_username');
        } elseif (!$authModel->validarPassword($password)) {
            $response['message'] = Lang::get('invalid_password_format');
        } else {
            $usuario = $authModel->autenticar($username, $password);
            
            if ($usuario) {
                // Guardar sesión
                $_SESSION['ID_Usuario']      = (int)$usuario['ID_Usuario'];
                $_SESSION['usuario']         = $usuario['Username'];
                $_SESSION['nombre_completo'] = $usuario['Nombres_Usuario'] . ' ' . $usuario['Apellidos_Usuario'];
                $_SESSION['ID_Cargo']        = (int)$usuario['ID_Cargo'];
                $_SESSION['imagen_usuario']  = !empty($usuario['imagen_archivo']) ? $usuario['imagen_archivo'] : 'default.png';

                $response['success'] = true;
                $response['message'] = Lang::get('login_success');
                $response['redirect'] = $_SESSION['ID_Cargo'] === 1 
                    ? 'index.php?c=dashboard&a=admin' 
                    : 'index.php?c=dashboard&a=empleado';
            } else {
                $response['message'] = Lang::get('invalid_credentials');
            }
        }
    } catch (Exception $e) {
        error_log("Error en login: " . $e->getMessage());
        $response['message'] = Lang::get('system_error');
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

/**
 * Maneja la recuperación de contraseña
 */
function auth_recoverPassword() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handleRecoverPasswordPost();
        return;
    }

    include __DIR__ . '/../../views/auth/recoverpassword.php';
}

/**
 * Procesa el POST de recuperación de contraseña
 */
function handleRecoverPasswordPost(): void {
    $response = ['success' => false, 'message' => ''];

    try {
        $username = trim($_POST['Username'] ?? '');
        $newPassword = trim($_POST['Password'] ?? '');
        $authModel = new Auth();

        // Validaciones
        if (empty($username) || empty($newPassword)) {
            $response['message'] = Lang::get('complete_all_fields');
        } elseif (!$authModel->validarUsername($username)) {
            $response['message'] = Lang::get('invalid_username');
        } elseif (!$authModel->validarPassword($newPassword)) {
            $response['message'] = Lang::get('invalid_password_format');
        } else {
            if (!$authModel->usuarioExiste($username)) {
                $response['message'] = Lang::get('user_not_found');
            } elseif ($authModel->actualizarPassword($username, $newPassword)) {
                $response['success'] = true;
                $response['message'] = Lang::get('password_updated_successfully');
            } else {
                $response['message'] = Lang::get('error_updating_password');
            }
        }
    } catch (Exception $e) {
        error_log("Error en recuperación: " . $e->getMessage());
        $response['message'] = Lang::get('system_error');
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

/**
 * Cierra la sesión del usuario
 */
function auth_logout(): void {
    session_unset();
    session_destroy();
    
    if (ob_get_level()) {
        ob_end_clean();
    }
    
    header('Location: index.php?c=auth&a=login', true, 302);
    exit();
}