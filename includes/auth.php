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

function rolTieneAccesoAdmin($rol) {
    return in_array($rol, ['admin', 'super_admin'], true);
}

function esAdmin() {
    return isset($_SESSION['usuario_rol']) && rolTieneAccesoAdmin($_SESSION['usuario_rol']);
}

function esSuperAdmin() {
    return isset($_SESSION['usuario_rol']) && $_SESSION['usuario_rol'] === 'super_admin';
}

/**
 * Actualiza $_SESSION['usuario_rol'] desde la BD (útil tras migrar a super_admin sin cerrar sesión).
 */
function sincronizarRolSesion($pdo) {
    if (!estaLogueado() || !$pdo) {
        return;
    }
    try {
        $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE id = ? AND activo = TRUE");
        $stmt->execute([(int)$_SESSION['usuario_id']]);
        $row = $stmt->fetch();
        if ($row && !empty($row->rol)) {
            $_SESSION['usuario_rol'] = $row->rol;
        }
    } catch (PDOException $e) {
        // Sin conexión o columna rol antigua: se mantiene el rol de sesión
    }
}

/**
 * ¿Puede el usuario en sesión eliminar al objetivo?
 */
function puedeEliminarUsuario($rolObjetivo, $idObjetivo) {
    $miRol = $_SESSION['usuario_rol'] ?? '';
    $miId = (int)($_SESSION['usuario_id'] ?? 0);
    $idObjetivo = (int)$idObjetivo;

    if ($miId <= 0 || $idObjetivo <= 0 || $miId === $idObjetivo) {
        return false;
    }
    if ($rolObjetivo === 'super_admin') {
        return false;
    }
    if ($miRol === 'super_admin') {
        return in_array($rolObjetivo, ['usuario', 'admin'], true);
    }
    if ($miRol === 'admin') {
        return $rolObjetivo === 'usuario';
    }
    return false;
}

/**
 * Obliga a tener sesión activa al llamar un controlador (ruta desde /controllers/).
 */
function exigirSesionEnControlador() {
    if (!estaLogueado()) {
        header('Location: ../vistas/fronted/login.php');
        exit();
    }
}

/**
 * Añade una región al catálogo maestro si no existe (tabla regiones).
 */
function asegurarRegionEnCatalogo($pdo, $nombre) {
    $nombre = trim($nombre);
    if ($nombre === '') {
        return;
    }
    $stmt = $pdo->prepare("SELECT id FROM regiones WHERE nombre = ?");
    $stmt->execute([$nombre]);
    if (!$stmt->fetch()) {
        $ins = $pdo->prepare("INSERT INTO regiones (nombre) VALUES (?)");
        $ins->execute([$nombre]);
    }
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

/** Carga BD si hace falta y actualiza el rol en sesión desde la tabla usuarios. */
function prepararSesionAdmin() {
    if (empty($GLOBALS['pdo'])) {
        require_once __DIR__ . '/../config/config.php';
    }
    sincronizarRolSesion($GLOBALS['pdo']);
}

// Proteger vistas de administración
function redirigirSiNoAdmin() {
    if (!estaLogueado()) {
        header('Location: ../fronted/login.php');
        exit();
    }
    prepararSesionAdmin();
    if (!esAdmin()) {
        header('Location: ../fronted/login.php');
        exit();
    }
}