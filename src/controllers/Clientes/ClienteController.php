<?php
use RapiExpress\Models\Cliente;
use RapiExpress\Models\Sucursal;
use RapiExpress\Models\Casillero;

function cliente_index() {
    $clienteModel = new Cliente();
    $sucursalModel = new Sucursal();
    $casilleroModel = new Casillero();

    if (!isset($_SESSION['usuario'])) {
        header('Location: index.php');
        exit();
    }

    $clientes = $clienteModel->obtenerTodos();
    $sucursales = $sucursalModel->obtenerTodas();
    $casilleros = $casilleroModel->obtenerTodos();
    include __DIR__ . '/../../views/cliente/cliente.php';
}


// ✅ Registrar nuevo cliente
function cliente_registrar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $clienteModel = new Cliente();

        $data = [
            'ID_Cliente'       => null, // se asume auto_increment
            'Cedula_Identidad' => trim($_POST['Cedula_Identidad']),
            'Nombres_Cliente'  => trim($_POST['Nombres_Cliente']),
            'Apellidos_Cliente'=> trim($_POST['Apellidos_Cliente']),
            'Direccion_Cliente'=> trim($_POST['Direccion_Cliente']),
            'Telefono_Cliente' => trim($_POST['Telefono_Cliente']),
            'Correo_Cliente'   => trim($_POST['Correo_Cliente']),
            'ID_Sucursal'      => intval($_POST['ID_Sucursal']),
            'ID_Casillero'     => intval($_POST['ID_Casillero'])
        ];

        $resultado = $clienteModel->registrar($data);

        switch ($resultado) {
            case 'registro_exitoso':
                $respuesta = ['estado' => 'success', 'mensaje' => 'Cliente registrado exitosamente.'];
                break;
            case 'cedula_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'La cédula ya está registrada.'];
                break;
            case 'telefono_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El teléfono ya está registrado.'];
                break;
            case 'correo_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El correo ya está registrado.'];
                break;
            case 'error_bd':
                $respuesta = ['estado' => 'error', 'mensaje' => 'Error de base de datos.'];
                break;
            default:
                $respuesta = ['estado' => 'error', 'mensaje' => 'Error al registrar el cliente.'];
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
}

// ✅ Editar cliente existente
function cliente_editar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $clienteModel = new Cliente();

        $data = [
            'ID_Cliente'       => intval($_POST['ID_Cliente']),
            'Cedula_Identidad' => trim($_POST['Cedula_Identidad']),
            'Nombres_Cliente'  => trim($_POST['Nombres_Cliente']),
            'Apellidos_Cliente'=> trim($_POST['Apellidos_Cliente']),
            'Direccion_Cliente'=> trim($_POST['Direccion_Cliente']),
            'Telefono_Cliente' => trim($_POST['Telefono_Cliente']),
            'Correo_Cliente'   => trim($_POST['Correo_Cliente']),
            'ID_Sucursal'      => intval($_POST['ID_Sucursal']),
            'ID_Casillero'     => intval($_POST['ID_Casillero'])
        ];

        $resultado = $clienteModel->actualizar($data);

        switch ($resultado) {
            case true:
                $respuesta = ['estado' => 'success', 'mensaje' => 'Cliente actualizado correctamente.'];
                break;
            case 'cedula_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'La cédula ya pertenece a otro cliente.'];
                break;
            case 'telefono_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El teléfono ya pertenece a otro cliente.'];
                break;
            case 'correo_existente':
                $respuesta = ['estado' => 'error', 'mensaje' => 'El correo ya pertenece a otro cliente.'];
                break;
            case false:
            default:
                $respuesta = ['estado' => 'error', 'mensaje' => 'Error al actualizar el cliente.'];
                break;
        }

        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
}

// ✅ Eliminar cliente
function cliente_eliminar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id']);
        $clienteModel = new Cliente();

$resultado = $clienteModel->eliminar($id);

switch($resultado){
    case 'eliminacion_exitosa':
        $respuesta = ['estado'=>'success','mensaje'=>'Cliente eliminado correctamente.'];
        break;
    case 'cliente_relacionado_paquete':
        $respuesta = ['estado'=>'error','mensaje'=>'No se puede eliminar: Cliente tiene paquetes asociados.'];
        break;
    case 'cliente_relacionado_prealerta':
        $respuesta = ['estado'=>'error','mensaje'=>'No se puede eliminar: Cliente tiene prealertas asociadas.'];
        break;
    case 'cliente_relacionado_seguimiento':
        $respuesta = ['estado'=>'error','mensaje'=>'No se puede eliminar: Cliente tiene seguimientos asociados.'];
        break;
    default:
        $respuesta = ['estado'=>'error','mensaje'=>'Error al eliminar cliente.'];
}


        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit();
    }
}

// ✅ Obtener cliente por ID (para AJAX / Modal)
function cliente_obtenerCliente() {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $clienteModel = new Cliente();
        $cliente = $clienteModel->obtenerPorId($id);

        header('Content-Type: application/json');
        echo json_encode($cliente ?: ['error' => 'Cliente no encontrado.']);
        exit();
    }
}