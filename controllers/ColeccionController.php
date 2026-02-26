<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Coleccion.php';

class ColeccionController {
    private $coleccionModel;

    public function __construct($pdo) {
        $this->coleccionModel = new Coleccion($pdo);
    }

    // Carga la vista de la estantería del usuario logueado [cite: 149]
    public function mostrarEstanteria() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: login.php');
            exit();
        }
        
        $juegos = $this->coleccionModel->obtenerColeccionUsuario($_SESSION['usuario_id']);
        include __DIR__ . '/../vistas/fronted/mi_coleccion.php';
    }

    // Procesa el formulario de añadir juego [cite: 157]
    public function añadirJuego($edicion_id, $conservacion) {
        if ($this->coleccionModel->agregarEdicion($_SESSION['usuario_id'], $edicion_id, $conservacion)) {
            header('Location: mi_coleccion.php?status=added');
        }
    }
}
