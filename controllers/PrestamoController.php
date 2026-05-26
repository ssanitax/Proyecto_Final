<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

class PrestamoController {
    private $pdo;

    public function __construct($pdo) {
        exigirSesionEnControlador();
        $this->pdo = $pdo;
    }

    private function coleccionPerteneceAUsuario($coleccion_id, $usuario_id) {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM coleccion_usuario WHERE id = ? AND usuario_id = ?"
        );
        $stmt->execute([(int)$coleccion_id, (int)$usuario_id]);
        return (bool)$stmt->fetch();
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../vistas/fronted/mis_prestamos.php');
            exit();
        }

        $usuario_id = (int)$_SESSION['usuario_id'];
        $coleccion_id = (int)($_POST['coleccion_id'] ?? 0);

        if ($coleccion_id <= 0 || !$this->coleccionPerteneceAUsuario($coleccion_id, $usuario_id)) {
            header('Location: ../vistas/fronted/mi_coleccion.php?error=prestamo');
            exit();
        }

        $nombre_persona = htmlspecialchars(trim($_POST['nombre_persona'] ?? ''));
        $fecha_prestamo = $_POST['fecha_prestamo'] ?? date('Y-m-d');

        if ($nombre_persona === '') {
            redirigirFrontend('editar_item.php', 'id=' . $coleccion_id . '&error=prestamo');
        }

        $sql = "INSERT INTO prestamos (coleccion_id, nombre_persona, fecha_prestamo) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $exito = $stmt->execute([$coleccion_id, $nombre_persona, $fecha_prestamo]);

        if ($exito) {
            redirigirFrontend('mis_prestamos.php', 'status=prestado');
        } else {
            redirigirFrontend('mi_coleccion.php', 'error=prestamo');
        }
    }

    public function devolver() {
        $id_prestamo = (int)($_GET['id'] ?? 0);
        $usuario_id = (int)$_SESSION['usuario_id'];

        if ($id_prestamo <= 0) {
            header('Location: ../vistas/fronted/mis_prestamos.php');
            exit();
        }

        $sql = "UPDATE prestamos p
                INNER JOIN coleccion_usuario cu ON p.coleccion_id = cu.id
                SET p.devuelto = TRUE, p.fecha_devolucion = CURDATE()
                WHERE p.id = ? AND cu.usuario_id = ? AND p.devuelto = FALSE";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_prestamo, $usuario_id]);

        if ($stmt->rowCount() > 0) {
            header('Location: ../vistas/fronted/mis_prestamos.php?status=devuelto');
        } else {
            header('Location: ../vistas/fronted/mis_prestamos.php?error=devolver');
        }
        exit();
    }
}

if (isset($_GET['action'])) {
    $controller = new PrestamoController($pdo);
    if ($_GET['action'] == 'registrar') $controller->registrar();
    if ($_GET['action'] == 'devolver') $controller->devolver();
}
