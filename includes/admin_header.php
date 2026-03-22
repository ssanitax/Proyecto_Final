<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bengala Admin | Gestión</title>
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
            color: var(--graphite);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 10%;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo-admin { 
            font-weight: 800; 
            font-size: 1.2rem; 
            text-decoration: none; 
            color: var(--graphite);
            letter-spacing: -0.5px;
        }

        .nav-links {
            display: flex; 
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            text-decoration: none;
            color: #666;
            font-weight: 600;
            font-size: 0.85rem;
            transition: 0.3s;
        }

        .nav-links a:hover {
            color: var(--graphite);
        }

        .btn-logout {
            color: #e74c3c !important;
            border: 1px solid #fee2e2;
            padding: 8px 15px;
            border-radius: 10px;
        }

        .container { 
            padding: 40px 10%; 
            max-width: 1200px;
            margin: 0 auto;
        }

        .fade-up { 
            animation: fadeInUp 0.6s ease forwards; 
            opacity: 0; 
        }
        
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<nav>
    <a href="dashboard.php" class="logo-admin">BENGALA <span style="font-weight:400; font-size:0.7rem; color:#999;">ADMIN</span></a>
    
    <div class="nav-links">
        <a href="dashboard.php">Inicio</a>
        <a href="validar_juegos.php">Validaciones</a>
        <a href="registrar_directo.php">Alta de Contenido</a>
        <a href="gestionar_usuarios.php">Usuarios</a>
        <a href="inventario_maestro.php">Inventario Maestro</a>
        <a href="../fronted/logout.php" class="btn-logout">Cerrar Sesión</a>
    </div>
</nav>
<div class="container">