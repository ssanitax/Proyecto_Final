<?php
class Juego {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Obtener todos los juegos para el catálogo general
    public function obtenerTodos() {
        $sql = "SELECT * FROM juegos ORDER BY titulo ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }

    // Buscar juegos (útil para el buscador del MVP)
    public function buscarPorTitulo($termino) {
        $sql = "SELECT * FROM juegos WHERE titulo LIKE :termino";
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
