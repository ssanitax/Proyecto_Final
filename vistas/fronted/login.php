<?php
require_once '../../includes/auth.php';
require_once '../../config/config.php'; // Aquí se define $pdo [cite: 1091]
require_once '../../controllers/AuthController.php';

// Aseguramos que la variable sea accesible
global $pdo;

if (estaLogueado()) {
    if (esAdmin()) {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: dashboard.php');
    }
    exit();
}

// Verificamos manualmente antes de fallar
if (!isset($pdo)) {
    die("Error técnico: La conexión a la base de datos no está disponible. Revisa config.php");
}

$auth = new AuthController($pdo); 
$auth->login();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bengala | Iniciar Sesión</title>
    <link rel="stylesheet" href="../../css/style.css"> <style>
        /* Estilo rápido para mantener la estética de Bengala */
        body { font-family: 'Inter', sans-serif; background: #f4f5f7; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; }
        h2 { margin-bottom: 20px; font-weight: 800; color: #1C1F26; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #eee; border-radius: 10px; outline: none; }
        .btn-login { width: 100%; padding: 12px; border: none; border-radius: 10px; background: #1C1F26; color: white; font-weight: 700; cursor: pointer; margin-top: 10px; }
        .error { color: #e74c3c; font-size: 0.8rem; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>BENGALA</h2>
    
    <?php if (isset($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <button type="submit" class="btn-login">ENTRAR</button>
    </form>

    <p style="margin-top: 20px; font-size: 0.85rem; color: #666;">
        ¿No tienes cuenta? <a href="registro.php" style="color: #1C1F26; font-weight: 700;">Regístrate</a>
    </p>
</div>

</body>
</html>