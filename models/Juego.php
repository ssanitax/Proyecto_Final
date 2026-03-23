<?php
class Juego {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Obtener todos los juegos para el catálogo general
    public function obtenerTodos() {
                $sql = "SELECT j.*,
                                             (
                                                     SELECT e.imagen_portada
                                                     FROM ediciones e
                                                     WHERE e.juego_id = j.id
                                                         AND e.imagen_portada IS NOT NULL
                                                         AND e.imagen_portada != ''
                                                     ORDER BY e.id DESC
                                                     LIMIT 1
                                             ) AS imagen_portada
                                FROM juegos j
                                ORDER BY j.titulo ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Buscar juegos (útil para el buscador del MVP)
    public function buscarPorTitulo($termino) {
                $sql = "SELECT j.*,
                                             (
                                                     SELECT e.imagen_portada
                                                     FROM ediciones e
                                                     WHERE e.juego_id = j.id
                                                         AND e.imagen_portada IS NOT NULL
                                                         AND e.imagen_portada != ''
                                                     ORDER BY e.id DESC
                                                     LIMIT 1
                                             ) AS imagen_portada
                                FROM juegos j
                                WHERE j.titulo LIKE :termino
                                ORDER BY j.titulo ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':termino' => "%$termino%"]);
        return $stmt->fetchAll();
    }

    // Obtener info detallada de un juego
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM juegos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
