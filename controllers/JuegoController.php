<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/portadas.php';
require_once __DIR__ . '/../includes/catalogo.php';
require_once __DIR__ . '/../models/Juego.php';

class JuegoController {
    private $pdo;
    private $juegoModel;

    public function __construct($pdo) {
        exigirSesionEnControlador();
        $this->pdo = $pdo;
        $this->juegoModel = new Juego($pdo);
    }

    private function redirigirErrorPropuesta($ruta, $codigo) {
        header('Location: ' . $ruta . '?error=' . urlencode($codigo));
        exit();
    }

    /**
     * ACCIÓN 1: Proponer Juego Nuevo
     */
    public function proponer() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario_id = $_SESSION['usuario_id'];
            $titulo = trim($_POST['titulo'] ?? '');
            $desarrollador = trim($_POST['desarrollador'] ?? '');
            $plataforma_id = (int)($_POST['plataforma_id'] ?? 0);
            $fecha = trim($_POST['fecha_lanzamiento'] ?? '');
            $idiomasDisponibles = array_unique(array_filter(array_map('intval', (array)($_POST['idiomas_disponibles'] ?? []))));

            if ($titulo === '') {
                $this->redirigirErrorPropuesta('../vistas/fronted/registrar_nuevo.php', 'missing_title');
            }
            if ($plataforma_id <= 0) {
                $this->redirigirErrorPropuesta('../vistas/fronted/registrar_nuevo.php', 'missing_platform');
            }
            if ($fecha === '') {
                $this->redirigirErrorPropuesta('../vistas/fronted/registrar_nuevo.php', 'missing_date');
            }

            try {
                $this->pdo->beginTransaction();

                $portadaSugerida = isset($_FILES['portada'])
                    ? guardarImagenPortadaOpcional($_FILES['portada'])
                    : null;

                $sqlJuego = "INSERT INTO juegos_pendientes (usuario_id, titulo, desarrollador, fecha_lanzamiento, estado)
                             VALUES (?, ?, ?, ?, 'pendiente')";
                $stmtJuego = $this->pdo->prepare($sqlJuego);
                $stmtJuego->execute([
                    $usuario_id,
                    'Juego: ' . $titulo,
                    $desarrollador !== '' ? $desarrollador : null,
                    $fecha
                ]);
                $juego_pendiente_id = $this->pdo->lastInsertId();

                $bloqueoRegional = isset($_POST['bloqueo_regional']) && $_POST['bloqueo_regional'] === '1' ? 1 : 0;

                $sqlEdicion = "INSERT INTO ediciones_pendientes
                    (juego_pendiente_id, plataforma_id, imagen_portada_sugerida, region, bloqueo_regional, edicion_nombre)
                    VALUES (?, ?, ?, NULL, ?, 'Edición Estándar')";
                $stmtEdicion = $this->pdo->prepare($sqlEdicion);
                $stmtEdicion->execute([
                    $juego_pendiente_id,
                    $plataforma_id,
                    $portadaSugerida,
                    $bloqueoRegional
                ]);

                guardarIdiomasPropuestaPendiente($this->pdo, (int)$juego_pendiente_id, $idiomasDisponibles);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/mis_propuestas.php?status=enviado');
                exit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                die(__('error_general') . $e->getMessage());
            }
        }
    }

    /**
     * ACCIÓN 2: Proponer Edición de Juego Existente
     */
    public function proponerEdicionExistente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $plataforma_id = (int)($_POST['plataforma_id'] ?? 0);
            $fecha = trim($_POST['fecha_lanzamiento'] ?? '');
            $juego_id = (int)($_POST['juego_id'] ?? 0);

            if ($juego_id <= 0 || $plataforma_id <= 0) {
                $this->redirigirErrorPropuesta('../vistas/fronted/buscar.php', 'missing_platform');
            }
            if ($fecha === '') {
                $this->redirigirErrorPropuesta('../vistas/fronted/proponer_edicion.php?juego_id=' . $juego_id, 'missing_date');
            }

            try {
                $this->pdo->beginTransaction();

                $portadaSugerida = isset($_FILES['portada'])
                    ? guardarImagenPortadaOpcional($_FILES['portada'])
                    : null;

                $sqlP = "INSERT INTO juegos_pendientes (usuario_id, titulo, fecha_lanzamiento, estado)
                         SELECT ?, titulo, ?, 'pendiente' FROM juegos WHERE id = ?";
                $stmtP = $this->pdo->prepare($sqlP);
                $stmtP->execute([$_SESSION['usuario_id'], $fecha, $juego_id]);
                $nuevo_id = $this->pdo->lastInsertId();

                $bloqueoRegional = isset($_POST['bloqueo_regional']) && $_POST['bloqueo_regional'] === '1' ? 1 : 0;

                $sql = "INSERT INTO ediciones_pendientes
                    (juego_pendiente_id, juego_id_real, plataforma_id, imagen_portada_sugerida, region, bloqueo_regional, edicion_nombre)
                    VALUES (?, ?, ?, ?, NULL, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $nuevo_id,
                    $juego_id,
                    $plataforma_id,
                    $portadaSugerida,
                    $bloqueoRegional,
                    trim($_POST['edicion_nombre'] ?? 'Edición Estándar')
                ]);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/mis_propuestas.php?status=enviado');
                exit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }
                die(__('error_general') . $e->getMessage());
            }
        }
    }

    /**
     * ACCIÓN 3: Sugerir Plataforma Independiente
     */
    public function sugerirPlataformaIndependiente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $nombre = htmlspecialchars(trim($_POST['nombre_plataforma']));
            $fechaPlataforma = trim($_POST['fecha_lanzamiento_plataforma'] ?? '');

            if ($fechaPlataforma === '') {
                header('Location: ../vistas/fronted/proponer_plataforma.php?error=missing_date');
                exit();
            }

            try {
                $this->pdo->beginTransaction();
                
                $sqlJp = "INSERT INTO juegos_pendientes (usuario_id, titulo, estado) VALUES (?, ?, 'pendiente')";
                $stmtJp = $this->pdo->prepare($sqlJp);
                $stmtJp->execute([$_SESSION['usuario_id'], "Plataforma: " . $nombre]);
                $jp_id = $this->pdo->lastInsertId();

                $sqlEp = "INSERT INTO ediciones_pendientes (juego_pendiente_id, plataforma_nombre_nueva, fecha_plataforma_sugerida) VALUES (?, ?, ?)";
                $stmtEp = $this->pdo->prepare($sqlEp);
                $stmtEp->execute([$jp_id, $nombre, $fechaPlataforma]);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/mis_propuestas.php?status=enviado');
                exit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
                die(__('error_critical') . $e->getMessage());
            }
        }
    }

    /**
     * ACCIÓN 4: Sugerir Región Independiente
     */
    public function sugerirIdiomaIndependiente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $nombre = htmlspecialchars(trim($_POST['nombre_idioma']));

            try {
                $this->pdo->beginTransaction();

                $sqlJp = "INSERT INTO juegos_pendientes (usuario_id, titulo, estado) VALUES (?, ?, 'pendiente')";
                $stmtJp = $this->pdo->prepare($sqlJp);
                $stmtJp->execute([$_SESSION['usuario_id'], "Idioma: " . $nombre]);
                $jp_id = $this->pdo->lastInsertId();

                $sqlEp = "INSERT INTO ediciones_pendientes (juego_pendiente_id, idioma_nombre_nueva) VALUES (?, ?)";
                $stmtEp = $this->pdo->prepare($sqlEp);
                $stmtEp->execute([$jp_id, $nombre]);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/mis_propuestas.php?status=enviado');
                exit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
                die(__('error_critical') . $e->getMessage());
            }
        }
    }

    public function sugerirRegionIndependiente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $region = htmlspecialchars(trim($_POST['nombre_region']));

            try {
                $this->pdo->beginTransaction();
                
                // 1. Registro maestro para el historial del usuario
                $sqlJp = "INSERT INTO juegos_pendientes (usuario_id, titulo, estado) VALUES (?, ?, 'pendiente')";
                $stmtJp = $this->pdo->prepare($sqlJp);
                $stmtJp->execute([$_SESSION['usuario_id'], "Región: " . $region]);
                $jp_id = $this->pdo->lastInsertId();

                // 2. Registro de la región en ediciones_pendientes
                $sqlEp = "INSERT INTO ediciones_pendientes (juego_pendiente_id, region) VALUES (?, ?)";
                $stmtEp = $this->pdo->prepare($sqlEp);
                $stmtEp->execute([$jp_id, $region]);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/mis_propuestas.php?status=enviado');
                exit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
                die(__('error_critical') . $e->getMessage());
            }
        }
    }

    public function buscar($termino) {
        return $this->juegoModel->buscarPorTitulo($termino);
    }
}

// --- ROUTER ---
if (isset($_GET['action'])) {
    $controller = new JuegoController($pdo);
    if ($_GET['action'] == 'proponer') $controller->proponer();
    if ($_GET['action'] == 'proponer_edicion_existente') $controller->proponerEdicionExistente();
    if ($_GET['action'] == 'sugerir_plataforma_independiente') $controller->sugerirPlataformaIndependiente();
    if ($_GET['action'] == 'sugerir_idioma_independiente') $controller->sugerirIdiomaIndependiente();
    if ($_GET['action'] == 'sugerir_region_independiente') $controller->sugerirRegionIndependiente();
}