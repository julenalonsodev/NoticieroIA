<?php
// controllers/guardar_genero.php

require_once __DIR__ . '/../db/db.php';

// --------------------------------------------
// SOLO ACEPTAR MÉTODO POST
// --------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/home_view.phtml');
    exit;
}

// --------------------------------------------
// CONEXIÓN A LA BASE DE DATOS
// --------------------------------------------
$conexion = Database::conectar();

// --------------------------------------------
// RECIBIR DATOS DEL FORMULARIO
// --------------------------------------------
$tema = isset($_POST['tema']) ? trim($_POST['tema']) : null;
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
$frecuencia = isset($_POST['frecuencia']) ? trim($_POST['frecuencia']) : null;
$cantidad = isset($_POST['cantidad']) ? intval($_POST['cantidad']) : null;
$addSources = isset($_POST['addSources']) ? trim($_POST['addSources']) : 'no';
$idioma = isset($_POST['idioma']) ? trim($_POST['idioma']) : null;

// En el formulario el hidden se llama "fuentes"
$sources = isset($_POST['fuentes']) && $_POST['fuentes'] !== '' ? $_POST['fuentes'] : null;

// --------------------------------------------
// VALIDACIONES BÁSICAS
// --------------------------------------------
if (!$tema || !$descripcion || !$frecuencia || !$cantidad || !$idioma) {
    die("Error: faltan campos obligatorios.");
}

// Validar JSON si existen fuentes (opcional)
if ($sources !== null) {
    json_decode($sources);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Si el JSON es inválido, lo ignoramos
        $sources = null;
    }
}

// --------------------------------------------
// INSERTAR EN LA BD (MYSQLI PREPARED STATEMENT)
// --------------------------------------------
// OJO: aquí la columna se llama `sources` (como en tu BBDD)
$query = "INSERT INTO planificacioncontenido 
          (tema, descripcion, frecuencia, cantidad, addSources, idioma, sources)
          VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conexion->prepare($query);

if (!$stmt) {
    die("Error en prepare: " . $conexion->error);
}

// Vincular parámetros: s = string, i = int
$stmt->bind_param(
    "sssisss",
    $tema,
    $descripcion,
    $frecuencia,
    $cantidad,
    $addSources,
    $idioma,
    $sources
);

if (!$stmt->execute()) {
    die("Error al insertar: " . $stmt->error);
}

$stmt->close();
$conexion->close();

// --------------------------------------------
// REDIRECCIÓN DESPUÉS DE INSERTAR
// --------------------------------------------
header("Location: ../index.php?controller=home&action=index");
exit;
    //  <!-- HOLA RUBEN -->
