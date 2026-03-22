<?php
// Iniciamos la sesión para poder identificarla y destruirla
session_start();

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

// Redirigimos al login o a la página de inicio
header("Location: ../../index.php");
exit();
?>
