<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Coleccion.php';

class ColeccionController {
    private $pdo;
    private $coleccionModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->coleccionModel = new Coleccion($pdo);
    }

    // 1. AGREGAR JUEGO (Ya lo tenías)
    public function agregar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            $usuario_id = $_SESSION['usuario_id'];
            $edicion_id = $_POST['edicion_id'];
            $exito = $this->coleccionModel->agregarEdicion($usuario_id, $edicion_id, 'bueno');
            header('Location: ../vistas/fronted/mi_coleccion.php?status=' . ($exito ? 'success' : 'error'));
            exit();
        }
    }

    // 2. ACTUALIZAR JUEGO (La parte que te falta)
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            $id = $_POST['id'];
            $estado = $_POST['estado'];
            $valoracion = $_POST['valoracion'];
            $notas = $_POST['notas'];

            // Llamamos al modelo para actualizar
            $sql = "UPDATE coleccion_usuario SET estado = ?, valoracion_personal = ?, notas = ? WHERE id = ? AND usuario_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $exito = $stmt->execute([$estado, $valoracion, $notas, $id, $_SESSION['usuario_id']]);

            header('Location: ../vistas/fronted/mi_coleccion.php?updated=' . ($exito ? '1' : '0'));
            exit();
        }
    }

    // 3. ELIMINAR JUEGO
    public function eliminar() {
        session_start();
        $id = $_GET['id'] ?? null;
        if ($id) {
            $sql = "DELETE FROM coleccion_usuario WHERE id = ? AND usuario_id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id, $_SESSION['usuario_id']]);
        }
        header('Location: ../vistas/fronted/mi_coleccion.php?deleted=1');
        exit();
    }
}

// ROUTER: Ejecuta la acción según el parámetro ?action=
if (isset($_GET['action'])) {
    $controller = new ColeccionController($pdo);
    
    if ($_GET['action'] == 'agregar') $controller->agregar();
    if ($_GET['action'] == 'actualizar') $controller->actualizar();
    if ($_GET['action'] == 'eliminar') $controller->eliminar();
}
