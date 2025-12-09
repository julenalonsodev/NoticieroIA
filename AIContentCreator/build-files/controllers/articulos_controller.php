<?php
// controllers/articulos_controller.php

require_once __DIR__ . '/../db/db.php';

$pdo = Database::conectar();

if (!$pdo) {
    die('Error de conexión a la base de datos.');
}

$idGenero = isset($_GET['id_genero']) ? (int) $_GET['id_genero'] : 0;

if ($idGenero <= 0) {
    die('id_genero no válido');
}

// 1) Obtener datos del género
$sqlGenero = "
    SELECT 
        id_genero,
        tema,
        descripcion,
        frecuencia,
        cantidad,
        addSources,
        idioma,
        sources,
        tipo_llamada,
        fecha_creacion
    FROM planificacioncontenido
    WHERE id_genero = :id_genero
    LIMIT 1
";
$stmtGenero = $pdo->prepare($sqlGenero);
$stmtGenero->execute([':id_genero' => $idGenero]);
$genero = $stmtGenero->fetch(PDO::FETCH_ASSOC);

if (!$genero) {
    die('El género indicado no existe en planificacioncontenido.');
}

// 2) Obtener noticias asociadas al género
$sqlNoticias = "
    SELECT 
        id,
        id_genero,
        titulo,
        descripcion,
        imagen,
        noticia_revisada,
        imagen_revisada,
        publicado,
        fecha_publicacion,
        fecha_creacion,
        tipo_llamada
    FROM noticias
    WHERE id_genero = :id_genero
    ORDER BY fecha_creacion DESC
";
$stmtNoticias = $pdo->prepare($sqlNoticias);
$stmtNoticias->execute([':id_genero' => $idGenero]);
$noticias = $stmtNoticias->fetchAll(PDO::FETCH_ASSOC);

// Normalizar estados a minúsculas para que encajen con los <option value="...">
foreach ($noticias as &$row) {
    $row['noticia_revisada'] = $row['noticia_revisada'] !== null ? strtolower($row['noticia_revisada']) : null;
    $row['imagen_revisada']  = $row['imagen_revisada']  !== null ? strtolower($row['imagen_revisada'])  : null;
    $row['publicado']        = $row['publicado']        !== null ? strtolower($row['publicado'])        : null;
}
unset($row);

// 3) Cargar la vista con controles de edición
require __DIR__ . '/../views/articulos_view.phtml';
