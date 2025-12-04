<?php
// controllers/recibir_noticias.php

require_once __DIR__ . '/../db/db.php';

header('Content-Type: application/json; charset=utf-8');

// --------------------------------------------
// CONEXIÓN A LA BBDD
// --------------------------------------------
$pdo = Database::conectar();

if (!$pdo) {
    http_response_code(500);
    echo json_encode(['error' => 'Error de conexión a la base de datos']);
    exit;
}

// --------------------------------------------
// SOLO ACEPTAR MÉTODO POST
// --------------------------------------------
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'POST requerido']);
    exit;
}

// --------------------------------------------
// RECIBIR Y DECODIFICAR JSON
// --------------------------------------------
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'JSON inválido']);
    exit;
}

$idGenero = isset($data['id_genero']) ? (int)$data['id_genero'] : 0;
$noticias = $data['noticias'] ?? [];

if ($idGenero <= 0 || empty($noticias) || !is_array($noticias)) {
    http_response_code(400);
    echo json_encode(['error' => 'Datos incompletos o inválidos']);
    exit;
}

// --------------------------------------------
// PREPARAR SQL (tipo_llamada usa el DEFAULT 'articulos')
// --------------------------------------------
$sql = "INSERT INTO noticias 
        (id_genero, titulo, descripcion, imagen, fecha_creacion)
        VALUES (:id_genero, :titulo, :descripcion, :imagen, NOW())";

$stmt = $pdo->prepare($sql);

$insertadas = 0;

try {
    // Usamos transacción por seguridad
    $pdo->beginTransaction();

    foreach ($noticias as $n) {

        $titulo      = $n['titulo']      ?? '';
        $descripcion = $n['descripcion'] ?? '';
        $imagen      = $n['imagen']      ?? '';

        // Normalizar
        $titulo      = trim($titulo);
        $descripcion = trim($descripcion);
        $imagen      = trim($imagen);

        // Si no hay ni título ni descripción, la saltamos
        if ($titulo === '' && $descripcion === '') {
            continue;
        }

        $stmt->execute([
            ':id_genero'   => $idGenero,
            ':titulo'      => $titulo,
            ':descripcion' => $descripcion,
            ':imagen'      => $imagen,
        ]);

        $insertadas++;
    }

    $pdo->commit();

    echo json_encode([
        'status'     => 'ok',
        'insertadas' => $insertadas
    ]);
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Error en recibir_noticias.php: ' . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'error'   => 'Error al insertar noticias',
        'details' => $e->getMessage() // si no quieres exponer detalles, elimina esta línea
    ]);
    exit;
}
