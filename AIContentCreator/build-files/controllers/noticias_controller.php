<?php
// controllers/noticias_controller.php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php?controller=start");
    exit;
}

$usuario = $_SESSION['usuario'];

require_once __DIR__ . '/../db/db.php';

$pdo = Database::conectar();

if (!$pdo) {
    die("Error de conexión a la base de datos.");
}

// -----------------------------
// Filtro por género (opcional)
// -----------------------------
$idGenero = isset($_GET['id_genero']) ? (int) $_GET['id_genero'] : null;

// -----------------------------
// Obtener noticias desde MySQL
// -----------------------------
$sql = "SELECT 
            n.id,
            n.id_genero,
            n.titulo,
            n.descripcion,
            n.imagen,
            n.noticia_revisada,
            n.imagen_revisada,
            n.publicado,
            n.fecha_publicacion,
            n.fecha_creacion,
            pc.tema AS genero_tema
        FROM noticias n
        LEFT JOIN planificacioncontenido pc 
            ON n.id_genero = pc.id_genero";

$params = [];

if ($idGenero) {
    $sql .= " WHERE n.id_genero = :id_genero";
    $params[':id_genero'] = $idGenero;
}

$sql .= " ORDER BY n.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$noticias = [];
$generoTema = null;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    // Normalizar estados (aunque aquí solo se muestran)
    $row['noticia_revisada'] = $row['noticia_revisada'] !== null ? strtolower($row['noticia_revisada']) : null;
    $row['imagen_revisada']  = $row['imagen_revisada']  !== null ? strtolower($row['imagen_revisada'])  : null;
    $row['publicado']        = $row['publicado']        !== null ? strtolower($row['publicado'])        : null;

    if ($generoTema === null && !empty($row['genero_tema'])) {
        $generoTema = $row['genero_tema'];
    }

    $noticias[] = $row;
}

// variables auxiliares para la vista
$__idGenero   = $idGenero;
$__generoTema = $generoTema;

// -----------------------------
// Cargar la vista (solo lectura)
// -----------------------------
require_once __DIR__ . '/../views/noticias_view.phtml';
