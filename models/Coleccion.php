<?php
class Coleccion {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Añadir un juego a la estantería del usuario [cite: 83]
    public function agregarEdicion($usuario_id, $edicion_id, $estado_conservacion) {
        $sql = "INSERT INTO coleccion_usuario (usuario_id, edicion_id, estado_conservacion) 
                VALUES (:usuario_id, :edicion_id, :estado_conservacion)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':edicion_id' => $edicion_id,
            ':estado_conservacion' => $estado_conservacion
        ]);
    }

    // Obtener la colección completa de un usuario (para el "Shelf View") [cite: 152]
    public function obtenerColeccionUsuario($usuario_id) {
        $sql = "SELECT cu.*, e.edicion_nombre, e.imagen_portada, j.titulo, p.nombre as plataforma 
                FROM coleccion_usuario cu
                JOIN ediciones e ON cu.edicion_id = e.id
                JOIN juegos j ON e.juego_id = j.id
                JOIN plataformas p ON e.plataforma_id = p.id
                WHERE cu.usuario_id = :usuario_id
                ORDER BY cu.fecha_adicion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    // Actualizar estado (Pendiente, Jugando, Completado) [cite: 14, 151]
    public function actualizarEstado($id, $nuevo_estado) {
        $sql = "UPDATE coleccion_usuario SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
    }

    // Registrar valoración personal (1-10) y notas [cite: 155]
    public function valorarCopia($id, $nota, $comentario) {
        $sql = "UPDATE coleccion_usuario 
                SET valoracion_personal = :nota, notas = :notas 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nota' => $nota,
            ':notas' => $comentario,
            ':id' => $id
        ]);
    }
}
