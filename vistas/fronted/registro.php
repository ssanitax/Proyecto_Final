<?php 
require_once '../../includes/auth.php';
require_once '../../config/config.php';
require_once '../../controllers/AuthController.php';

$auth = new AuthController($pdo);
$auth->registrar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['frontend_register_title']; ?></title>
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

        .register-card {
            background: white;
            padding: 50px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }

        .register-card h1 {
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--graphite);
            margin-bottom: 10px;
        }

        .register-card p {
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
            font-size: 0.75rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: var(--dark-gray);
            letter-spacing: 0.5px;
        }

        input {
            width: 100%;
            padding: 14px 15px;
            border-radius: 12px;
            border: 1px solid #eee;
            background: #fafafa;
            font-family: inherit;
            transition: 0.3s;
            font-size: 0.9rem;
        }

        input:focus {
            outline: none;
            border-color: var(--graphite);
            background: white;
            box-shadow: 0 0 0 4px rgba(28, 31, 38, 0.05);
        }

        .btn-register {
            width: 100%;
            padding: 16px;
            border-radius: 50px;
            border: none;
            background: var(--graphite);
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-register:hover {
            background: var(--dark-gray);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .error-msg {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.85rem;
            margin-bottom: 25px;
            border: 1px solid #fecaca;
        }

        .footer-links {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #777;
        }

        .footer-links a {
            color: var(--graphite);
            text-decoration: none;
            font-weight: 700;
        }

        .footer-links a:hover {
            text-decoration: underline;
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

<div class="register-card">
    <div class="lang-selector">
        <a href="?lang=es" class="lang-btn <?php echo ($idiomaActual == 'es') ? 'active' : ''; ?>">ES</a>
        <a href="?lang=en" class="lang-btn <?php echo ($idiomaActual == 'en') ? 'active' : ''; ?>">EN</a>
    </div>
    <h1><?php echo $lang['frontend_register_heading']; ?></h1>
    <p><?php echo $lang['frontend_register_subtitle']; ?></p>

    <?php if(isset($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label><?php echo $lang['frontend_register_label_name']; ?></label>
            <input type="text" name="nombre" placeholder="<?php echo $lang['frontend_register_placeholder_name']; ?>" 
                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label><?php echo $lang['frontend_register_label_email']; ?></label>
            <input type="email" name="email" placeholder="<?php echo $lang['frontend_register_placeholder_email']; ?>" 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label><?php echo $lang['frontend_register_label_password']; ?></label>
            <input type="password" name="password" id="pass1" placeholder="<?php echo $lang['frontend_register_placeholder_password']; ?>" required>
        </div>

        <div class="form-group">
            <label><?php echo $lang['frontend_register_label_confirm_password']; ?></label>
            <input type="password" name="password_confirm" id="pass2" placeholder="<?php echo $lang['frontend_register_placeholder_password']; ?>" oninput="validarPasswords(this)" required>
        </div>

        <button type="submit" class="btn-register"><?php echo $lang['frontend_register_button']; ?></button>
    </form>

    <div class="footer-links">
        <?php echo $lang['frontend_register_login_text']; ?> <a href="login.php"><?php echo $lang['frontend_register_login_link']; ?></a>
    </div>
</div>

<script>
    function validarPasswords(input) {
        const pass1 = document.getElementById('pass1').value;
        if (input.value !== pass1) {
            input.setCustomValidity('Las contraseñas no coinciden.');
        } else {
            input.setCustomValidity(''); // Esto limpia el error y permite enviar
        }
    }
</script>

</body>
</html>