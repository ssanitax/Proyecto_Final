<?php
require_once '../../includes/auth.php';
require_once '../../config/config.php'; // Define la variable $pdo
require_once '../../controllers/AuthController.php';

// 1. Redirección si el usuario ya tiene una sesión activa
if (estaLogueado()) {
    if (esAdmin()) {
        header('Location: ' . bengalaUrlConTab('../admin/dashboard.php'));
    } else {
        header('Location: ' . bengalaUrlConTab('dashboard.php'));
    }
    exit();
}

$error = null;

// 2. Procesar el intento de inicio de sesión cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verificamos que la conexión PDO esté lista
    if (isset($pdo)) {
        $auth = new AuthController($pdo);
        
        /**
         * Importante: El método login() en AuthController debe estar 
         * configurado para retornar un string con el error en caso de fallo.
         */
        $error = $auth->login(); 
    } else {
        $error = "Error crítico: No se pudo establecer conexión con la base de datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['frontend_login_title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --graphite: #1C1F26;
            --bg: #f4f5f7;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--bg); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }

        .login-box { 
            background: white; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            width: 100%; 
            max-width: 400px; 
            text-align: center;
        }

        h2 { 
            margin-bottom: 25px; 
            font-weight: 800; 
            color: var(--graphite); 
        }

        /* Estilo para la alerta de error (Usuario no registrado) */
        .error-alert { 
            background: #fee2e2; 
            color: #991b1b; 
            padding: 12px; 
            border-radius: 10px; 
            font-size: 0.85rem; 
            margin-bottom: 20px;
            border: 1px solid #fecaca;
            font-weight: 600;
            text-align: left;
        }

        input { 
            width: 100%; 
            padding: 14px; 
            margin: 10px 0; 
            border: 1px solid #eee; 
            border-radius: 12px; 
            outline: none; 
            font-family: inherit;
            background: #fafafa;
            transition: 0.3s;
        }

        input:focus {
            border-color: var(--graphite);
            background: white;
        }

        .btn-login { 
            width: 100%; 
            padding: 14px; 
            border: none; 
            border-radius: 50px; 
            background: var(--graphite); 
            color: white; 
            font-weight: 700; 
            cursor: pointer; 
            margin-top: 15px; 
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        .btn-login:hover {
            background: #333;
            transform: translateY(-2px);
        }

        .register-link {
            margin-top: 25px; 
            font-size: 0.85rem; 
            color: #666;
        }

        .register-link a { 
            color: var(--graphite); 
            font-weight: 700; 
            text-decoration: none;
        }

        .lang-selector {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 5px;
        }

        .lang-btn {
            padding: 6px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 700;
            color: #666;
            background: #f5f5f5;
            transition: 0.3s;
        }

        .lang-btn:hover {
            background: var(--graphite);
            color: white;
        }

        .lang-btn.active {
            background: var(--graphite);
            color: white;
        }
    </style>
</head>
<body>
<?php bengalaRenderTabScript(); ?>

<div class="login-box">
    <div class="lang-selector">
        <a href="?lang=es" class="lang-btn <?php echo ($idiomaActual == 'es') ? 'active' : ''; ?>">ES</a>
        <a href="?lang=en" class="lang-btn <?php echo ($idiomaActual == 'en') ? 'active' : ''; ?>">EN</a>
    </div>
    <h2><?php echo $lang['frontend_login_heading']; ?></h2>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'account_deleted'): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 0.85rem; font-weight: 600;">
            <?php echo $lang['frontend_profile_account_deleted']; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="error-alert">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="tab" value="<?php echo htmlspecialchars(bengalaTabActual(), ENT_QUOTES); ?>">
        <input type="email" name="email" placeholder="<?php echo $lang['frontend_login_email_placeholder']; ?>" required>
        <input type="password" name="password" placeholder="<?php echo $lang['frontend_login_password_placeholder']; ?>" required>
        <button type="submit" class="btn-login"><?php echo $lang['frontend_login_button']; ?></button>
    </form>

    <div class="register-link">
        <?php echo $lang['frontend_login_register_text']; ?> <a href="registro.php"><?php echo $lang['frontend_login_register_link']; ?></a>
    </div>
</div>

</body>
</html>