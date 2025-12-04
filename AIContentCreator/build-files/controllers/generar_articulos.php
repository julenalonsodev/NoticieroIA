<?php
// controllers/generar_articulos.php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?controller=start");
    exit;
}

require_once __DIR__ . '/../db/db.php';

// Solo aceptar POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?controller=home');
    exit;
}

$pdo = Database::conectar();

if (!$pdo) {
    die("Error de conexión a la base de datos.");
}

$id_genero = isset($_POST['id_genero']) ? (int)$_POST['id_genero'] : 0;

if ($id_genero <= 0) {
    die("ID de género inválido.");
}

$sql = "SELECT 
            id_genero,
            tema,
            descripcion,
            frecuencia,
            cantidad,
            addSources,
            idioma,
            sources,
            tipo_llamada
        FROM planificacioncontenido
        WHERE id_genero = :id_genero";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id_genero' => $id_genero]);
$genero = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$genero) {
    die("No se encontró el género.");
}

$tipo_llamada = !empty($genero['tipo_llamada']) ? $genero['tipo_llamada'] : 'articulos';

header("Location: ../index.php?controller=articulos&id_genero=" . $id_genero . "&tipo_llamada=" . urlencode($tipo_llamada));
exit;
