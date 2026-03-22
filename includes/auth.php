<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- LÓGICA DE IDIOMA ---

// 1. Detectar si el usuario quiere cambiar el idioma por URL
if (isset($_GET['lang'])) {
    $lang_requested = $_GET['lang'];
    // Validamos que solo acepte 'es' o 'en'
    $_SESSION['lang'] = ($lang_requested === 'en') ? 'en' : 'es';
}

// 2. Establecer idioma actual (por defecto español si no hay sesión)
$idiomaActual = $_SESSION['lang'] ?? 'es';

// 3. Cargar el diccionario (Asumiendo que creas la carpeta /lang/ en la raíz)
// Usamos __DIR__ para que la ruta sea relativa a este archivo auth.php
$path_to_lang = __DIR__ . "/../lang/{$idiomaActual}.php";

if (file_exists($path_to_lang)) {
    require_once $path_to_lang;
} else {
    // Diccionario de seguridad por si falla la carga del archivo
    $lang = []; 
}

/**
 * Función global para traducir claves
 * Uso: echo __('inicio');
 */
function __($clave) {
    global $lang;
    return $lang[$clave] ?? $clave;
}

// --- FUNCIONES DE AUTENTICACIÓN ---

function estaLogueado() {
    return isset($_SESSION['usuario_id']);
}

function esAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'admin';
}

// Proteger vistas de usuario (Mi Biblioteca, Buscar, etc.)
function redirigirSiNoUsuario() {
    if (!estaLogueado()) {
        header('Location: login.php');
        exit();
    }
    // Si un admin intenta entrar a la zona de usuario, lo mandamos a su panel
    if (esAdmin() && strpos($_SERVER['PHP_SELF'], 'admin') === false) {
        header('Location: ../admin/dashboard.php');
        exit();
    }
}

// Proteger vistas de administración
function redirigirSiNoAdmin() {
    if (!estaLogueado() || !esAdmin()) {
        header('Location: ../fronted/login.php');
        exit();
    }
}