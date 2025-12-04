<?php
// db/db.php

class Database
{
    private static $host    = 'localhost';        // Servidor MySQL (XAMPP)
    private static $db      = 'AIContentCreator'; // Nombre de tu BD
    private static $user    = 'root';             // Usuario XAMPP
    private static $pass    = '';                 // Password XAMPP (vacía por defecto)
    private static $charset = 'utf8mb4';

    public static function conectar()
    {
        $dsn = "mysql:host=" . self::$host .
               ";dbname=" . self::$db .
               ";charset=" . self::$charset;

        try {
            $pdo = new PDO($dsn, self::$user, self::$pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $pdo;

        } catch (PDOException $e) {
            // Puedes loguear el error si quieres
            error_log('Error de conexión: ' . $e->getMessage());
            die('Error de conexión a la base de datos'); // Mensaje simple para el usuario
        }
    }
}



    // private static $host = '4.251.116.81';   // ← external host / public IP / dominio público
    // private static $db   = 'dbgenerator';
    // private static $user = 'dominion';
    // private static $pass = 'TU_PASS';
    // private static $port = '3306';
    // private static $charset = 'utf8mb4';
