<?php

require __DIR__ . '/../../vendor/autoload.php';   // carga Composer
use MongoDB\Client;

class Database {

    public static function conectar()
    {
        // Cargar variables de entorno (.env)
        if (file_exists(__DIR__ . '/../../.env')) {
            $env = parse_ini_file(__DIR__ . '/../../.env');
        }

        // Si usas otro sistema de carga de .env, adapta esta línea:
        $uri = $env['MONGO_URI'] ?? getenv('MONGO_URI');

        if (!$uri) {
            die("Error: No se encontró la variable MONGO_URI en el .env");
        }

        try {
            // Crear cliente MongoDB
            $client = new Client($uri);

            // Seleccionar base de datos (igual que en Node.js)
            return $client->selectDatabase('AIContentCreator');

        } catch (Exception $e) {
            die("Error de conexión a MongoDB: " . $e->getMessage());
        }
    }
}
    //  <!-- HOLA RUBEN -->
