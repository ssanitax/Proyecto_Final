<?php
require_once '../../includes/auth.php';

// Seguridad: Si no es admin, fuera de aquí
redirigirSiNoAdmin();
$pdo = $GLOBALS['pdo'];

// Consultas para estadísticas
$stmtJuegos = $pdo->query("SELECT COUNT(*) FROM juegos_pendientes WHERE estado = 'pendiente'");
$totalPendientes = $stmtJuegos->fetchColumn();

$stmtUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol = 'usuario'");
$totalUsuarios = $stmtUsuarios->fetchColumn();

include '../../includes/admin_header.php'; 
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;"><?php echo $lang['admin_panel_title']; ?></h2>
        <p style="color: #666; font-size: 1.1rem;"><?php echo $lang['admin_panel_desc']; ?></p>
    </header>

    <div class="dashboard-grid">
        <!-- VALIDACIONES -->
        <a href="validar_juegos.php" class="dash-card <?php echo $totalPendientes > 0 ? 'card-highlight' : ''; ?>">
            <div class="dash-icon">📑</div>
            <div class="dash-content">
                <h3><?php echo $lang['validate_proposals']; ?></h3>
                <p><?php echo sprintf($lang['pending_suggestions'], $totalPendientes); ?></p>
                <span class="btn-dash"><?php echo $lang['review']; ?></span>
            </div>
        </a>

        <!-- ALTA DIRECTA -->
        <a href="registrar_directo.php" class="dash-card">
            <div class="dash-icon">➕</div>
            <div class="dash-content">
                <h3><?php echo $lang['content_creation']; ?></h3>
                <p><?php echo $lang['content_creation_desc']; ?></p>
                <span class="btn-dash"><?php echo $lang['register']; ?></span>
            </div>
        </a>

        <!-- USUARIOS -->
        <a href="gestionar_usuarios.php" class="dash-card">
            <div class="dash-icon">👥</div>
            <div class="dash-content">
                <h3><?php echo $lang['users']; ?></h3>
                <p><?php echo sprintf($lang['active_collectors'], $totalUsuarios); ?></p>
                <span class="btn-dash"><?php echo $lang['manage']; ?></span>
            </div>
        </a>

        <!-- INVENTARIO MAESTRO -->
        <a href="inventario_maestro.php" class="dash-card">
            <div class="dash-icon">🗄️</div>
            <div class="dash-content">
                <h3><?php echo $lang['master_inventory']; ?></h3>
                <p><?php echo $lang['master_inventory_desc']; ?></p>
                <span class="btn-dash"><?php echo $lang['view_all']; ?></span>
            </div>
        </a>
    </div>
</div>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        max-width: 1200px;
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
        display: inline-block;
        padding: 10px 25px;
        border-radius: 50px;
        background: var(--graphite);
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        transition: 0.3s;
    }
</style>

<?php include '../../includes/admin_footer.php'; ?>