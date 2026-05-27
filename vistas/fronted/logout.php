<?php
require_once '../../includes/auth.php';

// Limpiamos todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la sesión completamente, también hay que borrar la cookie de sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruimos la sesión
session_destroy();
session_write_close();

// Redirigimos al index conservando el identificador de pestaña
header('Location: ' . bengalaUrlConTab('../../index.php'));
exit();
?>
