<?php
require_once '../../includes/auth.php';

// Seguridad: Si no es admin, fuera de aquí
redirigirSiNoAdmin();

require_once '../../config/config.php';

// Consultas para estadísticas [cite: 12, 27]
$stmtJuegos = $pdo->query("SELECT COUNT(*) FROM juegos_pendientes WHERE estado = 'pendiente'");
$totalPendientes = $stmtJuegos->fetchColumn();

$stmtUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'usuario'");
$totalUsuarios = $stmtUsuarios->fetchColumn();

include '../../includes/admin_header.php'; 
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;">Panel de Administración 🛡️</h2>
        <p style="color: #666; font-size: 1.1rem;">Gestión interna de la plataforma Bengala</p>
    </header>

    <div class="dashboard-grid">
        <a href="validar_juegos.php" class="dash-card <?php echo $totalPendientes > 0 ? 'card-highlight' : ''; ?>">
            <div class="dash-icon">📑</div>
            <div class="dash-content">
                <h3>Validar Juegos</h3>
                <p>Hay <strong><?php echo $totalPendientes; ?></strong> juegos pendientes de aprobación.</p>
                <span class="btn-dash">Revisar</span>
            </div>
        </a>

        <a href="gestionar_usuarios.php" class="dash-card">
            <div class="dash-icon">👥</div>
            <div class="dash-content">
                <h3>Usuarios</h3>
                <p>Gestionar los <strong><?php echo $totalUsuarios; ?></strong> coleccionistas activos.</p>
                <span class="btn-dash">Gestionar</span>
            </div>
        </a>

        <a href="gestionar_plataformas.php" class="dash-card">
            <div class="dash-icon">🎮</div>
            <div class="dash-content">
                <h3>Plataformas</h3>
                <p>Configurar el catálogo de consolas y plataformas.</p>
                <span class="btn-dash">Configurar</span>
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

<?php include '../../includes/admin_footer.php'; ?>