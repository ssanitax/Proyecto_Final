<?php 
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
    <title>Registro | Bengala</title>
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
    </style>
</head>
<body>

<div class="register-card">
    <h1>ÚNETE A BENGALA</h1>
    <p>Crea tu cuenta para gestionar tu colección física</p>

    <?php if(isset($error)): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label>NOMBRE COMPLETO</label>
            <input type="text" name="nombre" placeholder="Ej. Ana Sánchez" 
                   value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>CORREO ELECTRÓNICO</label>
            <input type="email" name="email" placeholder="tu@email.com" 
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
        </div>

        <div class="form-group">
            <label>CONTRASEÑA</label>
            <input type="password" name="password" id="pass1" placeholder="••••••••" required>
        </div>

        <div class="form-group">
            <label>CONFIRMAR CONTRASEÑA</label>
            <input type="password" name="password_confirm" id="pass2" placeholder="••••••••" oninput="validarPasswords(this)" required>
        </div>

        <button type="submit" class="btn-register">Crear mi biblioteca</button>
    </form>

    <div class="footer-links">
        ¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a>
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