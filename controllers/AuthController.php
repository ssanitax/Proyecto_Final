<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private $usuarioModel;

    public function __construct($pdo) {
        $this->usuarioModel = new Usuario($pdo);
    }

    public function registrar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = $_POST['nombre'];
            $email = $_POST['email'];
            $password = $_POST['password'];

            if ($this->usuarioModel->registrar($nombre, $email, $password)) {
                header('Location: login.php?registro=exito');
            } else {
                $error = "El email ya está registrado.";
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
                session_start();
                $_SESSION['usuario_id'] = $usuario->id;
                $_SESSION['usuario_nombre'] = $usuario->nombre;
                $_SESSION['usuario_rol'] = $usuario->rol;

                // Redirigir según el rol que definiste en tu SQL
                if ($usuario->rol === 'admin') {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: dashboard.php');
                }
            } else {
                $error = "Credenciales incorrectas o cuenta inactiva.";
                include __DIR__ . '/../vistas/fronted/login.php';
            }
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: login.php');
    }
}
