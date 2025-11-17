<?php
session_start();

// Importamos el modelo con ruta relativa (sin ROOT)
require_once "models/user_model.php";

$userModel = new user_model();

$accion = isset($_GET['action']) ? $_GET['action'] : 'login';

switch ($accion) {

    case 'login':
        $error = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email    = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $userModel->buscarPorEmail($email);

            if ($user && password_verify($password, $user['password'])) {
                // Guardamos todos los datos en sesión
                $_SESSION['usuario'] = $user;
                header("Location: index.php?controller=home");
                exit;
            } else {
                $error = "Email o contraseña incorrectos";
            }
        }

        // Cargamos la vista de login
        require "views/login_view.phtml";
    break;

    case 'register':
        $errores = [];

        // valores por defecto para rellenar el formulario si hay errores
        $dni            = $_POST['dni']            ?? '';
        $nombre         = $_POST['nombre']         ?? '';
        $apellidos      = $_POST['apellidos']      ?? '';
        $numero_empresa = $_POST['numero_empresa'] ?? '';
        $email          = $_POST['email']          ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if ($password !== $confirm) {
                $errores[] = "Las contraseñas no coinciden";
            }

            if ($userModel->buscarPorEmail($email)) {
                $errores[] = "El email ya está registrado";
            }

            if (empty($dni) || empty($nombre) || empty($apellidos) || empty($numero_empresa) || empty($email)) {
                $errores[] = "Todos los campos son obligatorios";
            }

            if (empty($errores)) {
                if ($userModel->registrar($dni, $nombre, $apellidos, $numero_empresa, $email, $password)) {
                    header("Location: index.php?controller=auth&action=login");
                    exit;
                } else {
                    $errores[] = "Error al registrar el usuario";
                }
            }
        }

        // Cargamos la vista de registro
        require "views/register_view.phtml";
    break;

    case 'logout':
        session_destroy();
        header("Location: index.php?controller=home");
        exit;

    default:
        header("Location: index.php?controller=auth&action=login");
        exit;
}
