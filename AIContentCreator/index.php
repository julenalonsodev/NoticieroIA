<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar conexión BD
require_once "db/db.php";

// Router básico
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'start';

switch ($controller) {
    case 'start':
        require_once "controllers/start_controller.php";
        break;

    case 'home':
        require_once "controllers/home_controller.php";
        break;

    case 'auth':
        require_once "controllers/auth_controller.php";
        break;

    default:
        require_once "controllers/start_controller.php";
        break;
}
