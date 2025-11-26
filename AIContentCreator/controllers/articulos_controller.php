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

// --------------------------------------------
// 1) Llamar al webhook de n8n para pedir artículos
// --------------------------------------------
$webhookUrl = 'https://digital-n8n.owolqd.easypanel.host/webhook/from-php-noticiero';

$payload = [
    'tipo_llamada' => 'articulos',
    'id_genero'    => $idGenero,
];

$ch = curl_init($webhookUrl);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => json_encode($payload),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 20, // evita colgar la petición
]);

$response  = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// --------------------------------------------
// 2) Depuración si hay error con n8n
// --------------------------------------------
if ($response === false || $curlError || $httpCode < 200 || $httpCode >= 300) {
    echo "<pre>";
    echo "ERROR al llamar a n8n\n\n";
    echo "URL usada: $webhookUrl\n\n";
    echo "HTTP CODE: $httpCode\n\n";
    echo "cURL ERROR: $curlError\n\n";
    echo "RESPUESTA RAW:\n";
    var_dump($response);
    echo "</pre>";
    exit;
}

// --------------------------------------------
// 3) Si OK, decodificar JSON (esperamos { noticias: [...] })
// --------------------------------------------
$data = json_decode($response, true);
if (!is_array($data) || json_last_error() !== JSON_ERROR_NONE) {
    echo "<pre>";
    echo "Respuesta de n8n inválida.\n\n";
    echo "HTTP CODE: $httpCode\n\n";
    echo "JSON ERROR: " . json_last_error_msg() . "\n\n";
    echo "RESPUESTA RAW:\n";
    var_dump($response);
    echo "</pre>";
    exit;
}

$noticias = isset($data['noticias']) && is_array($data['noticias'])
    ? $data['noticias']
    : [];

// 4) Montar la estructura $genero para la vista
$genero = [
    'tema'        => 'Género ' . $idGenero,
    'descripcion' => 'Artículos generados desde n8n para este género.',
];

// 5) Cargar la vista
require __DIR__ . '/../views/articulos_view.phtml';
