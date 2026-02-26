<?php
class Edicion {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Obtener todas las ediciones de un juego concreto (ej: todos los Zeldas)
    public function obtenerPorJuego($juego_id) {
        $sql = "SELECT e.*, p.nombre as plataforma_nombre 
                FROM ediciones e 
                JOIN plataformas p ON e.plataforma_id = p.id 
                WHERE e.juego_id = :juego_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':juego_id' => $juego_id]);
        return $stmt->fetchAll();
    }

    // Obtener detalle de una edición específica para añadir a la colección
    public function obtenerDetalle($id) {
        $sql = "SELECT e.*, j.titulo, p.nombre as plataforma_nombre 
                FROM ediciones e
                JOIN juegos j ON e.juego_id = j.id
                JOIN plataformas p ON e.plataforma_id = p.id
                WHERE e.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
