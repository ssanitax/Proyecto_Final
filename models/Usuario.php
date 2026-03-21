<?php

class Usuario {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // 1. REGISTRO: Crea un nuevo usuario con la contraseña cifrada
    public function registrar($nombre, $email, $password) {
        try {
            $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'usuario')";
            $stmt = $this->db->prepare($sql);
            
            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $exito = $stmt->execute([
                ':nombre'   => $nombre,
                ':email'    => $email,
                ':password' => $passwordHash
            ]);

            // Si se insertó correctamente, devolvemos el ID generado
            return $exito ? $this->db->lastInsertId() : false;

        } catch (PDOException $e) {
            return false;
        }
    }

    // 2. LOGIN: Verifica si las credenciales son correctas
    public function login($email, $password) {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND activo = TRUE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch();

        // Si el usuario existe, comprobamos la contraseña cifrada
        if ($usuario && password_verify($password, $usuario->password)) {
            return $usuario; // Devolvemos el objeto usuario con sus datos (id, rol, nombre...)
        }
        
        return false;
    }

    // 3. BUSCAR POR ID: Útil para mostrar el perfil o la colección
    public function obtenerPorId($id) {
        $sql = "SELECT id, nombre, email, rol, created_at FROM usuarios WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
