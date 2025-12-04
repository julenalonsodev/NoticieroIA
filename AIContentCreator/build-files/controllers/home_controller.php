<?php
// controllers/home_controller.php

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

// Listar todos los géneros de planificacioncontenido
$sql = "SELECT 
            id_genero,
            tema,
            descripcion,
            frecuencia,
            cantidad,
            idioma
        FROM planificacioncontenido
        ORDER BY id_genero DESC";

$stmt = $pdo->query($sql);
$generos = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

// Cargar la vista principal
require __DIR__ . '/../views/home_view.phtml';
