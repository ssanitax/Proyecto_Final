<?php
require_once __DIR__ . '/../config/config.php';

class PrestamoController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            $coleccion_id = $_POST['coleccion_id'];
            $nombre_persona = htmlspecialchars($_POST['nombre_persona']);
            $fecha_prestamo = $_POST['fecha_prestamo'];

            $sql = "INSERT INTO prestamos (coleccion_id, nombre_persona, fecha_prestamo) VALUES (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $exito = $stmt->execute([$coleccion_id, $nombre_persona, $fecha_prestamo]);

            if ($exito) {
                header('Location: ../vistas/fronted/mis_prestamos.php?status=prestado');
            } else {
                header('Location: ../vistas/fronted/mi_coleccion.php?error=prestamo');
            }
            exit();
        }
    }

    public function devolver() {
        $id_prestamo = $_GET['id'];
        $sql = "UPDATE prestamos SET devuelto = TRUE, fecha_devolucion = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id_prestamo]);
        
        header('Location: ../vistas/fronted/mis_prestamos.php?status=devuelto');
        exit();
    }
}

// Router para préstamos
if (isset($_GET['action'])) {
    $controller = new PrestamoController($pdo);
    if ($_GET['action'] == 'registrar') $controller->registrar();
    if ($_GET['action'] == 'devolver') $controller->devolver();
}