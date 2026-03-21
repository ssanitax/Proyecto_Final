<?php
require_once 'includes/auth.php';

// 1. Verificamos si el usuario ya tiene una sesión activa
if (estaLogueado()) {
    
    // 2. Si es administrador, lo mandamos a la carpeta de admin
    if (esAdmin()) {
        header('Location: vistas/admin/dashboard.php');
        exit();
    } 
    // 3. Si es un usuario normal, lo mandamos a su colección
    else {
        header('Location: vistas/fronted/mi_coleccion.php');
        exit();
    }

} else {
    // 4. Si no está logueado, lo mandamos al login para que se identifique
    header('Location: vistas/fronted/login.php');
    exit();
}