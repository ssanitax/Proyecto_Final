<?php
// =============================================================================
// Configuración y conexión a la base de datos
// Este archivo se incluye en todos los controladores con require_once
// =============================================================================

// Credenciales de acceso a MySQL (definidas como constantes para que sean globales)
define('DB_HOST', 'localhost');       // Servidor de base de datos (local)
define('DB_NAME', 'proyectofinal');   // Nombre de la base de datos
define('DB_USER', 'ana_sanchez');     // Usuario de MySQL
define('DB_PASS', '3dleSLF$gl1FM'); // Contraseña del usuario

try {
    // Conectamos con PDO (permite consultas preparadas → evita inyección SQL)
    // charset=utf8 asegura que las tildes y ñ se guardan y leen correctamente
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);

    // Lanza excepciones si hay errores SQL (en vez de fallar silenciosamente)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Los resultados de fetch() serán objetos: $fila->nombre en vez de $fila['nombre']
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $e) {
    // Si la conexión falla, detenemos la ejecución y mostramos el error
    die("Error crítico de conexión: " . $e->getMessage());
}
?>
