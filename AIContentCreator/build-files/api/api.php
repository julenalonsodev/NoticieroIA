<?php
header("Content-Type: application/json; charset=UTF-8");

// Seguridad básica
$apiKey = "TU_API_KEY_SECRETA"; // cámbiala
if (!isset($_GET['key']) || $_GET['key'] !== $apiKey) {
    http_response_code(403);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$host = "127.0.0.1";
$db   = "aicontentcreator";
$user = "root";
$pass = "";
$charset = "utf8mb4";

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    $stmt = $pdo->query("SELECT * FROM TU_TABLA LIMIT 50");
    $data = $stmt->fetchAll();
    echo json_encode($data);

} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
