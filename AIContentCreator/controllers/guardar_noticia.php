<?php
// controllers/guardar_noticia.php

require_once __DIR__ . '/../db/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?controller=noticias");
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) die("ID inválido");

// Leer valores del formulario
$noticia_revisada = ($_POST['noticia_revisada'] === "") ? null : $_POST['noticia_revisada'];
$imagen_revisada  = ($_POST['imagen_revisada'] === "") ? null : $_POST['imagen_revisada'];
$publicado        = ($_POST['publicado'] === "") ? null : $_POST['publicado'];

$conexion = Database::conectar();

// Nota: publicado = "publicado" debe generar fecha, "borrador" debe eliminarla.
// NULL = mantener fecha como está.
$sql = "
    UPDATE noticias SET
        noticia_revisada = COALESCE(?, noticia_revisada),
        imagen_revisada  = COALESCE(?, imagen_revisada),
        publicado        = COALESCE(?, publicado),
        fecha_publicacion = CASE
            WHEN ? = 'publicado' THEN 
                IF(fecha_publicacion IS NULL, NOW(), fecha_publicacion)
            WHEN ? = 'borrador' THEN NULL
            ELSE fecha_publicacion
        END
    WHERE id = ?
";

$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "sssssi",
    $noticia_revisada,
    $imagen_revisada,
    $publicado,
    $publicado,
    $publicado,
    $id
);

$stmt->execute();
$stmt->close();
$conexion->close();

header("Location: ../index.php?controller=noticias");
exit;
