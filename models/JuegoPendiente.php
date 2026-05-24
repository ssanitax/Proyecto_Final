<?php

class JuegoPendiente {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Obtiene todas las propuestas enviadas por un usuario específico.
     * Incluye el nombre de la plataforma y la región para que el usuario
     * sepa exactamente qué versión de su juego está en revisión [cite: 568-570].
     */
    public function obtenerPropuestasPorUsuario($usuario_id) {
        // Realizamos JOINs para traer el nombre de la consola asociada a la propuesta
        $sql = "SELECT jp.*, p.nombre as plataforma_nombre, ep.region, ep.bloqueo_regional 
                FROM juegos_pendientes jp
                LEFT JOIN ediciones_pendientes ep ON ep.juego_pendiente_id = jp.id
                LEFT JOIN plataformas p ON ep.plataforma_id = p.id
                WHERE jp.usuario_id = :usuario_id
                ORDER BY jp.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }

    /**
     * Cuenta cuántas propuestas tiene un usuario todavía en estado 'pendiente'.
     * Útil para mostrar avisos o contadores en la interfaz de usuario.
     */
    public function contarPendientesUsuario($usuario_id) {
        $sql = "SELECT COUNT(*) FROM juegos_pendientes 
                WHERE usuario_id = :usuario_id AND estado = 'pendiente'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchColumn();
    }

    /**
     * Obtiene el detalle de una propuesta específica por su ID[cite: 568].
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM juegos_pendientes WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}