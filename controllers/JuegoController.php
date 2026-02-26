<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Juego.php';

class JuegoController {
    private $pdo;
    private $juegoModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->juegoModel = new Juego($pdo);
    }

    /**
     * ACCIÓN 1: Proponer un juego totalmente nuevo (Maestro + Primera Edición)
     */
    public function proponer() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION)) { session_start(); }
            $usuario_id = $_SESSION['usuario_id'];

            // Datos del Maestro
            $titulo = htmlspecialchars($_POST['titulo']);
            $desarrollador = htmlspecialchars($_POST['desarrollador']);
            
            // Datos de la Edición
            $plataforma_id = $_POST['plataforma_id'];
            $region = $_POST['region'];

            try {
                $this->pdo->beginTransaction();

                // 1. Insertar en juegos_pendientes
                $sqlJuego = "INSERT INTO juegos_pendientes (usuario_id, titulo, desarrollador, estado) 
                             VALUES (:user, :titulo, :dev, 'pendiente')";
                $stmtJuego = $this->pdo->prepare($sqlJuego);
                $stmtJuego->execute([
                    ':user'   => $usuario_id,
                    ':titulo' => $titulo,
                    ':dev'    => $desarrollador
                ]);

                $juego_pendiente_id = $this->pdo->lastInsertId();

                // 2. Insertar la edición asociada en ediciones_pendientes
                $sqlEdicion = "INSERT INTO ediciones_pendientes (juego_pendiente_id, plataforma_id, region, edicion_nombre) 
                               VALUES (:juego_id, :plat_id, :region, 'Edición Estándar')";
                $stmtEdicion = $this->pdo->prepare($sqlEdicion);
                $stmtEdicion->execute([
                    ':juego_id' => $juego_pendiente_id,
                    ':plat_id'  => $plataforma_id,
                    ':region'   => $region
                ]);

                $this->pdo->commit();
                header('Location: ../vistas/fronted/buscar.php?propuesta=enviada');
                exit();

            } catch (Exception $e) {
                $this->pdo->rollBack();
                die("Error al procesar la propuesta de nuevo juego: " . $e->getMessage());
            }
        }
    }

    /**
     * ACCIÓN 2: Proponer una nueva edición/plataforma para un juego que YA existe
     */
    public function proponerEdicionExistente() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_SESSION)) { session_start(); }
            
            $juego_id = $_POST['juego_id']; // ID del juego maestro real
            $plataforma_id = $_POST['plataforma_id'];
            $edicion_nombre = htmlspecialchars($_POST['edicion_nombre']);
            $region = $_POST['region'];

            try {
                // Insertamos directamente en ediciones_pendientes. 
                // Nota: Usamos juego_id (maestro) en lugar de juego_pendiente_id.
                // Tu SQL puede requerir un pequeño ajuste o podemos usar una lógica de revisión.
                $sql = "INSERT INTO ediciones_pendientes (juego_id_maestro, plataforma_id, region, edicion_nombre) 
                        VALUES (:j_id, :p_id, :reg, :nom)";
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    ':j_id' => $juego_id,
                    ':p_id' => $plataforma_id,
                    ':reg'  => $region,
                    ':nom'  => $edicion_nombre
                ]);

                header('Location: ../vistas/fronted/juego_detalle.php?id=' . $juego_id . '&propuesta=enviada');
                exit();
            } catch (Exception $e) {
                die("Error al proponer edición: " . $e->getMessage());
            }
        }
    }

    /**
     * Método para que el buscador llame a la lógica de búsqueda
     */
    public function buscar($termino) {
        return $this->juegoModel->buscarPorTitulo($termino);
    }
}

// --- ROUTER DE ACCIONES ---
if (isset($_GET['action'])) {
    $controller = new JuegoController($pdo);
    
    if ($_GET['action'] == 'proponer') {
        $controller->proponer();
    }
    
    if ($_GET['action'] == 'proponer_edicion_existente') {
        $controller->proponerEdicionExistente();
    }
}
