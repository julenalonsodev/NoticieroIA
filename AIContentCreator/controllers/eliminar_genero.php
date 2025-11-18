<?php
require_once __DIR__ . '/../db/db.php';

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?controller=home&action=index");
    exit;
}

if (!isset($_POST['id'])) {
    die("Error: falta el ID.");
}

$id = intval($_POST['id']);

$conexion = Database::conectar();

$stmt = $conexion->prepare("DELETE FROM planificacioncontenido WHERE id = ?");
$stmt->bind_param("i", $id);

if (!$stmt->execute()) {
    die("Error al eliminar: " . $stmt->error);
}

$stmt->close();
$conexion->close();

// Volver al home tras eliminar
header("Location: ../index.php?controller=home&action=index&deleted=1");
exit;
