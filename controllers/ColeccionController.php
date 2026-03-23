<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Coleccion.php';

class ColeccionController {
    private $pdo;
    private $coleccionModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->coleccionModel = new Coleccion($pdo);
    }

    // 1. AGREGAR JUEGO (El juego ya está registrado en la base de datos, solo se añade a biblioteca)
    // Busca este bloque en ColeccionController.php
    public function agregar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION)) { session_start(); }
            
            $usuario_id = $_SESSION['usuario_id'];
            $edicion_id = $_POST['edicion_id'] ?? null;

            if (!$edicion_id) {
                header('Location: ../vistas/fronted/buscar.php?error=no_selection');
                exit();
            }

            try {
                // Si el usuario ya valoró este juego en otra copia, heredamos esa nota.
                $sqlValoracionExistente = "SELECT cu.valoracion_personal
                                           FROM coleccion_usuario cu
                                           JOIN ediciones e_copia ON e_copia.id = cu.edicion_id
                                           JOIN ediciones e_nueva ON e_nueva.id = ?
                                           WHERE cu.usuario_id = ?
                                             AND e_copia.juego_id = e_nueva.juego_id
                                             AND cu.valoracion_personal IS NOT NULL
                                           ORDER BY cu.id DESC
                                           LIMIT 1";
                $stmtValoracionExistente = $this->pdo->prepare($sqlValoracionExistente);
                $stmtValoracionExistente->execute([$edicion_id, $usuario_id]);
                $valoracionExistente = $stmtValoracionExistente->fetchColumn();

                // Ahora simplemente insertamos. Al no haber restricción UNIQUE, 
                // se creará una nueva fila cada vez que el usuario pulse el botón.
                $this->coleccionModel->agregarEdicion(
                    $usuario_id,
                    $edicion_id,
                    'bueno',
                    $valoracionExistente !== false ? $valoracionExistente : null
                );
                header("Location: ../vistas/fronted/mi_coleccion.php?status=success");
                exit();
            } catch (PDOException $e) {
                die($lang['error_database'] . $e->getMessage());
            }
        }
    }

    // 2. ACTUALIZAR JUEGO
    public function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            $id = $_POST['id'];
            $estado = $_POST['estado'];
            $valoracion = isset($_POST['valoracion']) && $_POST['valoracion'] !== '' ? (int)$_POST['valoracion'] : null;
            $notas = $_POST['notas'];

            try {
                $this->pdo->beginTransaction();

                // 1) Actualizamos estado y notas solo de la copia editada
                $sql = "UPDATE coleccion_usuario SET estado = ?, notas = ? WHERE id = ? AND usuario_id = ?";
                $stmt = $this->pdo->prepare($sql);
                $exito = $stmt->execute([$estado, $notas, $id, $_SESSION['usuario_id']]);

                // 2) Detectamos el juego al que pertenece esa copia
                $sqlJuego = "SELECT e.juego_id
                             FROM coleccion_usuario cu
                             JOIN ediciones e ON e.id = cu.edicion_id
                             WHERE cu.id = ? AND cu.usuario_id = ?
                             LIMIT 1";
                $stmtJuego = $this->pdo->prepare($sqlJuego);
                $stmtJuego->execute([$id, $_SESSION['usuario_id']]);
                $juegoId = $stmtJuego->fetchColumn();

                // 3) Propagamos la valoración a TODAS las copias del mismo juego del usuario
                if ($juegoId) {
                    $sqlPropaga = "UPDATE coleccion_usuario cu
                                   JOIN ediciones e ON e.id = cu.edicion_id
                                   SET cu.valoracion_personal = ?
                                   WHERE cu.usuario_id = ?
                                     AND e.juego_id = ?";
                    $stmtPropaga = $this->pdo->prepare($sqlPropaga);
                    $stmtPropaga->execute([$valoracion, $_SESSION['usuario_id'], $juegoId]);
                }

                $this->pdo->commit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                $exito = false;
            }

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
