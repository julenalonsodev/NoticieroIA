<?php
// controllers/guardar_genero.php

require_once __DIR__ . '/../db/db.php';

// --------------------------------------------
// SOLO ACEPTAR MÉTODO POST
// --------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?controller=home');
    exit;
}

// --------------------------------------------
// CONEXIÓN A LA BASE DE DATOS (PDO)
// --------------------------------------------
$pdo = Database::conectar();

if (!$pdo) {
    die("Error de conexión a la base de datos.");
}

// --------------------------------------------
// RECIBIR DATOS DEL FORMULARIO
// --------------------------------------------
$tema        = isset($_POST['tema']) ? trim($_POST['tema']) : null;
$descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null;
$frecuencia  = isset($_POST['frecuencia']) ? trim($_POST['frecuencia']) : null;
$cantidad    = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : null;
$addSources  = isset($_POST['addSources']) ? trim($_POST['addSources']) : 'no';
$idioma      = isset($_POST['idioma']) ? trim($_POST['idioma']) : null;

// valor por defecto para la columna tipo_llamada
$tipo_llamada = 'genero';

// En el formulario el hidden se llama "fuentes"
$sources = isset($_POST['fuentes']) && $_POST['fuentes'] !== '' ? $_POST['fuentes'] : null;

// --------------------------------------------
// VALIDACIONES BÁSICAS
// --------------------------------------------
if (!$tema || !$descripcion || !$frecuencia || $cantidad === null || !$idioma) {
    die("Error: faltan campos obligatorios.");
}

if ($cantidad <= 0) {
    die("Error: la cantidad debe ser un número entero mayor que 0.");
}

// Validar JSON si hay fuentes
if ($sources !== null) {
    json_decode($sources);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // si no es JSON válido, lo dejamos como NULL para no romper el CHECK(json_valid)
        $sources = null;
    }
}

// Opcional: validar que frecuencia e idioma estén dentro de los ENUM permitidos
$frecuenciasValidas = ['Diario', 'Semanal', 'Mensual'];
$idiomasValidos     = ['es', 'en', 'fr'];
$addSourcesValidos  = ['si', 'no'];

if (!in_array($frecuencia, $frecuenciasValidas, true)) {
    die("Error: frecuencia no válida.");
}

if (!in_array($idioma, $idiomasValidos, true)) {
    die("Error: idioma no válido.");
}

if (!in_array($addSources, $addSourcesValidos, true)) {
    $addSources = 'no';
}

// --------------------------------------------
// INSERTAR EN LA BD (PDO)
// --------------------------------------------
$sql = "INSERT INTO planificacioncontenido 
        (tema, descripcion, frecuencia, cantidad, addSources, idioma, sources, tipo_llamada)
        VALUES (:tema, :descripcion, :frecuencia, :cantidad, :addSources, :idioma, :sources, :tipo_llamada)";

try {
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':tema'         => $tema,
        ':descripcion'  => $descripcion,
        ':frecuencia'   => $frecuencia,
        ':cantidad'     => $cantidad,
        ':addSources'   => $addSources,
        ':idioma'       => $idioma,
        ':sources'      => $sources,
        ':tipo_llamada' => $tipo_llamada,
    ]);

} catch (PDOException $e) {
    die("Error al insertar: " . $e->getMessage());
}

// --------------------------------------------
// OBTENER id_genero RECIÉN INSERTADO (por si lo necesitas)
// --------------------------------------------
$id_genero = $pdo->lastInsertId();

// --------------------------------------------
// REDIRECCIÓN DESPUÉS DE INSERTAR
// --------------------------------------------
header("Location: ../index.php?controller=home&genero_creado=1");
exit;
