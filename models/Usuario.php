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

    public function obtenerPasswordHash($id) {
        $stmt = $this->db->prepare("SELECT password FROM usuarios WHERE id = ? AND activo = TRUE");
        $stmt->execute([(int)$id]);
        $row = $stmt->fetch();
        return $row ? $row->password : null;
    }

    public function actualizarNombre($id, $nombre) {
        $stmt = $this->db->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?");
        return $stmt->execute([$nombre, (int)$id]);
    }

    public function actualizarPassword($id, $passwordPlano) {
        $hash = password_hash($passwordPlano, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
        return $stmt->execute([$hash, (int)$id]);
    }

    /**
     * Elimina la cuenta de un coleccionista y todo su rastro personal.
     * No borra juegos, ediciones, plataformas, idiomas ni regiones del catálogo maestro
     * (lo validado por admin permanece).
     */
    public function eliminarCuenta($id) {
        $id = (int)$id;

        try {
            $this->db->beginTransaction();

            $stmtRol = $this->db->prepare("SELECT id FROM usuarios WHERE id = ? AND rol = 'usuario'");
            $stmtRol->execute([$id]);
            if (!$stmtRol->fetch()) {
                $this->db->rollBack();
                return false;
            }

            // Préstamos ligados a copias de su biblioteca
            $this->db->prepare(
                "DELETE p FROM prestamos p
                 INNER JOIN coleccion_usuario cu ON p.coleccion_id = cu.id
                 WHERE cu.usuario_id = ?"
            )->execute([$id]);

            // Biblioteca (copias en estantería; las ediciones del catálogo no se eliminan)
            $this->db->prepare("DELETE FROM coleccion_usuario WHERE usuario_id = ?")->execute([$id]);

            // Valoraciones personales sobre juegos del catálogo
            $this->db->prepare("DELETE FROM valoraciones WHERE usuario_id = ?")->execute([$id]);

            // Historial de propuestas (pendientes, aprobadas o rechazadas); el catálogo ya creado sigue intacto
            $this->db->prepare("DELETE FROM juegos_pendientes WHERE usuario_id = ?")->execute([$id]);

            $this->db->prepare(
                "UPDATE juegos_pendientes SET revisado_por = NULL WHERE revisado_por = ?"
            )->execute([$id]);

            $stmtDel = $this->db->prepare("DELETE FROM usuarios WHERE id = ? AND rol = 'usuario'");
            $stmtDel->execute([$id]);
            $ok = $stmtDel->rowCount() > 0;

            if ($ok) {
                $this->db->commit();
            } else {
                $this->db->rollBack();
            }
            return $ok;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }
}
