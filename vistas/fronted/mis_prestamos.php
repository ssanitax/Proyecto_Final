<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/Prestamo.php';

$prestamoModel = new Prestamo($pdo);
// Obtenemos todos los préstamos del usuario [cite: 2589]
$todosLosPrestamos = $prestamoModel->obtenerPrestamosUsuario($_SESSION['usuario_id']);

// Filtramos solo los que NO han sido devueltos para la vista principal [cite: 2589]
$prestamosActivos = array_filter($todosLosPrestamos, function($p) {
    return !$p->devuelto;
});

include '../../includes/header.php';
?>

<style>
    .shelf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 30px;
        padding: 20px 0;
    }
    .game-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        border: 1px solid #eee;
        border-left: 5px solid #f1c40f; /* Color de advertencia: está fuera */
    }
    .game-cover {
        width: 100%;
        aspect-ratio: 3 / 4;
        background: #fff8e1;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .game-cover img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        object-position: center;
        background: #e8eaed;
        display: block;
    }
    .plat-badge-cover {
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--graphite);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .game-content { padding: 20px; flex-grow: 1; }
    .game-content h3 { font-size: 1.1rem; margin-bottom: 5px; font-weight: 800; color: var(--graphite); }
    .borrower-info { background: #f8f9fa; padding: 10px; border-radius: 8px; margin: 10px 0; font-size: 0.85rem; }
    
    .btn-return {
        display: block;
        width: 100%; padding: 10px; background: #2ecc71; color: white;
        text-align: center; text-decoration: none; border-radius: 50px; font-weight: 700;
        font-size: 0.8rem; margin-top: 10px;
        text-transform: uppercase;
    }

    .history-section {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #eee;
        text-align: center;
    }
    .btn-history {
        display: inline-block;
        padding: 12px 25px;
        background: #f8f9fa;
        color: #666;
        text-decoration: none;
        border-radius: 8px;
        font-weight: 600;
        border: 1px solid #ddd;
        transition: 0.3s;
    }
    .btn-history:hover { background: #eee; color: var(--graphite); }
</style>

<div class="fade-up visible">
    <header style="margin-bottom: 40px; text-align: center;">
        <h2><?php echo $lang['frontend_loans_title']; ?></h2>
        <p style="color: #666;"><?php echo $lang['frontend_loans_desc']; ?></p>
    </header>

    <?php if (empty($prestamosActivos)): ?>
        <div style="text-align: center; padding: 100px 0;">
            <span style="font-size: 4rem; display: block; margin-bottom: 20px;">🏠</span>
            <p style="color:#999; margin-bottom: 20px;"><?php echo $lang['frontend_loans_empty']; ?></p>
            <a href="mi_coleccion.php" class="btn-dash" style="display: inline-block; text-decoration: none; background: var(--graphite); color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600;">
                <?php echo $lang['frontend_loans_empty_button']; ?>
            </a>
        </div>
    <?php else: ?>
        <div class="shelf-grid">
            <?php foreach ($prestamosActivos as $p): ?>
                <div class="game-card">
                    <div class="game-cover">
                        <?php if (!empty($p->imagen_portada)): ?>
                            <img src="../../img/portadas/<?php echo htmlspecialchars($p->imagen_portada); ?>"
                                 alt="<?php echo htmlspecialchars($p->titulo); ?>">
                        <?php else: ?>
                            <span style="font-size: 4rem;">🎮</span>
                        <?php endif; ?>
                        <?php if (!empty($p->plataforma_nombre)): ?>
                            <span class="plat-badge-cover"><?php echo htmlspecialchars($p->plataforma_nombre); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="game-content">
                        <h3><?php echo htmlspecialchars($p->titulo); ?></h3>
                        <?php if (!empty($p->edicion_nombre)): ?>
                            <p style="font-size: 0.8rem; color: #888; margin: 0 0 8px 0;">
                                <?php echo htmlspecialchars($p->edicion_nombre); ?>
                            </p>
                        <?php endif; ?>
                        <div class="borrower-info">
                            <strong><?php echo $lang['frontend_loans_borrowed_to']; ?></strong> <?php echo htmlspecialchars($p->nombre_persona); ?><br>
                            <small><?php echo $lang['frontend_loans_since']; ?> <?php echo date('d/m/Y', strtotime($p->fecha_prestamo)); ?></small>
                        </div>
                        <a href="../../controllers/PrestamoController.php?action=devolver&id=<?php echo $p->id; ?>" 
                           class="btn-return"
                           onclick="return confirm('<?php echo $lang['frontend_loans_confirm_return']; ?>')">
                            <?php echo $lang['frontend_loans_return_button']; ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="history-section">
        <a href="historial_prestamos.php" class="btn-history">
            <?php echo $lang['frontend_loans_history_link']; ?>
        </a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>