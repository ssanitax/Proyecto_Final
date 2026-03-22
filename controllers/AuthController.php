<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new Usuario($pdo);
    }

    /**
     * Procesa el registro de nuevos usuarios con validación de contraseña
     */
    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            if ($password !== $password_confirm) {
                $error = $lang['passwords_not_match'];
                include __DIR__ . '/../vistas/fronted/registro.php';
                return;
            }

            if (strlen($password) < 6) {
                $error = $lang['password_too_short'];
                include __DIR__ . '/../vistas/fronted/registro.php';
                return;
            }

            // El modelo ahora devuelve el ID del nuevo usuario 
            $nuevo_id = $this->usuarioModel->registrar($nombre, $email, $password);

            if ($nuevo_id) {
                // LOGIN AUTOMÁTICO: Iniciamos la sesión aquí mismo 
                if (!isset($_SESSION)) { session_start(); }
                
                $_SESSION['usuario_id'] = $nuevo_id;
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_rol'] = 'usuario';

                // Redirigimos directamente al dashboard de usuario 
                header('Location: dashboard.php');
                exit();
            } else {
                $error = $lang['email_already_registered'];
                include __DIR__ . '/../vistas/fronted/registro.php';
            }
        }
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $usuario = $this->usuarioModel->login($email, $password);

            if ($usuario) {
                if (!isset($_SESSION)) { session_start(); }
                $_SESSION['usuario_id'] = $usuario->id;
                $_SESSION['usuario_nombre'] = $usuario->nombre;
                $_SESSION['usuario_rol'] = $usuario->rol;

                if ($usuario->rol === 'admin') {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
                exit();
            } else {
                // RETORNAMOS el texto para que la vista lo pinte
                return $lang['invalid_credentials'];
            }
        }
        return null;
    }

    public function logout() {
        if (!isset($_SESSION)) { session_start(); }
        session_destroy();
        header('Location: login.php');
        exit();
    }
}