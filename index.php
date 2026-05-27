<?php
require_once 'includes/auth.php';
require_once 'config/config.php';

// 1. Si el usuario ya entró antes, lo mandamos directo a su estantería [cite: 6-8]
if (estaLogueado()) {
    if (esAdmin()) {
        header('Location: ' . bengalaUrlConTab('vistas/admin/dashboard.php'));
    } else {
        header('Location: ' . bengalaUrlConTab('vistas/fronted/mi_coleccion.php'));
    }
    exit();
}

// 2. Traemos los números reales de la base de datos para animar al usuario [cite: 212-213]
try {
    $totalJuegos = $pdo->query("SELECT COUNT(*) FROM juegos")->fetchColumn();
    $totalSistemas = $pdo->query("SELECT COUNT(*) FROM plataformas")->fetchColumn();
} catch (Exception $e) {
    $totalJuegos = "20+";
    $totalSistemas = "5+";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang['homepage_title']; ?></title>
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

        /* --- SECCIÓN PRINCIPAL --- */
        .hero {
            padding: 100px 10% 60px 10%;
            background: white;
            text-align: left;
        }

        .hero h1 { 
            font-size: 4rem; 
            font-weight: 800; 
            letter-spacing: -2px; 
            margin-bottom: 20px;
        }

        .hero p { 
            font-size: 1.2rem; 
            color: #555; 
            max-width: 600px; 
            margin-bottom: 40px;
            line-height: 1.6;
        }

        /* --- BOTONES --- */
        .cta-group { display: flex; gap: 15px; }

        .btn {
            padding: 18px 35px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            transition: 0.2s;
        }

        .btn-dark { background: var(--graphite); color: white; }
        .btn-dark:hover { background: #333; transform: translateY(-2px); }

        .btn-outline { border: 2px solid var(--graphite); color: var(--graphite); }
        .btn-outline:hover { background: var(--graphite); color: white; }

        /* --- BARRA DE DATOS --- */
        .data-strip {
            display: flex;
            padding: 50px 10%;
            gap: 80px;
            background: #fafafa;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .data-point h3 { font-size: 2.5rem; font-weight: 800; }
        .data-point p { font-size: 0.8rem; color: #888; font-weight: 600; text-transform: uppercase; }

        /* --- CARACTERÍSTICAS --- */
        .features {
            padding: 80px 10%;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.02);
        }

        .card h3 { margin-bottom: 15px; font-weight: 800; }
        .card p { color: #666; font-size: 0.95rem; line-height: 1.5; }

        footer {
            padding: 40px 10%;
            text-align: center;
            color: #999;
            font-size: 0.8rem;
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

    <div class="lang-selector">
        <a href="?lang=es" class="lang-btn <?php echo ($idiomaActual == 'es') ? 'active' : ''; ?>">ES</a>
        <a href="?lang=en" class="lang-btn <?php echo ($idiomaActual == 'en') ? 'active' : ''; ?>">EN</a>
    </div>

    <section class="hero">
        <h1><?php echo $lang['homepage_heading']; ?></h1>
        <p><?php echo $lang['homepage_description']; ?></p>
        
        <div class="cta-group">
            <a href="<?php echo htmlspecialchars(bengalaUrlConTab('vistas/fronted/registro.php'), ENT_QUOTES); ?>" class="btn btn-dark"><?php echo $lang['homepage_btn_start']; ?></a>
            <a href="<?php echo htmlspecialchars(bengalaUrlConTab('vistas/fronted/login.php'), ENT_QUOTES); ?>" class="btn btn-outline"><?php echo $lang['homepage_btn_login']; ?></a>
        </div>
    </section>

    <div class="data-strip">
        <div class="data-point">
            <h3><?php echo $totalJuegos; ?></h3>
            <p><?php echo $lang['homepage_data_games']; ?></p>
        </div>
        <div class="data-point">
            <h3><?php echo $totalSistemas; ?></h3>
            <p><?php echo $lang['homepage_data_systems']; ?></p>
        </div>
        <div class="data-point">
            <h3><?php echo $lang['homepage_data_free']; ?></h3>
            <p><?php echo $lang['homepage_data_collectors']; ?></p>
        </div>
    </div>

    <section class="features">
        <div class="card">
            <h3><?php echo $lang['homepage_feature_library_title']; ?></h3>
            <p><?php echo $lang['homepage_feature_library_desc']; ?></p>
        </div>
        <div class="card">
            <h3><?php echo $lang['homepage_feature_loans_title']; ?></h3>
            <p><?php echo $lang['homepage_feature_loans_desc']; ?></p>
        </div>
        <div class="card">
            <h3><?php echo $lang['homepage_feature_rating_title']; ?></h3>
            <p><?php echo $lang['homepage_feature_rating_desc']; ?></p>
        </div>
        <div class="card">
            <h3><?php echo $lang['homepage_feature_data_title']; ?></h3>
            <p><?php echo $lang['homepage_feature_data_desc']; ?></p>
        </div>
    </section>

    <footer>
        <?php echo $lang['homepage_footer']; ?>
    </footer>

</body>
</html>