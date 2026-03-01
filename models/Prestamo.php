<?php
class Prestamo {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Obtener los préstamos del usuario logueado
    public function obtenerPrestamosUsuario($usuario_id) {
        $sql = "SELECT p.*, j.titulo, e.edicion_nombre, plat.nombre as plataforma_nombre 
                FROM prestamos p
                JOIN coleccion_usuario cu ON p.coleccion_id = cu.id
                JOIN ediciones e ON cu.edicion_id = e.id
                JOIN juegos j ON e.juego_id = j.id
                JOIN plataformas plat ON e.plataforma_id = plat.id
                WHERE cu.usuario_id = :usuario_id
                ORDER BY p.fecha_prestamo DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuario_id]);
        return $stmt->fetchAll();
    }
}
