<?php
require_once __DIR__ . '/../config/config.php';
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

            // 1. VALIDACIÓN LÓGICA: ¿Las contraseñas coinciden?
            if ($password !== $password_confirm) {
                $error = "Las contraseñas no coinciden. Por favor, inténtalo de nuevo.";
                include __DIR__ . '/../vistas/fronted/registro.php';
                return; // Cortamos la ejecución para que no guarde nada
            }

            // 2. VALIDACIÓN DE SEGURIDAD: Longitud mínima (opcional pero recomendada)
            if (strlen($password) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
                include __DIR__ . '/../vistas/fronted/registro.php';
                return;
            }

            // 3. INTENTO DE REGISTRO
            if ($this->usuarioModel->registrar($nombre, $email, $password)) {
                header('Location: login.php?registro=exito');
                exit();
            } else {
                // Si el modelo devuelve false, suele ser por el UNIQUE del email
                $error = "El email ya está registrado o el nombre no es válido.";
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
                $error = "Credenciales incorrectas o cuenta inactiva.";
                include __DIR__ . '/../vistas/fronted/login.php';
            }
        }
    }

    public function logout() {
        if (!isset($_SESSION)) { session_start(); }
        session_destroy();
        header('Location: login.php');
        exit();
    }
}