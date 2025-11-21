<?php
// controllers/guardar_genero.php

session_start();
require_once __DIR__ . '/../db/db.php';

use MongoDB\BSON\UTCDateTime;

// --------------------------------------------
// SOLO ACEPTAR MÉTODO POST
// --------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/home_view.phtml');
    exit;
}

// --------------------------------------------
// COMPROBAR USUARIO LOGUEADO
// --------------------------------------------
if (!isset($_SESSION['usuario'])) {
    // Si no hay usuario en sesión, lo mandamos al login
    header('Location: ../index.php?controller=auth&action=login');
    exit;
}

$usuario = $_SESSION['usuario'];

$dniUsuario = $usuario['dni'] ?? null;
$numeroEmpresaUsuario = isset($usuario['numero_empresa']) ? (int)$usuario['numero_empresa'] : null;

if ($dniUsuario === null || $numeroEmpresaUsuario === null) {
    die("Error: no se pudo obtener el usuario o la empresa de la sesión.");
}

// --------------------------------------------
// CONEXIÓN A LA BASE DE DATOS (MONGODB)
// --------------------------------------------
$db = Database::conectar();
$coleccion = $db->selectCollection('planificacioncontenido');

// --------------------------------------------
// RECIBIR DATOS DEL FORMULARIO
// --------------------------------------------
$tema        = isset($_POST['tema']) ? trim($_POST['tema']) : null;
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
$frecuencia  = isset($_POST['frecuencia']) ? trim($_POST['frecuencia']) : null;
$cantidad    = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : null;
$addSources  = isset($_POST['addSources']) ? trim($_POST['addSources']) : 'no';
$idioma      = isset($_POST['idioma']) ? trim($_POST['idioma']) : null;

// En el formulario el hidden se llama "fuentes"
$sources = isset($_POST['fuentes']) && $_POST['fuentes'] !== '' ? $_POST['fuentes'] : null;

// --------------------------------------------
// VALIDACIONES BÁSICAS
// --------------------------------------------
if (!$tema || !$descripcion || !$frecuencia || !$cantidad || !$idioma) {
    die("Error: faltan campos obligatorios.");
}

// Validar JSON si existen fuentes (opcional)
$fuentesArray = [];
if ($sources !== null) {
    $decoded = json_decode($sources, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $fuentesArray = $decoded;
    } else {
        // Si el JSON es inválido, lo ignoramos
        $fuentesArray = [];
    }
}

// addSources: lo convertimos a booleano (para cuadrar con el esquema)
$addSourcesBool = in_array(strtolower($addSources), ['si', 'sí', 'yes', 'on', '1'], true);

// --------------------------------------------
// OBTENER UN ID NUMÉRICO PROPIO (AUTO-INCREMENT SENCILLO)
// --------------------------------------------
// Buscamos el documento con el id más alto y sumamos 1
$ultimo = $coleccion->findOne([], ['sort' => ['id' => -1]]);

if ($ultimo && isset($ultimo['id'])) {
    $nuevoId = (int)$ultimo['id'] + 1;
} else {
    $nuevoId = 1;
}

// --------------------------------------------
// INSERTAR EN MONGODB
// --------------------------------------------
$documento = [
    'id'                     => $nuevoId,
    'tema'                   => $tema,
    'descripcion'            => $descripcion,
    'frecuencia'             => $frecuencia,
    'cantidad'               => (int)$cantidad,
    'addSources'             => $addSourcesBool,
    'idioma'                 => $idioma,
    'sources'                => $fuentesArray,
    'fecha_creacion'         => new UTCDateTime(), // ahora mismo
    'dni_usuario'            => $dniUsuario,
    'numero_empresa_usuario' => $numeroEmpresaUsuario,
];

try {
    $resultado = $coleccion->insertOne($documento);
    if ($resultado->getInsertedCount() !== 1) {
        die("Error al insertar: no se pudo guardar el documento.");
    }
} catch (Exception $e) {
    die("Error al insertar en MongoDB: " . $e->getMessage());
}

// --------------------------------------------
// REDIRECCIÓN DESPUÉS DE INSERTAR
// --------------------------------------------
header("Location: ../index.php?controller=home&action=index");
exit;

// ------------------------------------------------------------------------
// Eliminar sigue funcionando con el controlador que hicimos antes porque:

// Aquí insertamos un id entero.

// Allí borras usando deleteOne(['id' => $id]).

// Cada planificación se guarda con:

// dni_usuario y numero_empresa_usuario sacados de $_SESSION['usuario'].
    //  <!-- HOLA RUBEN -->
