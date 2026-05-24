<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Juego.php';

class JuegoController {
    private $pdo;
    private $juegoModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->juegoModel = new Juego($pdo);
    }

    /**
     * ACCIÓN 1: Proponer Juego Nuevo
     */
    public function proponer() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION)) { session_start(); }
            $usuario_id = $_SESSION['usuario_id'];

            try {
                $this->pdo->beginTransaction();

                $sqlJuego = "INSERT INTO juegos_pendientes (usuario_id, titulo, desarrollador, estado) VALUES (?, ?, ?, 'pendiente')";
                $stmtJuego = $this->pdo->prepare($sqlJuego);
                $stmtJuego->execute([$usuario_id, "Juego: " . $_POST['titulo'], $_POST['desarrollador']]);
                $juego_pendiente_id = $this->pdo->lastInsertId();

                $bloqueoRegional = isset($_POST['bloqueo_regional']) && $_POST['bloqueo_regional'] === '1' ? 1 : 0;

                $sqlEdicion = "INSERT INTO ediciones_pendientes (juego_pendiente_id, plataforma_id, region, bloqueo_regional, edicion_nombre) VALUES (?, ?, NULL, ?, 'Edición Estándar')";
                $stmtEdicion = $this->pdo->prepare($sqlEdicion);
                $stmtEdicion->execute([$juego_pendiente_id, $_POST['plataforma_id'], $bloqueoRegional]);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/mis_propuestas.php?status=enviado');
                exit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
                die($lang['error_general'] . $e->getMessage());
            }
        }
    }

    /**
     * ACCIÓN 2: Proponer Edición de Juego Existente
     */
    public function proponerEdicionExistente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION)) { session_start(); }
            
            try {
                $this->pdo->beginTransaction();

                $sqlP = "INSERT INTO juegos_pendientes (usuario_id, titulo, estado) SELECT ?, titulo, 'pendiente' FROM juegos WHERE id = ?";
                $stmtP = $this->pdo->prepare($sqlP);
                $stmtP->execute([$_SESSION['usuario_id'], $_POST['juego_id']]);
                $nuevo_id = $this->pdo->lastInsertId();

                $bloqueoRegional = isset($_POST['bloqueo_regional']) && $_POST['bloqueo_regional'] === '1' ? 1 : 0;

                $sql = "INSERT INTO ediciones_pendientes (juego_pendiente_id, juego_id_real, plataforma_id, region, bloqueo_regional, edicion_nombre) VALUES (?, ?, ?, NULL, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$nuevo_id, $_POST['juego_id'], $_POST['plataforma_id'], $bloqueoRegional, $_POST['edicion_nombre']]);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/mis_propuestas.php?status=enviado');
                exit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
                die($lang['error_general'] . $e->getMessage());
            }
        }
    }

    /**
     * ACCIÓN 3: Sugerir Plataforma Independiente
     */
    public function sugerirPlataformaIndependiente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION)) { session_start(); }
            
            $nombre = htmlspecialchars(trim($_POST['nombre_plataforma']));

            try {
                $this->pdo->beginTransaction();
                
                $sqlJp = "INSERT INTO juegos_pendientes (usuario_id, titulo, estado) VALUES (?, ?, 'pendiente')";
                $stmtJp = $this->pdo->prepare($sqlJp);
                $stmtJp->execute([$_SESSION['usuario_id'], "Plataforma: " . $nombre]);
                $jp_id = $this->pdo->lastInsertId();

                $sqlEp = "INSERT INTO ediciones_pendientes (juego_pendiente_id, plataforma_nombre_nueva) VALUES (?, ?)";
                $stmtEp = $this->pdo->prepare($sqlEp);
                $stmtEp->execute([$jp_id, $nombre]);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/mis_propuestas.php?status=enviado');
                exit();
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) { $this->pdo->rollBack(); }
                die($lang['error_critical'] . $e->getMessage());
            }
        }
    }

    /**
     * ACCIÓN 4: Sugerir Región Independiente
     */
    public function sugerirRegionIndependiente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION)) { session_start(); }
            
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
                die($lang['error_critical'] . $e->getMessage());
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
    if ($_GET['action'] == 'sugerir_region_independiente') $controller->sugerirRegionIndependiente();
}