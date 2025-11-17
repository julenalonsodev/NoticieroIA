<?php
class Conectar {

    public static function conexion() {

        $host = "localhost";
        $user = "root";   // cámbialo si usas otro usuario
        $pass = "";       // cámbialo si tu MySQL tiene password
        $db   = "aicontentcreator"; // <-- ESTE ES EL QUE FALTABA

        $conexion = new mysqli($host, $user, $pass, $db);

        if ($conexion->connect_errno) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        $conexion->set_charset("utf8mb4");
        return $conexion;
    }
}
