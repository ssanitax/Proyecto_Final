<?php 
require_once '../../config/config.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($pdo);
$auth->registrar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Registro - Bengala</title>
</head>
<body>
    <h1>Crea tu cuenta en Bengala</h1>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    
    <form method="POST" action="">
        <input type="text" name="nombre" placeholder="Nombre completo" required><br>
        <input type="email" name="email" placeholder="Correo electrónico" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <button type="submit">Registrarse</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="login.php">Loguéate aquí</a></p>
</body>
</html>
