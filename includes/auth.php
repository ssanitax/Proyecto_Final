<?php
session_start();

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function esAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

function redirigirSiNoLogueado() {
    if (!estaLogueado()) {
        header('Location: login.php');
        exit();
    }
}
