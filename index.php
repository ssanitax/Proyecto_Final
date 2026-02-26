<?php
require_once 'includes/auth.php';

// Si el usuario ya está logueado, lo mandamos a su colección
if (estaLogueado()) {
    header('Location: vistas/fronted/mi_coleccion.php');
} else {
    // Si no, al login
    header('Location: vistas/fronted/login.php');
}
exit();
?>
