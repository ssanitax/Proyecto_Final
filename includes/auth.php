<?php
session_start();

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function esAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

// Para proteger las vistas de usuario (Mi Biblioteca, Buscar, etc.)
function redirigirSiNoUsuario() {
    if (!estaLogueado()) {
        header('Location: login.php');
        exit();
    }
    if (esAdmin()) {
        header('Location: ../admin/dashboard.php');
        exit();
    }
}

// Para proteger las vistas de administración
function redirigirSiNoAdmin() {
    if (!estaLogueado() || !esAdmin()) {
        header('Location: ../fronted/login.php');
        exit();
    }
}