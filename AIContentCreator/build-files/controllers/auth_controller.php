<?php
// controllers/auth_controller.php

session_start();
require_once __DIR__ . '/../models/user_model.php';

$userModel = new user_model();

// --- FUNCIONES DE VALIDACIÓN ---

function validar_dni($dni)
{
    $dni = strtoupper(trim($dni));
    return preg_match('/^[0-9XYZ][0-9]{7}[A-Z]$/', $dni);
}

function validar_password_fuerte($password, &$errores)
{
    if (strlen($password) < 8) {
        $errores[] = "La contraseña debe tener al menos 8 caracteres";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errores[] = "La contraseña debe incluir al menos una letra mayúscula";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errores[] = "La contraseña debe incluir al menos una letra minúscula";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errores[] = "La contraseña debe incluir al menos un número";
    }
    if (!preg_match('/[\W_]/', $password)) {
        $errores[] = "La contraseña debe incluir al menos un símbolo (ej: !, $, #, %)";
    }
}

// --- ROUTER DEL CONTROLADOR ---

$accion = isset($_GET['action']) ? $_GET['action'] : 'login';

switch ($accion) {

    // LOGIN
    case 'login':
        $error = "";
        $email_form = "";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email_form = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email_form) || !filter_var($email_form, FILTER_VALIDATE_EMAIL)) {
                $error = "Introduce un email válido";
            } elseif (empty($password)) {
                $error = "La contraseña es obligatoria";
            } else {
                $user = $userModel->buscarPorEmail($email_form);

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['usuario'] = $user;
                    header("Location: index.php?controller=home");
                    exit;
                } else {
                    $error = "Email o contraseña incorrectos";
                }
            }
        }

        require __DIR__ . '/../views/login_view.phtml';
        break;

    // REGISTRO
    case 'register':
        $errores = [];

        $dni            = $_POST['dni']            ?? '';
        $nombre         = $_POST['nombre']         ?? '';
        $apellidos      = $_POST['apellidos']      ?? '';
        $numero_empresa = $_POST['numero_empresa'] ?? '';
        $email          = $_POST['email']          ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if (empty($dni) || empty($nombre) || empty($apellidos) || empty($numero_empresa) || empty($email)) {
                $errores[] = "Todos los campos son obligatorios";
            }

            if (!empty($dni) && !validar_dni($dni)) {
                $errores[] = "El DNI no tiene un formato válido";
            }

            if (!empty($numero_empresa) && !filter_var($numero_empresa, FILTER_VALIDATE_INT)) {
                $errores[] = "El número de empresa debe ser un número entero";
            } elseif (!empty($numero_empresa) && (int)$numero_empresa <= 0) {
                $errores[] = "El número de empresa debe ser mayor que 0";
            }

            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El email no tiene un formato válido";
            }

            if ($password !== $confirm) {
                $errores[] = "Las contraseñas no coinciden";
            }

            if (!empty($password)) {
                validar_password_fuerte($password, $errores);
            } else {
                $errores[] = "La contraseña es obligatoria";
            }

            if (!empty($email) && $userModel->buscarPorEmail($email)) {
                $errores[] = "El email ya está registrado";
            }

            if (!empty($dni)) {
                $dniExistente = $userModel->buscarPorDNI($dni);
                if ($dniExistente) {
                    $errores[] = "El DNI ya está registrado";
                }
            }

            if (empty($errores)) {
                $resultado = $userModel->registrar(
                    $dni,
                    $nombre,
                    $apellidos,
                    $numero_empresa,
                    $email,
                    $password
                );

                if ($resultado === true) {
                    header("Location: index.php?controller=auth&action=login");
                    exit;
                } elseif ($resultado === "duplicate") {
                    $errores[] = "El DNI o el email ya está registrado";
                } else {
                    $errores[] = "Error al registrar el usuario";
                }
            }
        }

        require __DIR__ . '/../views/register_view.phtml';
        break;

    // LOGOUT
    case 'logout':
        session_destroy();
        header("Location: index.php?controller=start");
        exit;

    default:
        header("Location: index.php?controller=auth&action=login");
        exit;
}
