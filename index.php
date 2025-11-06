<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

use RapiExpress\Controllers\FrontController;
use RapiExpress\Helpers\Lang;

require_once __DIR__ . '/src/helpers/lang.php';

Lang::init();

$c = preg_replace('/[^a-z]/', '', strtolower($_GET['c'] ?? 'auth'));
$a = preg_replace('/[^a-zA-Z]/', '', ($_GET['a'] ?? 'login'));


    $frontController = new FrontController();
    $frontController->handle($c, $a);

