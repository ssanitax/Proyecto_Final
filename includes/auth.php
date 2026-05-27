<?php

function bengalaSanitizarTabId(?string $tab): string {
    $tab = trim((string)$tab);
    if ($tab === '' || strlen($tab) > 64) {
        return '';
    }
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $tab)) {
        return '';
    }
    return $tab;
}

function bengalaResolverTabId(): string {
    $tab = bengalaSanitizarTabId($_GET['tab'] ?? '');
    if ($tab === '') {
        $tab = bengalaSanitizarTabId($_POST['tab'] ?? '');
    }
    if ($tab === '') {
        $tab = bengalaSanitizarTabId($_COOKIE['bengala_tab'] ?? '');
    }
    return $tab !== '' ? $tab : 'default';
}

$GLOBALS['bengala_tab_id'] = bengalaResolverTabId();
$sessionSuffix = strtoupper(substr(sha1($GLOBALS['bengala_tab_id']), 0, 16));
session_name('BENGALA_' . $sessionSuffix);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!headers_sent()) {
    setcookie('bengala_tab', $GLOBALS['bengala_tab_id'], [
        'expires' => time() + (60 * 60 * 24 * 30),
        'path' => '/',
        'secure' => false,
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
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

function bengalaTabActual(): string {
    return $GLOBALS['bengala_tab_id'] ?? 'default';
}

function bengalaUrlConTab(string $url): string {
    $tab = bengalaTabActual();
    if ($tab === '' || strpos($url, 'tab=') !== false || strpos($url, 'javascript:') === 0) {
        return $url;
    }
    $sep = strpos($url, '?') !== false ? '&' : '?';
    return $url . $sep . 'tab=' . urlencode($tab);
}

function bengalaRenderTabScript(): void {
    $tab = bengalaTabActual();
    ?>
<script>
(function () {
    var tabId = <?php echo json_encode($tab); ?>;
    try {
        var stored = sessionStorage.getItem('bengala_tab');
        if (stored && /^[a-zA-Z0-9_-]{3,64}$/.test(stored)) {
            tabId = stored;
        } else if (!tabId || tabId === 'default') {
            tabId = 'tab_' + Math.random().toString(36).slice(2, 12);
            sessionStorage.setItem('bengala_tab', tabId);
        } else {
            sessionStorage.setItem('bengala_tab', tabId);
        }
    } catch (e) {
        if (!tabId || tabId === 'default') {
            tabId = 'tab_' + Math.random().toString(36).slice(2, 12);
        }
    }

    function ensureUrlTab() {
        try {
            var u = new URL(location.href);
            if (u.searchParams.get('tab') !== tabId) {
                u.searchParams.set('tab', tabId);
                history.replaceState(null, '', u.pathname + u.search + u.hash);
            }
        } catch (e) {}
    }

    function applyTabToLinksAndForms(root) {
        var links = (root || document).querySelectorAll('a[href]');
        links.forEach(function (a) {
            var href = a.getAttribute('href');
            if (!href || href[0] === '#' || href.indexOf('javascript:') === 0 || href.indexOf('mailto:') === 0) return;
            try {
                var u = new URL(href, location.href);
                if (u.origin !== location.origin) return;
                if (!u.searchParams.get('tab')) u.searchParams.set('tab', tabId);
                a.setAttribute('href', u.pathname + u.search + u.hash);
            } catch (e) {}
        });

        var forms = (root || document).querySelectorAll('form');
        forms.forEach(function (f) {
            var inp = f.querySelector('input[name="tab"]');
            if (!inp) {
                inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = 'tab';
                f.appendChild(inp);
            }
            inp.value = tabId;

            if ((f.method || 'get').toLowerCase() === 'get') {
                var action = f.getAttribute('action') || location.pathname + location.search;
                try {
                    var u = new URL(action, location.href);
                    if (!u.searchParams.get('tab')) u.searchParams.set('tab', tabId);
                    f.setAttribute('action', u.pathname + u.search);
                } catch (e) {}
            }
        });
    }

    ensureUrlTab();
    applyTabToLinksAndForms(document);
})();
</script>
    <?php
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

/**
 * Ruta relativa segura dentro de vistas/fronted (para volver tras un POST).
 */
function urlRetornoFrontendSegura(?string $url): ?string {
    $url = trim((string)$url);
    if ($url === '' || preg_match('#^https?://#i', $url)) {
        return null;
    }
    if (strpos($url, '..') !== false || strpos($url, '//') === 0) {
        return null;
    }
    $url = ltrim($url, '/');
    if (!preg_match('#^[a-zA-Z0-9_./?=&%-]+$#', $url)) {
        return null;
    }
    return $url;
}

/**
 * Ruta relativa segura dentro de vistas/admin.
 */
function urlRetornoAdminSegura(?string $url): ?string {
    $url = trim((string)$url);
    if ($url === '' || preg_match('#^https?://#i', $url)) {
        return null;
    }
    if (strpos($url, '..') !== false || strpos($url, '//') === 0) {
        return null;
    }
    $url = ltrim($url, '/');
    if (!preg_match('#^[a-zA-Z0-9_./?=&%-]+$#', $url)) {
        return null;
    }
    return $url;
}

/** Redirige a return_to (POST/GET) si es válido, si no a la ruta por defecto bajo admin. */
function redirigirAdmin(string $rutaPorDefecto, string $query = ''): void {
    $destino = urlRetornoAdminSegura($_POST['return_to'] ?? $_GET['return_to'] ?? '');
    $base = '../vistas/admin/';
    if ($destino !== null) {
        $sep = strpos($destino, '?') !== false ? '&' : '?';
        $loc = $base . $destino . ($query !== '' ? $sep . ltrim($query, '?&') : '');
    } else {
        $loc = $base . ltrim($rutaPorDefecto, '/');
        if ($query !== '') {
            $loc .= (strpos($loc, '?') !== false ? '&' : '?') . ltrim($query, '?&');
        }
    }
    header('Location: ' . bengalaUrlConTab($loc));
    exit();
}

/** Redirige a return_to (POST/GET) si es válido, si no a la ruta por defecto bajo fronted. */
function redirigirFrontend(string $rutaPorDefecto, string $query = ''): void {
    $destino = urlRetornoFrontendSegura($_POST['return_to'] ?? $_GET['return_to'] ?? '');
    $base = '../vistas/fronted/';
    if ($destino !== null) {
        $sep = strpos($destino, '?') !== false ? '&' : '?';
        $loc = $base . $destino . ($query !== '' ? $sep . ltrim($query, '?&') : '');
    } else {
        $loc = $base . ltrim($rutaPorDefecto, '/');
        if ($query !== '') {
            $loc .= (strpos($loc, '?') !== false ? '&' : '?') . ltrim($query, '?&');
        }
    }
    header('Location: ' . bengalaUrlConTab($loc));
    exit();
}

// Proteger vistas de usuario (Mi Biblioteca, Buscar, etc.)
function redirigirSiNoUsuario() {
    if (!estaLogueado()) {
        header('Location: ' . bengalaUrlConTab('login.php'));
        exit();
    }
    // Si un admin intenta entrar a la zona de usuario, lo mandamos a su panel
    if (esAdmin() && strpos($_SERVER['PHP_SELF'], 'admin') === false) {
        header('Location: ' . bengalaUrlConTab('../admin/dashboard.php'));
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
        header('Location: ' . bengalaUrlConTab('../fronted/login.php'));
        exit();
    }
    prepararSesionAdmin();
    if (!esAdmin()) {
        header('Location: ' . bengalaUrlConTab('../fronted/login.php'));
        exit();
    }
}