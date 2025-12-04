<?php
// controllers/guardar_noticia.php

session_start();
require_once __DIR__ . '/../db/db.php';

// --------------------------------------------
// SOLO ACEPTAR USUARIOS AUTENTICADOS
// --------------------------------------------
if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?controller=start");
    exit;
}

// --------------------------------------------
// SOLO ACEPTAR MÉTODO POST
// --------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?controller=noticias");
    exit;
}

// --------------------------------------------
// ID de la noticia (obligatorio)
// --------------------------------------------
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    die("ID inválido");
}

// id_genero (opcional, para redirigir de vuelta a artículos por género)
$id_genero = isset($_POST['id_genero']) ? (int)$_POST['id_genero'] : 0;

// --------------------------------------------
// LEER VALORES DEL FORMULARIO Y NORMALIZAR
// --------------------------------------------
$noticia_revisada = isset($_POST['noticia_revisada']) ? trim($_POST['noticia_revisada']) : '';
$imagen_revisada  = isset($_POST['imagen_revisada'])  ? trim($_POST['imagen_revisada'])  : '';
$publicado        = isset($_POST['publicado'])        ? trim($_POST['publicado'])        : '';

// Normalizar a null cuando vienen vacíos
$noticia_revisada = ($noticia_revisada === '') ? null : $noticia_revisada;
$imagen_revisada  = ($imagen_revisada  === '') ? null : $imagen_revisada;
$publicado        = ($publicado        === '') ? null : $publicado;

// Aseguramos que publicado solo pueda ser 'publicado', 'borrador' o null
if (!in_array($publicado, ['publicado', 'borrador', null], true)) {
    $publicado = null;
}

// --------------------------------------------
// CONEXIÓN A LA BD (PDO)
// --------------------------------------------
$pdo = Database::conectar();
if (!$pdo) {
    die("Error de conexión a la base de datos.");
}

// --------------------------------------------
// 1) Obtener la noticia actual (fecha_publicacion)
// --------------------------------------------
$sqlSelect = "SELECT fecha_publicacion FROM noticias WHERE id = :id";
$stmtSelect = $pdo->prepare($sqlSelect);
$stmtSelect->execute([':id' => $id]);
$noticia = $stmtSelect->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    die("Error: no se encontró la noticia con ese ID.");
}

$fechaActual = $noticia['fecha_publicacion']; // string o null
$nuevaFechaPublicacion = $fechaActual;

// Lógica de fecha_publicacion:
// - publicado = 'publicado' → si fecha_publicacion es NULL, poner ahora; si ya tiene, mantener.
// - publicado = 'borrador'  → poner NULL.
// - publicado = NULL        → dejar fecha_publicacion como está.
if ($publicado === 'publicado') {
    if ($fechaActual === null) {
        $nuevaFechaPublicacion = date('Y-m-d H:i:s'); // ahora
    }
} elseif ($publicado === 'borrador') {
    $nuevaFechaPublicacion = null;
}

// --------------------------------------------
// 2) Actualizar valores (solo flags + fecha)
// --------------------------------------------
$sqlUpdate = "
    UPDATE noticias
    SET
        noticia_revisada  = COALESCE(:noticia_revisada, noticia_revisada),
        imagen_revisada   = COALESCE(:imagen_revisada, imagen_revisada),
        publicado         = COALESCE(:publicado, publicado),
        fecha_publicacion = :fecha_publicacion
    WHERE id = :id
";

$stmtUpdate = $pdo->prepare($sqlUpdate);

$params = [
    ':noticia_revisada'  => $noticia_revisada,
    ':imagen_revisada'   => $imagen_revisada,
    ':publicado'         => $publicado,
    ':fecha_publicacion' => $nuevaFechaPublicacion,
    ':id'                => $id,
];

if (!$stmtUpdate->execute($params)) {
    $errorInfo = $stmtUpdate->errorInfo();
    die("Error al actualizar la noticia: " . ($errorInfo[2] ?? 'Error desconocido'));
}

// --------------------------------------------
// Redirección:
// - Si venimos de la vista por género (artículos de un género), volvemos allí.
// - Si no, volvemos al listado general de noticias.
// --------------------------------------------
if ($id_genero > 0) {
    header("Location: ../index.php?controller=articulos&id_genero=" . $id_genero . "&tipo_llamada=articulos");
} else {
    header("Location: ../index.php?controller=noticias");
}
exit;
