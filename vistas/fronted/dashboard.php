<?php
require_once '../../includes/auth.php';
redirigirSiNoLogueado();
require_once '../../config/config.php';
include '../../includes/header.php'; 
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;">Hola, <?php echo $_SESSION['usuario_nombre']; ?> 👋</h2>
        <p style="color: #666; font-size: 1.1rem;">Tu biblioteca de juegos físicos, organizada.</p>
    </header>

    <div class="dashboard-grid">
        <a href="mi_coleccion.php" class="dash-card">
            <div class="dash-icon">📚</div>
            <div class="dash-content">
                <h3>Mi Biblioteca</h3>
                <p>Gestiona tus juegos, cambia estados y añade tus valoraciones personales.</p>
                <span class="btn-dash">Entrar</span>
            </div>
        </a>

        <a href="buscar.php" class="dash-card card-highlight">
            <div class="dash-icon">🔍</div>
            <div class="dash-content">
                <h3>Añadir Nuevo</h3>
                <p>Explora el catálogo y añade nuevas piezas a tu colección.</p>
                <span class="btn-dash">Buscar Juegos</span>
            </div>
        </a>

        <a href="mis_prestamos.php" class="dash-card">
            <div class="dash-icon">🤝</div>
            <div class="dash-content">
                <h3>Préstamos</h3>
                <p>Lleva el control de qué juegos has prestado y a quién.</p>
                <span class="btn-dash">Ver Préstamos</span>
            </div>
        </a>
    </div>
</div>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1100px;
        margin: 0 auto;
    }

    .dash-card {
        background: white;
        padding: 40px 30px;
        border-radius: 20px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: 0.3s ease;
        border: 1px solid #eee;
    }

    .dash-card:hover {
        transform: translateY(-10px);
        border-color: var(--graphite);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .card-highlight {
        background: var(--graphite);
        color: white;
    }

    .card-highlight .dash-content p { color: #aaa; }
    .card-highlight .btn-dash { background: white; color: var(--graphite); }

    .dash-icon { font-size: 3.5rem; margin-bottom: 20px; }
    .dash-content h3 { font-size: 1.4rem; margin-bottom: 15px; font-weight: 800; }
    .dash-content p { font-size: 0.95rem; line-height: 1.6; margin-bottom: 25px; min-height: 60px; }

    .btn-dash {
        padding: 10px 25px;
        border-radius: 50px;
        background: var(--graphite);
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
</style>

<?php include '../../includes/footer.php'; ?>