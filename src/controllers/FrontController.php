<?php
namespace RapiExpress\Controllers;

class FrontController
{
    private array $specialControllers = [
        'lang'    => [
            'controller' => 'langController', 
            'methods' => ['cambiar'], 
            'default' => 'cambiar',
            'subfolder' => null
        ],
        'auth'     => [
            'controller' => 'AuthController',
            'methods' => ['login', 'recoverPassword', 'logout'], 
            'default' => 'login',
            'subfolder' => 'Auth'
        ],
        'dashboard'=> [
            'controller' => 'dashboardController', 
            'methods' => ['index','admin','empleado'], 
            'default' => 'index',
            'subfolder' =>'dashboard'
        ],
        'cliente'  => [
            'controller' => 'clienteController', 
            'methods' => ['index', 'registrar', 'editar', 'eliminar', 'obtenerCliente'], 
            'default' => 'index',
            'subfolder' => 'clientes'
        ],
        'tienda'   => [
            'controller' => 'tiendaController', 
            'methods' => ['index', 'registrar', 'editar', 'eliminar', 'obtenerTienda'], 
            'default' => 'index',
            'subfolder' => 'Tiendas'
        ],
        'cargo'    => [
            'controller' => 'cargoController', 
            'methods' => ['index', 'registrar', 'editar', 'eliminar', 'obtenerCargo'], 
            'default' => 'index',
            'subfolder' => 'Cargos'
        ],
        'courier'  => [
            'controller' => 'courierController', 
            'methods' => ['index', 'registrar', 'editar', 'eliminar', 'obtenerCourier'], 
            'default' => 'index',
            'subfolder' => 'Courier'
        ],
        'casillero'=> [
            'controller' => 'casilleroController', 
            'methods' => ['index', 'registrar', 'editar','tabla', 'eliminar', 'obtenerCasillero'], 
            'default' => 'index',
            'subfolder' => 'Casilleros'
        ],
        'categoria'=> [
            'controller' => 'categoriaController', 
            'methods' => ['index', 'registrar', 'editar', 'eliminar',  'obtenerCategoria'], 
            'default' => 'index',
            'subfolder' =>'Categorias'
        ],
        'prealerta' => [
            'controller' =>'prealertaController', 
            'methods' =>  ['index','registrar','editar','eliminar', 'consolidar'], 
            'default' => 'index',
            'subfolder' => 'Paquetes'
        ],
        'paquete' => [
            'controller' => 'paqueteController', 
            'methods' => ['index','registrar','editar','eliminar','imprimirEtiqueta'], 
            'default' => 'index',
            'subfolder' => 'Paquetes'
        ],
        'seguimiento' => [
            'controller' =>'seguimientoController', 
            'methods' => ['index', 'buscar'], 
            'default' => 'index',
            'subfolder' => 'Paquetes'
        ],
        'usuario'  => [
            'controller' => 'usuarioController', 
            'methods' => ['index', 'registrar', 'editar', 'eliminar', 'obtenerUsuario'], 
            'default' => 'index',
            'subfolder' => 'Usuarios'
        ],
        'perfil' => [
            'controller' => 'perfilController',
            'methods' => ['index','actualizar'],
            'default' => 'index',
            'subfolder' => 'Usuarios'
        ],
        'sucursal' => [
            'controller' => 'sucursalController', 
            'methods' => ['index','verificarDuplicado', 'registrar', 'editar', 'eliminar', 'obtenerSucursal'], 
            'default' => 'index',
            'subfolder' => 'Sucursal'
        ],
        'saca'     => [
            'controller' => 'sacaController', 
            'methods' => ['index','registrar','editar','eliminar','obtenerSaca','obtenerDatosImpresion','generarQR',], 
            'default' => 'index',
            'subfolder' => 'sacas'
        ],
        'detallesaca' => [
            'controller' => 'detallesacaController', 
            'methods' => ['index','agregar','quitar'], 
            'default' => 'index',
            'subfolder' =>  'sacas'
        ],
        'pagos'    => [
            'controller' => 'pagosController', 
            'methods' => ['index', 'registrar', 'editar', 'eliminar', 'obtenerCargo'], 
            'default' => 'index',
            'subfolder' => null
        ],
        'manifiesto'  => [
            'controller' => 'manifiestoController', 
            'methods' => ['index', 'generar', 'eliminar', 'obtenerCargo'], 
            'default' => 'index',
            'subfolder' => 'manifiestos'
        ],
        'reportes'    => [
            'controller' => 'cargoController', 
            'methods' => ['index', 'registrar', 'editar', 'eliminar', 'obtenerCargo'], 
            'default' => 'index',
            'subfolder' => 'reportes'
        ],
    ];

    public function handle(string $controllerName, string $action): void
    {
        try {
            if (!array_key_exists($controllerName, $this->specialControllers)) {
                throw new \Exception("Controlador no válido: " . $controllerName);
            }

            $config = $this->specialControllers[$controllerName];
            $methodToCall = in_array($action, $config['methods']) ? $action : $config['default'];
            
            $subfolder = $config['subfolder'] ?? null;
            $controllerFile = __DIR__ . ($subfolder ? "/{$subfolder}/" : '/') . $config['controller'] . ".php";

            if (!file_exists($controllerFile)) {
                $controllerFile = __DIR__ . "/../controllers" . ($subfolder ? "/{$subfolder}/" : '/') . $config['controller'] . ".php";
                if (!file_exists($controllerFile)) {
                    throw new \Exception("Controlador no encontrado: " . $controllerFile);
                }
            }
            require_once $controllerFile;

            $controllerClassName = 'RapiExpress\\Controllers\\' . ($subfolder ? $subfolder . '\\' : '') . $config['controller'];

            if (class_exists($controllerClassName)) {
                $controllerInstance = new $controllerClassName();
                if (method_exists($controllerInstance, $methodToCall)) {
                    $controllerInstance->$methodToCall();
                } else {
                    throw new \Exception("Acción no válida: " . $methodToCall);
                }
            } else {
                $functionName = $controllerName . '_' . $methodToCall;
                if (function_exists($functionName)) {
                    call_user_func($functionName);
                } else {
                    throw new \Exception("Función no encontrada: " . $functionName);
                }
            }
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo "<h2>Error en FrontController</h2>";
            echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            exit;
        }
    }
}
