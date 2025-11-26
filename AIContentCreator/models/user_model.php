<?php
// conectamos con la BD
require_once __DIR__ . "/../db/db.php";

class user_model
{
    /** @var PDO */
    private $pdo;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        // Ahora conectar() devuelve un PDO (MySQL)
        $this->pdo = Database::conectar();

        if (!$this->pdo) {
            throw new Exception("Error de conexión a la base de datos.");
        }
    }

    /**
     * Busca un usuario por email.
     *
     * @param string $email
     * @return array|null
     */
    public function buscarPorEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':email' => trim($email)]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('Error al buscar usuario por email: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca un usuario por DNI.
     *
     * @param string $dni
     * @return array|null
     */
    public function buscarPorDNI(string $dni): ?array
    {
        $sql = "SELECT * FROM users WHERE dni = :dni LIMIT 1";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':dni' => trim($dni)]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log('Error al buscar usuario por DNI: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Registra un nuevo usuario.
     *
     * @param string $dni
     * @param string $nombre
     * @param string $apellidos
     * @param int    $numero_empresa
     * @param string $email
     * @param string $password
     * @return bool|string true = ok, "duplicate" = email/dni repetido, false = otro error
     */
    public function registrar(
        string $dni,
        string $nombre,
        string $apellidos,
        int $numero_empresa,
        string $email,
        string $password
    ) {
        $dni            = trim($dni);
        $nombre         = trim($nombre);
        $apellidos      = trim($apellidos);
        $numero_empresa = (int)$numero_empresa;
        $email          = trim($email);

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users 
                    (dni, nombre, apellidos, numero_empresa, email, password)
                VALUES 
                    (:dni, :nombre, :apellidos, :numero_empresa, :email, :password)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':dni'            => $dni,
                ':nombre'         => $nombre,
                ':apellidos'      => $apellidos,
                ':numero_empresa' => $numero_empresa,
                ':email'          => $email,
                ':password'       => $hash,
            ]);

            return true;
        } catch (PDOException $e) {
            // 23000 = violación de restricción (p.ej. UNIQUE)
            if ($e->getCode() === '23000') {
                // Asumimos clave única en email o dni
                return "duplicate";
            }

            error_log('Error al registrar usuario en MySQL: ' . $e->getMessage());
            return false;
        }
    }
}
