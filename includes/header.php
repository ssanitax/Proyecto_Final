<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bengala | Video Game Collection</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --silver: #C7CCD6;
            --graphite: #1C1F26;
            --dark-gray: #333333;
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
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo { font-weight: 800; font-size: 1.5rem; text-decoration: none; color: var(--graphite); }

        nav a {
            text-decoration: none;
            color: var(--dark-gray);
            margin-left: 25px;
            font-weight: 500;
            transition: 0.3s;
        }

        nav a:hover { color: var(--graphite); }

        .container { padding: 40px 10%; }

        h2 { font-size: 2rem; margin-bottom: 30px; text-align: center; }

        /* GRID DE LA ESTANTERÍA (Shelf View) */
        .shelf-grid {
            display: grid;
            /* Configuración para 4 o 5 columnas */
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 25px;
        }

        .game-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.04);
            transition: 0.3s;
        }

        .game-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); }

        .game-image {
            width: 100%;
            aspect-ratio: 3 / 4; /* Proporción típica de carátulas */
            background: #eee;
            overflow: hidden;
        }

        .game-image img { width: 100%; height: 100%; object-fit: cover; }

        .game-info { padding: 15px; }

        .game-info h3 { font-size: 1rem; margin-bottom: 5px; height: 2.4em; overflow: hidden; }

        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
            border-radius: 4px;
            background: var(--silver);
            text-transform: uppercase;
            font-weight: 600;
        }

        .status-jugando { background: #d1fae5; color: #065f46; }
        .status-pendiente { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>
<nav>
    <a href="dashboard.php" class="logo">BENGALA</a>
    <div>
        <a href="mi_coleccion.php">Mi Biblioteca</a>
        <a href="buscar.php">Añadir Juego</a>
        <a href="mis_prestamos.php">Préstamos</a>
        <a href="mis_propuestas.php">Propuestas</a>
        <a href="logout.php" style="color: #991b1b; font-weight: 600;">Salir</a>
    </div>
</nav>
<div class="container">
