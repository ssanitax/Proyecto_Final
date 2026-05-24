<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Usuario.php';

class PerfilController {
    private $pdo;
    private $usuarioModel;

    public function __construct($pdo) {
        exigirSesionEnControlador();
        if (esAdmin()) {
            header('Location: ../vistas/admin/dashboard.php');
            exit();
        }
        $this->pdo = $pdo;
        $this->usuarioModel = new Usuario($pdo);
    }

    private function redirigirPerfil($query = '') {
        $url = '../vistas/fronted/perfil.php';
        if ($query !== '') {
            $url .= '?' . $query;
        }
        header('Location: ' . $url);
        exit();
    }

    private function validarPasswordActualDoble($usuarioId, $pass1, $pass2) {
        if ($pass1 === '' || $pass2 === '') {
            return 'profile_password_required';
        }
        if ($pass1 !== $pass2) {
            return 'profile_password_mismatch';
        }
        $hash = $this->usuarioModel->obtenerPasswordHash($usuarioId);
        if (!$hash || !password_verify($pass1, $hash)) {
            return 'profile_password_wrong';
        }
        return null;
    }

    public function actualizarNombre() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigirPerfil();
        }

        $usuarioId = (int)$_SESSION['usuario_id'];
        $nombre = trim($_POST['nombre'] ?? '');
        $pass1 = $_POST['password_actual'] ?? '';
        $pass2 = $_POST['password_actual_confirm'] ?? '';

        if ($nombre === '') {
            $this->redirigirPerfil('error=name_empty');
        }

        $errorPass = $this->validarPasswordActualDoble($usuarioId, $pass1, $pass2);
        if ($errorPass) {
            $this->redirigirPerfil('error=' . $errorPass);
        }

        if ($this->usuarioModel->actualizarNombre($usuarioId, $nombre)) {
            $_SESSION['usuario_nombre'] = $nombre;
            $this->redirigirPerfil('status=name_updated');
        }

        $this->redirigirPerfil('error=save_failed');
    }

    public function actualizarPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigirPerfil();
        }

        $usuarioId = (int)$_SESSION['usuario_id'];
        $pass1 = $_POST['password_actual'] ?? '';
        $pass2 = $_POST['password_actual_confirm'] ?? '';
        $nueva = $_POST['password_nueva'] ?? '';
        $nuevaConfirm = $_POST['password_nueva_confirm'] ?? '';

        $errorPass = $this->validarPasswordActualDoble($usuarioId, $pass1, $pass2);
        if ($errorPass) {
            $this->redirigirPerfil('error=' . $errorPass);
        }

        if ($nueva === '' || $nuevaConfirm === '') {
            $this->redirigirPerfil('error=new_password_empty');
        }
        if ($nueva !== $nuevaConfirm) {
            $this->redirigirPerfil('error=new_password_mismatch');
        }
        if (strlen($nueva) < 6) {
            $this->redirigirPerfil('error=new_password_short');
        }
        if ($nueva === $pass1) {
            $this->redirigirPerfil('error=new_password_same');
        }

        if ($this->usuarioModel->actualizarPassword($usuarioId, $nueva)) {
            $this->redirigirPerfil('status=password_updated');
        }

        $this->redirigirPerfil('error=save_failed');
    }

    public function eliminarCuenta() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigirPerfil();
        }

        $usuarioId = (int)$_SESSION['usuario_id'];
        $pass1 = $_POST['password_actual'] ?? '';
        $pass2 = $_POST['password_actual_confirm'] ?? '';

        $errorPass = $this->validarPasswordActualDoble($usuarioId, $pass1, $pass2);
        if ($errorPass) {
            $this->redirigirPerfil('error=' . $errorPass);
        }

        if (!$this->usuarioModel->eliminarCuenta($usuarioId)) {
            $this->redirigirPerfil('error=delete_failed');
        }

        session_destroy();
        header('Location: ../vistas/fronted/login.php?status=account_deleted');
        exit();
    }
}

if (isset($_GET['action'])) {
    $controller = new PerfilController($pdo);
    $action = $_GET['action'];
    if ($action === 'actualizar_nombre') {
        $controller->actualizarNombre();
    }
    if ($action === 'actualizar_password') {
        $controller->actualizarPassword();
    }
    if ($action === 'eliminar_cuenta') {
        $controller->eliminarCuenta();
    }
}
