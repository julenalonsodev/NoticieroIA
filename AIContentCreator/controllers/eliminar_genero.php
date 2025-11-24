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

// Conectamos a MongoDB
$db = Database::conectar();
$coleccion = $db->selectCollection('planificacioncontenido');

try {
    // Eliminamos por tu campo "id" propio
    $resultado = $coleccion->deleteOne(['id' => $id]);

    if ($resultado->getDeletedCount() === 0) {
        die("Error: no existe un registro con ese ID.");
    }
} catch (Exception $e) {
    die("Error al eliminar: " . $e->getMessage());
}

// Volver al home tras eliminar
header("Location: ../index.php?controller=home&action=index&deleted=1");
exit;

// ------------------------------------------------------------------------
// ✅ ¿Cómo funciona esto en Mongo?

// Se conecta así:

// $db = Database::conectar();
// $coleccion = $db->selectCollection('planificacioncontenido');


// Y elimina con:

// $coleccion->deleteOne(['id' => $id]);


// Mongo devuelve:

// DeletedCount = 1 → eliminado correcto

// DeletedCount = 0 → no había documento con ese id

// Lo controlo en el código.

// ⚠️ IMPORTANTE: Para que esto funcione, debes asegurarte de que cada documento tiene su id propio.

// Ejemplo al insertar:

// $coleccion->insertOne([
//     'id' => 1,
//     'tema' => '...',
//     'dni_usuario' => '...',
//     // etc
// ]);
    //  <!-- HOLA RUBEN -->
