<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

class AdminController {
    private $pdo;

    public function __construct($pdo) {
        // Protección: solo los administradores de Bengala pueden ejecutar estas acciones [cite: 74, 694]
        if (!esAdmin()) {
            header('Location: ../vistas/fronted/dashboard.php');
            exit();
        }
        $this->pdo = $pdo;
    }

    /**
     * Procesa la aprobación de una propuesta permitiendo correcciones previas.
     * Gestiona automáticamente la creación de plataformas si no existen.
     */
    public function aprobarPropuesta() {
        $id_pendiente = $_GET['id'] ?? null;
        if (!$id_pendiente) {
            header('Location: ../vistas/admin/validar_juegos.php?error=no_id');
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Obtener la propuesta original [cite: 79, 80, 699, 700]
            $stmt = $this->pdo->prepare("
                SELECT jp.*, ep.plataforma_id, ep.region, ep.edicion_nombre, ep.juego_id_real, ep.plataforma_nombre_nueva
                FROM juegos_pendientes jp
                LEFT JOIN ediciones_pendientes ep ON ep.juego_pendiente_id = jp.id
                WHERE jp.id = ?
            ");
            $stmt->execute([$id_pendiente]);
            $propuesta = $stmt->fetch();

            if (!$propuesta) throw new Exception("Propuesta no encontrada.");

            // 2. Recoger datos corregidos del formulario [cite: 704-707]
            $tituloCorregido = $_POST['corregir_titulo'] ?? $propuesta->titulo;
            $devCorregido    = $_POST['corregir_dev']    ?? $propuesta->desarrollador;
            $regionCorregida = $_POST['corregir_region'] ?? $propuesta->region;
            $platCorregida   = $_POST['corregir_plataforma'] ?? null;

            // 3. Gestionar Plataforma (Buscar existente o crear nueva por corrección)
            $plataforma_id_final = $propuesta->plataforma_id;
            if ($platCorregida) {
                $stPlat = $this->pdo->prepare("SELECT id FROM plataformas WHERE nombre = ?");
                $stPlat->execute([trim($platCorregida)]);
                $existe = $stPlat->fetch();

                if ($existe) {
                    $plataforma_id_final = $existe->id;
                } else {
                    $insPlat = $this->pdo->prepare("INSERT INTO plataformas (nombre) VALUES (?)");
                    $insPlat->execute([trim($platCorregida)]);
                    $plataforma_id_final = $this->pdo->lastInsertId();
                }
            }

            // 4. LÓGICA SEGÚN EL TIPO DE PROPUESTA (Detectada por prefijo)
            if (strpos($tituloCorregido, 'Plataforma: ') === 0) {
                // Ya se gestionó en el paso 3 si era una corrección de nombre
            } elseif (strpos($tituloCorregido, 'Región: ') === 0) {
                // Lógica informativa para regiones independientes
            } else {
                // CASO: JUEGO o EDICIÓN [cite: 82-84, 702-703]
                $tituloFinal = trim(str_replace('Juego: ', '', $tituloCorregido));
                $juego_id_final = $propuesta->juego_id_real;

                if (!$juego_id_final) {
                    $sqlJuego = "INSERT INTO juegos (titulo, desarrollador) VALUES (?, ?)";
                    $stmtJuego = $this->pdo->prepare($sqlJuego);
                    $stmtJuego->execute([$tituloFinal, $devCorregido]);
                    $juego_id_final = $this->pdo->lastInsertId();
                }

                if ($juego_id_final && $plataforma_id_final) {
                    $sqlEdicion = "INSERT INTO ediciones (juego_id, plataforma_id, region, edicion_nombre) VALUES (?, ?, ?, ?)";
                    $this->pdo->prepare($sqlEdicion)->execute([
                        $juego_id_final,
                        $plataforma_id_final,
                        $regionCorregida,
                        $propuesta->edicion_nombre ?? 'Edición Estándar'
                    ]);
                }
            }

            // 5. Marcar como aprobado [cite: 87, 88, 707, 708]
            $stmtUpdate = $this->pdo->prepare("UPDATE juegos_pendientes SET estado = 'aprobado', revisado_por = ?, fecha_revision = NOW() WHERE id = ?");
            $stmtUpdate->execute([$_SESSION['usuario_id'], $id_pendiente]);

            $this->pdo->commit();
            header('Location: ../vistas/admin/validar_juegos.php?status=aprobado');
            exit();

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            die("Error crítico en validación: " . $e->getMessage());
        }
    }

    public function rechazarPropuesta() {
        $id_pendiente = $_GET['id'] ?? null;
        if ($id_pendiente) {
            $stmt = $this->pdo->prepare("UPDATE juegos_pendientes SET estado = 'rechazado', revisado_por = ?, fecha_revision = NOW() WHERE id = ?");
            $stmt->execute([$_SESSION['usuario_id'], $id_pendiente]);
        }
        header('Location: ../vistas/admin/validar_juegos.php?status=rechazado');
        exit();
    }

    // --- ACCIONES DE REGISTRO DIRECTO (ALTA DIRECTIVA) ---

    public function registrarPlataformaDirecta() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = htmlspecialchars(trim($_POST['nombre']));
            $stmt = $this->pdo->prepare("INSERT INTO plataformas (nombre) VALUES (?)");
            $stmt->execute([$nombre]);
            header('Location: ../vistas/admin/registrar_directo.php?status=success');
            exit();
        }
    }

    public function registrarRegionDirecta() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Confirmación de éxito para regiones (se guardan como texto en las ediciones)
            header('Location: ../vistas/admin/registrar_directo.php?status=success');
            exit();
        }
    }

    public function registrarJuegoDirecto() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $titulo = htmlspecialchars(trim($_POST['titulo']));
            $dev = htmlspecialchars(trim($_POST['desarrollador']));
            $stmt = $this->pdo->prepare("INSERT INTO juegos (titulo, desarrollador) VALUES (?, ?)");
            $stmt->execute([$titulo, $dev]);
            header('Location: ../vistas/admin/registrar_directo.php?status=success');
            exit();
        }
    }
}

// --- ROUTER DE ACCIONES ADMIN ---
if (isset($_GET['action'])) {
    $admin = new AdminController($pdo);
    if ($_GET['action'] == 'aprobar') $admin->aprobarPropuesta();
    if ($_GET['action'] == 'rechazar') $admin->rechazarPropuesta();
    if ($_GET['action'] == 'registrar_plataforma') $admin->registrarPlataformaDirecta();
    if ($_GET['action'] == 'registrar_region') $admin->registrarRegionDirecta();
    if ($_GET['action'] == 'registrar_juego') $admin->registrarJuegoDirecto();
}