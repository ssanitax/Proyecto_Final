<?php
require_once __DIR__ . '/../includes/portadas.php';

class Juego {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Obtener todos los juegos para el catálogo general
    public function obtenerTodos() {
        $portada = sqlSelectPortadaMasRecientePorJuego('j.id');
        $sql = "SELECT j.*, {$portada} AS imagen_portada
                FROM juegos j
                WHERE EXISTS (SELECT 1 FROM ediciones e WHERE e.juego_id = j.id)
                ORDER BY j.titulo ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Buscar juegos (útil para el buscador del MVP)
    public function buscarPorTitulo($termino) {
        $portada = sqlSelectPortadaMasRecientePorJuego('j.id');
        $sql = "SELECT j.*, {$portada} AS imagen_portada
                FROM juegos j
                WHERE j.titulo LIKE :termino
                  AND EXISTS (SELECT 1 FROM ediciones e WHERE e.juego_id = j.id)
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
