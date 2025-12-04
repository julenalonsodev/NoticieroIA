<?php
// actions/eliminar_genero.php
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

if ($id <= 0) {
    die("Error: ID no válido.");
}

$pdo = Database::conectar();

try {
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();

    // 1) Eliminar noticias del género
    $stmtNoticias = $pdo->prepare("DELETE FROM noticias WHERE id_genero = :id_genero");
    $stmtNoticias->execute([':id_genero' => $id]);

    // 2) Eliminar género
    $stmtGenero = $pdo->prepare("DELETE FROM planificacioncontenido WHERE id_genero = :id");
    $stmtGenero->execute([':id' => $id]);

    if ($stmtGenero->rowCount() === 0) {
        $pdo->rollBack();
        die("Error: no existe un registro con ese ID.");
    }

    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    die("Error al eliminar: " . $e->getMessage());
}

header("Location: ../index.php?controller=home&action=index&deleted=1");
exit;
