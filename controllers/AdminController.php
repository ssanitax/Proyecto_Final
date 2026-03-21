<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

class AdminController {
    private $pdo;

    public function __construct($pdo) {
        // Solo permitimos que entre si es admin [cite: 106]
        if (!esAdmin()) {
            header('Location: ../vistas/fronted/dashboard.php');
            exit();
        }
        $this->pdo = $pdo;
    }

    public function aprobarJuego() {
        $id_pendiente = $_GET['id'];

        try {
            $this->pdo->beginTransaction();

            // 1. Obtener los datos del juego pendiente [cite: 25]
            $stmt = $this->pdo->prepare("SELECT * FROM juegos_pendientes WHERE id = ?");
            $stmt->execute([$id_pendiente]);
            $propuesta = $stmt->fetch();

            if ($propuesta) {
                // 2. Insertar en la tabla oficial de JUEGOS 
                $sqlJuego = "INSERT INTO juegos (titulo, desarrollador, descripcion) VALUES (?, ?, ?)";
                $stmtJuego = $this->pdo->prepare($sqlJuego);
                $stmtJuego->execute([$propuesta->titulo, $propuesta->desarrollador, $propuesta->descripcion]);
                $nuevo_juego_id = $this->pdo->lastInsertId();

                // 3. Mover la EDICIÓN asociada de pendientes a oficiales [cite: 27, 17]
                $stmtEdic = $this->pdo->prepare("SELECT * FROM ediciones_pendientes WHERE juego_pendiente_id = ?");
                $stmtEdic->execute([$id_pendiente]);
                $edic_pend = $stmtEdic->fetch();

                if ($edic_pend) {
                    $sqlEdicOficial = "INSERT INTO ediciones (juego_id, plataforma_id, region, edicion_nombre) VALUES (?, ?, ?, ?)";
                    $this->pdo->prepare($sqlEdicOficial)->execute([
                        $nuevo_juego_id, 
                        $edic_pend->plataforma_id, 
                        $edic_pend->region, 
                        $edic_pend->edicion_nombre
                    ]);
                }

                // 4. Marcar como aprobado y actualizar quién lo revisó [cite: 25]
                $stmtUpdate = $this->pdo->prepare("UPDATE juegos_pendientes SET estado = 'aprobado', revisado_por = ?, fecha_revision = NOW() WHERE id = ?");
                $stmtUpdate->execute([$_SESSION['usuario_id'], $id_pendiente]);
            }

            $this->pdo->commit();
            header('Location: ../vistas/admin/validar_juegos.php?status=aprobado');
        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Error en la validación: " . $e->getMessage());
        }
    }
}

// Router para el admin
if (isset($_GET['action']) && $_GET['action'] == 'aprobar') {
    $admin = new AdminController($pdo);
    $admin->aprobarJuego();
}
?>