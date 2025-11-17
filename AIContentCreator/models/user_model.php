<?php
// conectamos con la BD
require_once __DIR__ . "/../db/db.php";

class user_model {
    private $db;

    public function __construct() {
        $this->db = Conectar::conexion();
    }

    public function registrar($dni, $nombre, $apellidos, $numero_empresa, $email, $password) {
        $sql = "INSERT INTO users (dni, nombre, apellidos, numero_empresa, email, password)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Error SQL: " . $this->db->error);
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        // numero_empresa INT (cambia "i" a "s" si es texto)
        $stmt->bind_param(
            "sssiss",
            $dni,
            $nombre,
            $apellidos,
            $numero_empresa,
            $email,
            $hash
        );

        return $stmt->execute();
    }

    public function buscarPorEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            die("Error SQL: " . $this->db->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();

        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }
}
