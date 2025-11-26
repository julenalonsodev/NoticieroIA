<?php
// controllers/generar_articulos.php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../index.php?controller=start");
    exit;
}

require_once __DIR__ . '/../db/db.php';

// SOLO ACEPTAR MÉTODO POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php?controller=home');
    exit;
}

$pdo = Database::conectar();

if (!$pdo) {
    die("Error de conexión a la base de datos.");
}

// ID del género recibido desde el botón
$id_genero = isset($_POST['id_genero']) ? (int)$_POST['id_genero'] : 0;

if ($id_genero <= 0) {
    die("ID de género inválido.");
}

// Recuperamos los datos del género
$sql = "SELECT 
            id_genero,
            tema,
            descripcion,
            frecuencia,
            cantidad,
            addSources,
            idioma,
            sources
        FROM planificacioncontenido
        WHERE id_genero = :id_genero";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id_genero' => $id_genero]);
$genero = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$genero) {
    die("No se encontró el género.");
}

// tipo de llamada para el switch del webhook
$tipo_llamada = 'articulos';

// Payload hacia n8n
$payload = [
    'tipo_llamada' => $tipo_llamada,
    'id_genero'    => (int)$genero['id_genero'],
    'tema'         => $genero['tema'],
    'descripcion'  => $genero['descripcion'],
    'frecuencia'   => $genero['frecuencia'],
    'cantidad'     => (int)$genero['cantidad'],
    'addSources'   => $genero['addSources'],
    'idioma'       => $genero['idioma'],
    'sources'      => $genero['sources'],
    'created_at'   => date('Y-m-d H:i:s'),
];

// Webhook ÚNICO (URL de PRODUCCIÓN, no de test)
$n8n_url = 'https://digital-n8n.owolqd.easypanel.host/webhook/from-php-noticiero';

$ch = curl_init($n8n_url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20, // evita cuelgues largos
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// No bloqueamos al usuario si falla n8n; solo registramos el error
if ($response === false || $curlError || $httpCode < 200 || $httpCode >= 300) {
    error_log("[N8N] Error enviando artículos: HTTP $httpCode — RESPUESTA: $response — cURL: $curlError");
}

// Después de llamar a n8n, redirigimos a la pantalla de artículos de ese género
header("Location: ../index.php?controller=articulos&id_genero=" . $id_genero . "&tipo_llamada=articulos");
exit;
