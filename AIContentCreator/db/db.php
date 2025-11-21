<?php

class Database {

    public static function conectar()
    {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $dbname = "aicontentcreator";  // Ajusta al nombre final de tu BD

        $conexion = new mysqli($host, $user, $pass, $dbname);

        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Para permitir utf8 en texto
        $conexion->set_charset("utf8mb4");

        return $conexion;
    }
}
    //  <!-- HOLA RUBEN -->
