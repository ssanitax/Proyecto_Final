<?php
class Coleccion {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Añadir un juego a la estantería del usuario
    public function agregarEdicion($usuario_id, $edicion_id, $estado_conservacion = null, $valoracion_personal = null, $idioma_id = null) {
        $sql = "INSERT INTO coleccion_usuario (usuario_id, edicion_id, idioma_id, estado_conservacion, valoracion_personal) 
                VALUES (:usuario_id, :edicion_id, :idioma_id, :estado_conservacion, :valoracion_personal)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':edicion_id' => $edicion_id,
            ':idioma_id' => $idioma_id,
            ':estado_conservacion' => $estado_conservacion,
            ':valoracion_personal' => $valoracion_personal
        ]);
    }

    // Obtener la colección completa de un usuario
    public function obtenerColeccionUsuario($usuario_id) {
        $sql = "SELECT 
                    cu.*, 
                    j.titulo, 
                    e.juego_id,
                    e.edicion_nombre, 
                    e.region, 
                    e.imagen_portada,
                    p.nombre as plataforma,
                    i.nombre as idioma_nombre
                FROM coleccion_usuario cu
                JOIN ediciones e ON cu.edicion_id = e.id
                JOIN juegos j ON e.juego_id = j.id
                JOIN plataformas p ON e.plataforma_id = p.id
                LEFT JOIN idiomas i ON cu.idioma_id = i.id
                LEFT JOIN prestamos pr ON pr.coleccion_id = cu.id AND pr.devuelto = FALSE
                WHERE cu.usuario_id = :usuario_id 
                ORDER BY cu.fecha_adicion DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    /**
     * Obtener estadísticas de la colección:
     * - total_copias: Cantidad total de entradas (incluyendo repetidos)
     * - juegos_distintos: Cantidad de títulos únicos (basado en juego_id)
     */
    public function obtenerEstadisticas($usuario_id) {
        $sql = "SELECT 
                    COUNT(*) as total_copias,
                    COUNT(DISTINCT e.juego_id) as juegos_distintos
                FROM coleccion_usuario cu
                JOIN ediciones e ON cu.edicion_id = e.id
                WHERE cu.usuario_id = :usuario_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetch();
    }

    // Actualizar estado (Pendiente, Jugando, Completado)
    public function actualizarEstado($id, $nuevo_estado) {
        $sql = "UPDATE coleccion_usuario SET estado = :estado WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
    }

    // Registrar valoración personal (1-10) y notas
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