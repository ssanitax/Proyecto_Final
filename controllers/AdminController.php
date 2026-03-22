<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

class AdminController {
    private $pdo;

    public function __construct($pdo) {
        // Protección: solo los administradores de Bengala pueden ejecutar estas acciones 
        if (!esAdmin()) {
            header('Location: ../vistas/fronted/dashboard.php');
            exit();
        }
        $this->pdo = $pdo;
    }

    /**
     * Procesa la aprobación de una propuesta (Juego Nuevo o Edición Nueva)
     */
    public function aprobarPropuesta() {
        $id_pendiente = $_GET['id'] ?? null;
        if (!$id_pendiente) {
            header('Location: ../vistas/admin/validar_juegos.php?error=no_id');
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Obtener la propuesta de juego y su edición asociada
            $stmt = $this->pdo->prepare("
                SELECT jp.*, ep.id as edicion_pend_id, ep.plataforma_id, ep.region, ep.edicion_nombre, ep.juego_id_real
                FROM juegos_pendientes jp
                LEFT JOIN ediciones_pendientes ep ON ep.juego_pendiente_id = jp.id
                WHERE jp.id = ?
            ");
            $stmt->execute([$id_pendiente]);
            $propuesta = $stmt->fetch();

            if (!$propuesta) throw new Exception("Propuesta no encontrada.");

            $juego_id_final = null;

            // 2. LÓGICA DE APROBACIÓN
            if ($propuesta->juego_id_real) {
                // CASO A: Es una edición nueva para un juego que YA existe
                $juego_id_final = $propuesta->juego_id_real;
            } else {
                // CASO B: Es un JUEGO NUEVO. Insertamos en la tabla maestra 'juegos'
                $sqlJuego = "INSERT INTO juegos (titulo, desarrollador, descripcion) VALUES (?, ?, ?)";
                $stmtJuego = $this->pdo->prepare($sqlJuego);
                $stmtJuego->execute([$propuesta->titulo, $propuesta->desarrollador, $propuesta->descripcion]);
                $juego_id_final = $this->pdo->lastInsertId();
            }

            // 3. Insertar la EDICIÓN en la tabla oficial 'ediciones'
            if ($juego_id_final && $propuesta->plataforma_id) {
                $sqlEdicion = "INSERT INTO ediciones (juego_id, plataforma_id, region, edicion_nombre) VALUES (?, ?, ?, ?)";
                $stmtEdic = $this->pdo->prepare($sqlEdicion);
                $stmtEdic->execute([
                    $juego_id_final,
                    $propuesta->plataforma_id,
                    $propuesta->region,
                    $propuesta->edicion_nombre ?? 'Edición Estándar'
                ]);
            }

            // 4. Actualizar estado de la propuesta y registrar quién la revisó
            $stmtUpdate = $this->pdo->prepare("
                UPDATE juegos_pendientes 
                SET estado = 'aprobado', revisado_por = ?, fecha_revision = NOW() 
                WHERE id = ?
            ");
            $stmtUpdate->execute([$_SESSION['usuario_id'], $id_pendiente]);

            $this->pdo->commit();
            header('Location: ../vistas/admin/validar_juegos.php?status=aprobado');
            exit();

        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Error crítico en Bengala Admin: " . $e->getMessage());
        }
    }

    /**
     * Rechaza una propuesta sin añadirla al catálogo
     */
    public function rechazarPropuesta() {
        $id_pendiente = $_GET['id'] ?? null;
        if ($id_pendiente) {
            $stmt = $this->pdo->prepare("UPDATE juegos_pendientes SET estado = 'rechazado', revisado_por = ?, fecha_revision = NOW() WHERE id = ?");
            $stmt->execute([$_SESSION['usuario_id'], $id_pendiente]);
        }
        header('Location: ../vistas/admin/validar_juegos.php?status=rechazado');
        exit();
    }
}

// ROUTER DE ACCIONES ADMIN
if (isset($_GET['action'])) {
    $admin = new AdminController($pdo);
    if ($_GET['action'] == 'aprobar') $admin->aprobarPropuesta();
    if ($_GET['action'] == 'rechazar') $admin->rechazarPropuesta();
}