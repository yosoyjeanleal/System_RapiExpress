<?php
// src/controllers/dashboardController.php

use RapiExpress\models\Usuario;
use RapiExpress\models\Cliente;
use RapiExpress\models\Tienda;
use RapiExpress\models\Courier;
use RapiExpress\models\Cargo;
use RapiExpress\models\Sucursal;
use RapiExpress\Helpers\Url;
use RapiExpress\Helpers\Lang;

/**
 * Función auxiliar para obtener todos los usuarios
 */
function obtenerTodosUsuarios() {
    try {
        $usuarioModel = new Usuario();
        return $usuarioModel->obtenerTodos();
    } catch (\Exception $e) {
        error_log("Error obteniendo usuarios: " . $e->getMessage());
        return [];
    }
}

/**
 * Función auxiliar para verificar autenticación
 */
function verificarAutenticacion() {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['ID_Cargo'])) {
        error_log("No hay sesión activa, redirigiendo a login");
        Url::redirect('auth', 'login');
    }
}

/**
 * Función principal del dashboard: redirige según el rol
 */
function dashboard_index() {
    error_log("=== Ejecutando dashboard_index ===");
    
    verificarAutenticacion();

    // Determinar el rol del usuario y redirigir directamente
    if ((int)$_SESSION['ID_Cargo'] === 1) {
        error_log("Usuario es admin, mostrando dashboard admin");
        dashboard_admin();
    } else {
        error_log("Usuario es empleado, mostrando dashboard empleado");
        dashboard_empleado();
    }
}

/**
 * Dashboard del administrador
 */
function dashboard_admin() {
    error_log("=== Ejecutando dashboard_admin ===");
    
    verificarAutenticacion();

    // Verificar que sea admin
    if ((int)$_SESSION['ID_Cargo'] !== 1) {
        error_log("Usuario no es admin, redirigiendo a dashboard empleado");
        Url::redirect('dashboard', 'empleado');
    }

    try {
        // Obtener usuarios
        $usuarios = obtenerTodosUsuarios();
        $totalUsuarios = count($usuarios);
        error_log("Total usuarios obtenidos: $totalUsuarios");

        // Obtener clientes
        $clienteModel = new Cliente();
        $clientes = $clienteModel->obtenerTodos();
        $totalClientes = count($clientes);
        error_log("Total clientes obtenidos: $totalClientes");

        // Obtener tiendas
        $tiendaModel = new Tienda();
        $tiendas = $tiendaModel->obtenerTodas();
        $totalTiendas = count($tiendas);
        error_log("Total tiendas obtenidas: $totalTiendas");

        // Obtener cargos
        $cargoModel = new Cargo();
        $cargos = $cargoModel->obtenerTodos();
        error_log("Total cargos obtenidos: " . count($cargos));

        // Obtener couriers
        $courierModel = new Courier();
        $couriers = $courierModel->obtenerTodos();
        $totalCouriers = count($couriers);
        error_log("Total couriers obtenidos: $totalCouriers");

        // Inicializar variables adicionales para evitar warnings
        $totalPaquetes = 0;
        $totalReportes = 0;

        error_log("Mostrando vista dashboard admin");
        include __DIR__ . '/../../views/dashboard/dashboard.php';

    } catch (\Throwable $e) {
        error_log("Error en Dashboard Admin: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        // Mostrar error amigable
        mostrarErrorDashboard($e);
    }
}

/**
 * Dashboard del empleado
 */
function dashboard_empleado() {
    error_log("=== Ejecutando dashboard_empleado ===");
    
    verificarAutenticacion();

    // Verificar que NO sea admin (empleados tienen ID_Cargo !== 1)
    if ((int)$_SESSION['ID_Cargo'] === 1) {
        error_log("Usuario es admin, redirigiendo a dashboard admin");
        Url::redirect('dashboard', 'admin');
    }

    try {
        // Obtener usuarios
        $usuarios = obtenerTodosUsuarios();
        $totalUsuarios = count($usuarios);
        
        // Obtener clientes
        $clienteModel = new Cliente();
        $clientes = $clienteModel->obtenerTodos();
        $totalClientes = count($clientes);

        // Obtener tiendas
        $tiendaModel = new Tienda();
        $tiendas = $tiendaModel->obtenerTodas();
        $totalTiendas = count($tiendas);

        // Obtener couriers
        $courierModel = new Courier();
        $couriers = $courierModel->obtenerTodos();
        $totalCouriers = count($couriers);

        // Inicializar variables adicionales
        $totalPaquetes = 0;
        $totalReportes = 0;

        error_log("Mostrando vista dashboard empleado");
        include __DIR__ . '/../../views/dashboard/dashboard_empleado.php';

    } catch (\Throwable $e) {
        error_log("Error en Dashboard Empleado: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        
        mostrarErrorDashboard($e);
    }
}

/**
 * Muestra un error amigable del dashboard
 */
function mostrarErrorDashboard(\Throwable $e) {
    ob_clean();
    ?>
    <!DOCTYPE html>
    <html lang="<?= Lang::current() ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= Lang::get('error') ?> - Dashboard</title>
        <link rel="stylesheet" href="<?= Url::asset('Temple/vendors/styles/core.css') ?>">
        <style>
            .error-container {
                max-width: 800px;
                margin: 50px auto;
                padding: 30px;
                background: #fff;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .error-icon {
                font-size: 64px;
                color: #dc3545;
                text-align: center;
                margin-bottom: 20px;
            }
            .error-title {
                color: #dc3545;
                text-align: center;
                margin-bottom: 20px;
            }
            .error-details {
                background: #f8f9fa;
                padding: 15px;
                border-left: 4px solid #dc3545;
                border-radius: 4px;
                margin: 20px 0;
                font-family: monospace;
                font-size: 13px;
            }
            .error-actions {
                text-align: center;
                margin-top: 30px;
            }
            .btn {
                display: inline-block;
                padding: 10px 30px;
                margin: 0 10px;
                background: #007bff;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                transition: all 0.3s;
            }
            .btn:hover {
                background: #0056b3;
                transform: translateY(-2px);
            }
            .btn-secondary {
                background: #6c757d;
            }
            .btn-secondary:hover {
                background: #545b62;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">⚠️</div>
            <h1 class="error-title"><?= Lang::get('dashboard_error') ?? 'Error en Dashboard' ?></h1>
            
            <p style="text-align: center; color: #666; margin-bottom: 30px;">
                <?= Lang::get('dashboard_error_message') ?? 'Ha ocurrido un error al cargar el dashboard.' ?>
            </p>
            
            <?php if (ini_get('display_errors')): ?>
            <div class="error-details">
                <strong><?= Lang::get('error') ?>:</strong> <?= htmlspecialchars($e->getMessage()) ?><br>
                <strong><?= Lang::get('file') ?>:</strong> <?= htmlspecialchars($e->getFile()) ?><br>
                <strong><?= Lang::get('line') ?>:</strong> <?= $e->getLine() ?>
            </div>
            <?php endif; ?>
            
            <div class="error-actions">
                <a href="<?= Url::to('dashboard', 'index') ?>" class="btn">
                    <?= Lang::get('try_again') ?? 'Intentar nuevamente' ?>
                </a>
                <a href="<?= Url::toPublic('auth', 'logout') ?>" class="btn btn-secondary">
                    <?= Lang::get('logout') ?? 'Cerrar sesión' ?>
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}