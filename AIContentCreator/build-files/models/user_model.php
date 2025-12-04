<?php
// models/user_model.php

require_once __DIR__ . '/../db/db.php';

class user_model
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::conectar();
        // Ya no hace falta comprobar if (!$this->pdo) porque db.php hace die() si falla
    }

    public function buscarPorEmail($email)
    {
        $sql = "
            SELECT 
                dni,
                nombre,
                apellidos,
                numero_empresa,
                email,
                password,
                created_at
            FROM users
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function buscarPorDNI($dni)
    {
        $sql = "
            SELECT 
                dni,
                nombre,
                apellidos,
                numero_empresa,
                email,
                password,
                created_at
            FROM users
            WHERE dni = :dni
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':dni' => $dni]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function registrar($dni, $nombre, $apellidos, $numero_empresa, $email, $password)
    {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $sql = "
                INSERT INTO users (
                    dni,
                    nombre,
                    apellidos,
                    numero_empresa,
                    email,
                    password
                )
                VALUES (
                    :dni,
                    :nombre,
                    :apellidos,
                    :numero_empresa,
                    :email,
                    :password
                )
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':dni'            => $dni,
                ':nombre'         => $nombre,
                ':apellidos'      => $apellidos,
                ':numero_empresa' => (int)$numero_empresa,
                ':email'          => $email,
                ':password'       => $hashedPassword,
            ]);

            return true;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                return "duplicate";
            }

            error_log('Error al registrar usuario: ' . $e->getMessage());
            return false;
        }
    }
}
