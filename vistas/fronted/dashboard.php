<?php
require_once '../../includes/auth.php';
redirigirSiNoLogueado(); // [cite: 273]
require_once '../../config/config.php';
include '../../includes/header.php'; 
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;">Hola, <?php echo $_SESSION['usuario_nombre']; ?> 👋</h2>
        <p style="color: #666; font-size: 1.1rem;">¿Qué quieres gestionar hoy en tu colección física?</p>
    </header>

    <div class="dashboard-grid">
        <a href="mi_coleccion.php" class="dash-card">
            <div class="dash-icon">📚</div>
            <div class="dash-content">
                <h3>Mi Estantería</h3>
                <p>Organiza tu biblioteca personal, cambia estados de juego (jugando, pendiente o completado) y añade tus valoraciones personales.</p>
                <span class="btn-dash">Entrar a la estantería</span>
            </div>
        </a>

        <a href="mis_prestamos.php" class="dash-card">
            <div class="dash-icon">🤝</div>
            <div class="dash-content">
                <h3>Control de Préstamos</h3>
                <p>Lleva un seguimiento detallado de tus juegos prestados a amigos para que nunca pierdas de vista ni un solo título de tu colección.</p>
                <span class="btn-dash">Ver mis préstamos</span>
            </div>
        </a>
    </div>
</div>

<style>
    /* Estilos específicos para el Dashboard de dos columnas */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1fr; /* Dos columnas iguales */
        gap: 40px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .dash-card {
        background: white;
        padding: 40px;
        border-radius: 20px;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .dash-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border-color: var(--graphite);
    }

    .dash-icon {
        font-size: 3rem;
        margin-bottom: 20px;
    }

    .dash-content h3 {
        font-size: 1.5rem;
        margin-bottom: 15px;
        color: var(--graphite);
        font-weight: 800;
    }

    .dash-content p {
        color: #666;
        line-height: 1.6;
        margin-bottom: 25px;
        min-height: 80px;
    }

    .btn-dash {
        display: inline-block;
        padding: 12px 30px;
        border-radius: 50px;
        background: var(--graphite);
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        transition: 0.3s;
    }

    .dash-card:hover .btn-dash {
        background: var(--dark-gray);
    }

    /* Ajuste para móviles */
    @media (max-width: 800px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include '../../includes/footer.php'; ?>
