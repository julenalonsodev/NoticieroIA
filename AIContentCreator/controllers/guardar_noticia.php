<?php
// controllers/guardar_noticia.php

session_start();
require_once __DIR__ . '/../db/db.php';

use MongoDB\BSON\UTCDateTime;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?controller=noticias");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    die("ID inválido");
}

// Leer valores del formulario
$noticia_revisada = ($_POST['noticia_revisada'] === "") ? null : $_POST['noticia_revisada'];
$imagen_revisada  = ($_POST['imagen_revisada'] === "") ? null : $_POST['imagen_revisada'];
$publicado        = ($_POST['publicado'] === "") ? null : $_POST['publicado'];
// En tu lógica: publicado puede ser 'publicado', 'borrador' o null

// Conexión a MongoDB
$db = Database::conectar();
$coleccion = $db->selectCollection('noticias');

// Primero obtenemos la noticia actual para replicar bien la lógica de fecha_publicacion
$noticia = $coleccion->findOne(['id' => $id]);

if (!$noticia) {
    die("Error: no se encontró la noticia con ese ID.");
}

// Fecha de publicación actual (puede ser null)
$fechaActual = $noticia['fecha_publicacion'] ?? null;

// Replicar lógica MySQL:
//
// - publicado = 'publicado' → si fecha_publicacion es NULL, poner ahora; si ya tiene, mantener.
// - publicado = 'borrador'  → poner NULL.
// - publicado = NULL        → dejar fecha_publicacion como está.
$nuevaFechaPublicacion = $fechaActual;

if ($publicado === 'publicado') {
    if ($fechaActual === null) {
        $nuevaFechaPublicacion = new UTCDateTime(); // ahora
    }
} elseif ($publicado === 'borrador') {
    $nuevaFechaPublicacion = null;
}

// Construimos el $set dinámicamente (equivalente a los COALESCE)
$set = [];

// noticia_revisada = COALESCE(?, noticia_revisada)
if ($noticia_revisada !== null) {
    $set['noticia_revisada'] = $noticia_revisada;
}

// imagen_revisada = COALESCE(?, imagen_revisada)
if ($imagen_revisada !== null) {
    $set['imagen_revisada'] = $imagen_revisada;
}

// publicado = COALESCE(?, publicado)
if ($publicado !== null) {
    // Aquí estamos guardando el estado como string ('publicado', 'borrador', etc.)
    $set['publicado'] = $publicado;

    // Y aplicamos la nueva fecha_publicacion según la lógica anterior
    $set['fecha_publicacion'] = $nuevaFechaPublicacion;
}

// Si no hay nada que actualizar, volvemos tal cual
if (empty($set)) {
    header("Location: ../index.php?controller=noticias");
    exit;
}

// Ejecutar actualización en MongoDB
try {
    $resultado = $coleccion->updateOne(
        ['id' => $id],
        ['$set' => $set]
    );

    if ($resultado->getMatchedCount() === 0) {
        die("No se encontró la noticia para actualizar.");
    }
} catch (Exception $e) {
    die("Error al actualizar en MongoDB: " . $e->getMessage());
}

header("Location: ../index.php?controller=noticias");
exit;
    //  <!-- HOLA RUBEN -->
