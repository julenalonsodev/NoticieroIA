<?php
// conectamos con la BD
require_once __DIR__ . "/../db/db.php";

use MongoDB\Driver\Exception\BulkWriteException;

class user_model
{
    private $db;          // MongoDB\Database
    private $collection;  // MongoDB\Collection

    public function __construct()
    {
        // Ahora conectar() devuelve una MongoDB\Database
        $this->db = Database::conectar();
        $this->collection = $this->db->selectCollection('users');
    }

    /**
     * Convierte un BSONDocument a array asociativo PHP
     */
    private function docToArray($doc): array
    {
        // Forma simple y segura
        return json_decode(json_encode($doc), true);
    }

    public function buscarPorEmail($email)
    {
        $doc = $this->collection->findOne(['email' => $email]);

        return $doc ? $this->docToArray($doc) : null;
    }

    public function buscarPorDNI($dni)
    {
        $doc = $this->collection->findOne(['dni' => $dni]);

        return $doc ? $this->docToArray($doc) : null;
    }

    public function registrar($dni, $nombre, $apellidos, $numero_empresa, $email, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $documento = [
            'dni'            => (string)$dni,
            'nombre'         => (string)$nombre,
            'apellidos'      => (string)$apellidos,
            'numero_empresa' => (int)$numero_empresa,
            'email'          => (string)$email,
            'password'       => (string)$hash,
        ];

        try {
            $resultado = $this->collection->insertOne($documento);

            return $resultado->getInsertedCount() === 1;
        } catch (BulkWriteException $e) {
            // 11000 = duplicate key error en MongoDB
            $writeResult = $e->getWriteResult();
            foreach ($writeResult->getWriteErrors() as $err) {
                if ($err->getCode() === 11000) {
                    return "duplicate";
                }
            }
            return false;
        } catch (\Throwable $e) {
            // Cualquier otro error
            error_log('Error al registrar usuario en MongoDB: ' . $e->getMessage());
            return false;
        }
    }
}
?>
