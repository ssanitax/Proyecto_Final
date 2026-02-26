<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'proyectofinal'); // El nombre que pusiste en tu SQL [cite: 4]
define('DB_USER', 'root');          // Usuario por defecto en local
define('DB_PASS', '');              // Contraseña (vacía en XAMPP por defecto)

try {
    // Creamos la conexión con PDO
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    
    // Configuramos para que lance excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Configuramos para que el fetch por defecto sea objeto (más limpio para tus modelos)
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $e) {
    // Si hay error, detenemos la ejecución
    die("Error crítico de conexión: " . $e->getMessage());
}
?>
