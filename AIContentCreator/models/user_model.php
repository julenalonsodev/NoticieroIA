<?php
// conectamos con la BD
require_once __DIR__ . "/../db/db.php";

class user_model {

    private $db;

    public function __construct()
    {
        // Ajusta a tu forma real de conectar
        $this->db = Database::conectar(); // que devuelva un mysqli
    }

    public function buscarPorEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // o null si no existe
    }

    public function buscarPorDNI($dni)
    {
        $sql = "SELECT * FROM users WHERE dni = ? LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("s", $dni);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // o null si no existe
    }

    public function registrar($dni, $nombre, $apellidos, $numero_empresa, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (dni, nombre, apellidos, numero_empresa, email, password)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sssiss", $dni, $nombre, $apellidos, $numero_empresa, $email, $hash);

        if ($stmt->execute()) {
            return true;
        } else {
            // Error por clave duplicada (dni PK o email UNIQUE)
            if ($this->db->errno == 1062) {
                return "duplicate";
            }
            return false;
        }
    }
}
?>
     <!-- HOLA RUBEN -->
