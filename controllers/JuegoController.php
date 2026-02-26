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
     * Procesa la propuesta de un nuevo juego enviada por un usuario.
     * Los datos se guardan en las tablas de 'pendientes' para revisión del admin.
     */
    public function proponer() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            session_start();
            $usuario_id = $_SESSION['usuario_id'];

            // Datos del Maestro (Juego)
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
                $sqlEdicion = "INSERT INTO ediciones_pendientes (juego_pendiente_id, plataforma_id, region) 
                               VALUES (:juego_id, :plat_id, :region)";
                $stmtEdicion = $this->pdo->prepare($sqlEdicion);
                $stmtEdicion->execute([
                    ':juego_id' => $juego_pendiente_id,
                    ':plat_id'  => $plataforma_id,
                    ':region'   => $region
                ]);

                $this->pdo->commit();
                
                // Redirigir con éxito
                header('Location: ../vistas/fronted/buscar.php?propuesta=enviada');

            } catch (Exception $e) {
                $this->pdo->rollBack();
                die("Error al procesar la propuesta: " . $e->getMessage());
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

// Lógica para capturar la acción desde la URL (router básico)
if (isset($_GET['action'])) {
    $controller = new JuegoController($pdo);
    if ($_GET['action'] == 'proponer') {
        $controller->proponer();
    }
}
