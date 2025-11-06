<?php
use RapiExpress\Models\Auth;
use RapiExpress\Helpers\Lang;
use RapiExpress\Traits\ValidationTrait;

class AuthController {
    use ValidationTrait;

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleLoginPost();
            return;
        }

        include __DIR__ . '/../../views/auth/login.php';
    }

    public function handleLoginPost(): void {
        $response = ['success' => false, 'message' => '', 'redirect' => ''];
        $data = $this->sanitize($_POST);

        $rules = [
            'Username' => 'required|username',
            'Password' => 'required'
        ];
        $errors = $this->validate($data, $rules);

        if (!empty($errors)) {
            $response['message'] = implode(', ', array_merge(...array_values($errors)));
        } else {
            try {
                $authModel = new Auth();
                $usuario = $authModel->autenticar($data['Username'], $data['Password']);

                if ($usuario) {
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
            } catch (Exception $e) {
                error_log("Error en login: " . $e->getMessage());
                $response['message'] = Lang::get('system_error');
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    public function recoverPassword() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleRecoverPasswordPost();
            return;
        }

        include __DIR__ . '/../../views/auth/recoverPassword.php';
    }

    public function handleRecoverPasswordPost(): void {
        $response = ['success' => false, 'message' => ''];
        $data = $this->sanitize($_POST);

        $rules = [
            'Username' => 'required|username',
            'Password' => 'required'
        ];
        $errors = $this->validate($data, $rules);

        if (!empty($errors)) {
            $response['message'] = implode(', ', array_merge(...array_values($errors)));
        } else {
            try {
                $authModel = new Auth();
                if (!$authModel->usuarioExiste($data['Username'])) {
                    $response['message'] = Lang::get('user_not_found');
                } elseif ($authModel->actualizarPassword($data['Username'], $data['Password'])) {
                    $response['success'] = true;
                    $response['message'] = Lang::get('password_updated_successfully');
                } else {
                    $response['message'] = Lang::get('error_updating_password');
                }
            } catch (Exception $e) {
                error_log("Error en recuperación: " . $e->getMessage());
                $response['message'] = Lang::get('system_error');
            }
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        exit();
    }

    public function logout(): void {
        session_unset();
        session_destroy();

        if (ob_get_level()) {
            ob_end_clean();
        }

        header('Location: index.php?c=auth&a=login', true, 302);
        exit();
    }
}
