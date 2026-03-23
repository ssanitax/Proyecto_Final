<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/Prestamo.php';

$prestamoModel = new Prestamo($pdo);
// El modelo ya debe traer titulo, edicion_nombre y plataforma_nombre
$todos = $prestamoModel->obtenerPrestamosUsuario($_SESSION['usuario_id']);

// Filtramos solo los registros que ya fueron devueltos
$historial = array_filter($todos, function($p) {
    return $p->devuelto;
});

include '../../includes/header.php';
?>

<style>
    .plat-badge {
        background: var(--graphite);
        color: white;
        font-size: 0.65rem;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 800;
        text-transform: uppercase;
        margin-right: 10px;
        display: inline-block;
        vertical-align: middle;
    }
    
    .history-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 0.95rem;
    }

    .history-table thead tr {
        background: #fafafa;
        border-bottom: 2px solid #f0f0f0;
    }

    .history-table th {
        padding: 20px;
        font-weight: 800;
        color: #333;
        font-size: 0.75rem;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .history-table td {
        padding: 20px;
        border-bottom: 1px solid #f5f5f5;
        vertical-align: middle;
    }

    .btn-back {
        text-decoration: none;
        color: var(--graphite);
        background: white;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        border: 1px solid #eee;
        transition: 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-back:hover {
        background: var(--graphite);
        color: white;
        transform: translateX(-5px);
    }

    .date-pill {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .date-out { background: #f0f0f0; color: #666; }
    .date-in { background: #eef9f1; color: #27ae60; }
</style>

<div class="fade-up visible">
    <header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div style="text-align: left;">
            <h2 style="margin: 0; text-align: left;"><?php echo $lang['frontend_history_title']; ?></h2>
            <p style="color: #666; margin: 5px 0 0 0;"><?php echo $lang['frontend_history_desc']; ?></p>
        </div>
        <a href="mis_prestamos.php" class="btn-back">
            <?php echo $lang['frontend_history_back']; ?>
        </a>
    </header>

    <div class="about-box" style="padding: 0; overflow: hidden; background: white; border-radius: 20px; border: 1px solid #eee; box-shadow: 0 10px 30px rgba(0,0,0,0.02);">
        <?php if (empty($historial)): ?>
            <div style="padding: 80px; text-align: center;">
                <span style="font-size: 3rem; display: block; margin-bottom: 20px;">📂</span>
                <p style="color: #999; font-style: italic;"><?php echo $lang['frontend_history_empty']; ?></p>
            </div>
        <?php else: ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th><?php echo $lang['frontend_history_table_game']; ?></th>
                        <th><?php echo $lang['frontend_history_table_person']; ?></th>
                        <th><?php echo $lang['frontend_history_table_out']; ?></th>
                        <th><?php echo $lang['frontend_history_table_return']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $h): ?>
                        <tr onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='white'">
                            <td>
                                <div style="display: flex; align-items: center; margin-bottom: 6px;">
                                    <span class="plat-badge"><?php echo htmlspecialchars($h->plataforma_nombre); ?></span>
                                    <strong style="color: var(--graphite); font-size: 1rem;">
                                        <?php echo htmlspecialchars($h->titulo); ?>
                                    </strong>
                                </div>
                                <div style="font-size: 0.8rem; color: #888; margin-left: 0px;">
                                    📦 <?php echo htmlspecialchars($h->edicion_nombre); ?>
                                </div>
                            </td>
                            <td style="font-weight: 600; color: #444;">
                                <?php echo htmlspecialchars($h->nombre_persona); ?>
                            </td>
                            <td>
                                <span class="date-pill date-out">
                                    <?php echo date('d/m/Y', strtotime($h->fecha_prestamo)); ?>
                                </span>
                            </td>
                            <td>
                                <span class="date-pill date-in">
                                    <?php echo date('d/m/Y', strtotime($h->fecha_devolucion)); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>