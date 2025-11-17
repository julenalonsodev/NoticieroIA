<?php
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Cargar la conexión (la usará el modelo)
require_once "db/db.php";

// Router muy simple
$controller = isset($_GET['controller']) ? $_GET['controller'] : 'home';

switch ($controller) {
    case 'home':
        require_once "controllers/home_controller.php";
        break;

    case 'auth':
        require_once "controllers/auth_controller.php";
        break;

    default:
        echo "Controlador no encontrado";
        break;
}
