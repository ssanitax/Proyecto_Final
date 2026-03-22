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
        <h2>Juegos fuera de casa 🤝</h2>
        <p style="color: #666;">Préstamos activos actualmente</p>
    </header>

    <?php if (empty($prestamosActivos)): ?>
        <div style="text-align: center; padding: 100px 0;">
            <span style="font-size: 4rem; display: block; margin-bottom: 20px;">🏠</span>
            <p style="color:#999; margin-bottom: 20px;">¡Todos tus juegos están en la biblioteca!</p>
            <a href="mi_coleccion.php" class="btn-dash" style="display: inline-block; text-decoration: none; background: var(--graphite); color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600;">
                Prestar un juego ahora
            </a>
        </div>
    <?php else: ?>
        <div class="shelf-grid">
            <?php foreach ($prestamosActivos as $p): ?>
                <div class="game-card">
                    <div class="game-cover">
                        <span style="font-size: 4rem;">🤝</span>
                    </div>
                    <div class="game-content">
                        <h3><?php echo htmlspecialchars($p->titulo); ?></h3>
                        <div class="borrower-info">
                            <strong>Prestado a:</strong> <?php echo htmlspecialchars($p->nombre_persona); ?><br>
                            <small>Desde el <?php echo date('d/m/Y', strtotime($p->fecha_prestamo)); ?></small>
                        </div>
                        <a href="../../controllers/PrestamoController.php?action=devolver&id=<?php echo $p->id; ?>" 
                           class="btn-return"
                           onclick="return confirm('¿Confirmas que has recuperado el juego?')">
                            Marcar como recibido
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="history-section">
        <a href="historial_prestamos.php" class="btn-history">
            📂 Ver historial de préstamos pasados
        </a>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>