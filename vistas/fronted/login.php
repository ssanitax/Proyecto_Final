<?php
require_once '../../config/config.php';
require_once '../../controllers/AuthController.php';

// Iniciamos el controlador
$auth = new AuthController($pdo);

// Si el formulario se envía, el método login() gestionará la sesión
$auth->login();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Bengala</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --graphite: #1C1F26;
            --dark-gray: #333333;
            --silver: #C7CCD6;
            --bg: #f4f5f7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card h1 {
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--graphite);
            margin-bottom: 10px;
        }

        .login-card p {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark-gray);
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: #fafafa;
            font-family: inherit;
            transition: 0.3s;
        }

        input:focus {
            outline: none;
            border-color: var(--graphite);
            background: white;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 50px;
            border: none;
            background: var(--graphite);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: var(--dark-gray);
        }

        .error-msg {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-bottom: 20px;
        }

        .footer-links {
            margin-top: 25px;
            font-size: 0.85rem;
        }

        .footer-links a {
            color: var(--graphite);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h1>BENGALA</h1>
    <p>Gestiona tu colección de videojuegos</p>

    <?php if (isset($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>EMAIL</label>
            <input type="email" name="email" placeholder="tu@email.com" required>
        </div>

        <div class="form-group">
            <label>CONTRASEÑA</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn-login">Entrar</button>
    </form>

    <div class="footer-links">
        ¿No tienes cuenta? <a href="registro.php">Regístrate</a>
    </div>
</div>

</body>
</html>
