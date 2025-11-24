<?php
session_start();
require_once "models/user_model.php";

$userModel = new user_model();

// --- FUNCIONES DE VALIDACIÓN ---

function validar_dni($dni)
{
    $dni = strtoupper(trim($dni));
    // Formato básico DNI/NIE: 8 números + letra, o X/Y/Z + 7 números + letra
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

            // Validaciones básicas
            if (empty($email_form) || !filter_var($email_form, FILTER_VALIDATE_EMAIL)) {
                $error = "Introduce un email válido";
            } elseif (empty($password)) {
                $error = "La contraseña es obligatoria";
            } else {
                // Si pasa las validaciones, buscamos el usuario
                $user = $userModel->buscarPorEmail($email_form);

                if ($user && password_verify($password, $user['password'])) {
                    // Guardamos todo el documento en sesión (incluido _id de Mongo)
                    $_SESSION['usuario'] = $user;

                    // Recorrido: login -> home
                    header("Location: index.php?controller=home");
                    exit;
                } else {
                    $error = "Email o contraseña incorrectos";
                }
            }
        }

        require "views/login_view.phtml";
        break;

    // REGISTRO
    case 'register':
        $errores = [];

        // Valores por defecto para repintar el formulario
        $dni = $_POST['dni'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $numero_empresa = $_POST['numero_empresa'] ?? '';
        $email = $_POST['email'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm_password'] ?? '';

            // 1. Campos obligatorios
            if (empty($dni) || empty($nombre) || empty($apellidos) || empty($numero_empresa) || empty($email)) {
                $errores[] = "Todos los campos son obligatorios";
            }

            // 2. DNI formato
            if (!empty($dni) && !validar_dni($dni)) {
                $errores[] = "El DNI no tiene un formato válido";
            }

            // 3. Número de empresa
            if (!empty($numero_empresa) && !filter_var($numero_empresa, FILTER_VALIDATE_INT)) {
                $errores[] = "El número de empresa debe ser un número entero";
            } elseif (!empty($numero_empresa) && (int) $numero_empresa <= 0) {
                $errores[] = "El número de empresa debe ser mayor que 0";
            }

            // 4. Email formato
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errores[] = "El email no tiene un formato válido";
            }

            // 5. Contraseñas
            if ($password !== $confirm) {
                $errores[] = "Las contraseñas no coinciden";
            }

            if (!empty($password)) {
                validar_password_fuerte($password, $errores);
            } else {
                $errores[] = "La contraseña es obligatoria";
            }

            // 6. Email ya registrado
            if (!empty($email) && $userModel->buscarPorEmail($email)) {
                $errores[] = "El email ya está registrado";
            }

            // 7. DNI ya registrado
            if (!empty($dni)) {
                $dniExistente = $userModel->buscarPorDNI($dni);
                if ($dniExistente) {
                    $errores[] = "El DNI ya está registrado";
                }
            }

            // Si no hay errores, intentamos registrar
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

                    // --- ENVIAR DATOS A N8N ---
                    $data = [
                        'dni' => $dni,
                        'nombre' => $nombre,
                        'apellidos' => $apellidos,
                        'numero_empresa' => $numero_empresa,
                        'email' => $email,
                        // Enviamos contraseña hasheada a n8n (registro externo)
                        'password' => password_hash($password, PASSWORD_BCRYPT),
                        'created_at' => date('Y-m-d H:i:s'),
                    ];

                    // URL del webhook de n8n
                    $url = 'https://digital-n8n.owolqd.easypanel.host/webhook-test/from-php-users';

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, [
                        'Content-Type: application/json',
                        // Opcional: usa el mismo token que valides en el Webhook de n8n
                        'X-API-KEY: TU_TOKEN_SECRETO',
                    ]);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                    if ($response === false) {
                        $errorCurl = curl_error($ch);
                        // No romper el flujo si falla n8n: solo loguear
                        error_log('Error cURL n8n: ' . $errorCurl);
                    } elseif ($httpCode < 200 || $httpCode >= 300) {
                        // Loguear si n8n devolvió error
                        error_log('Error HTTP n8n: ' . $httpCode . ' - ' . $response);
                    }

                    curl_close($ch);
                    // --- FIN ENVÍO A N8N ---

                    // Tras registro, volvemos a login
                    header("Location: index.php?controller=auth&action=login");
                    exit;
                } elseif ($resultado === "duplicate") {
                    // En Mongo puede ser DNI o email (por índices únicos)
                    $errores[] = "El DNI o el email ya está registrado";
                } else {
                    $errores[] = "Error al registrar el usuario";
                }
            }
        }

        require "views/register_view.phtml";
        break;

    // LOGOUT
    case 'logout':
        session_destroy();
        // Recorrido: después de cerrar sesión, volvemos a start
        header("Location: index.php?controller=start");
        exit;

    default:
        header("Location: index.php?controller=auth&action=login");
        exit;
}

//  <!-- HOLA RUBEN -->
