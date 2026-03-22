<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/Coleccion.php';

$coleccionModel = new Coleccion($pdo);
$miColeccion = $coleccionModel->obtenerColeccionUsuario($_SESSION['usuario_id']);

include '../../includes/header.php';
?>

<style>
    /* Estilos globales para la estantería */
    .fade-up.visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .fade-up {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

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

    .dash-icon {
        font-size: 3.5rem;
        margin-bottom: 20px;
    }

    .dash-content h3 {
        font-size: 1.4rem;
        margin-bottom: 15px;
        font-weight: 800;
        color: var(--graphite);
    }

    .dash-content p {
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 25px;
        color: #666;
    }

    .btn-dash {
        padding: 10px 25px;
        border-radius: 50px;
        background: var(--graphite);
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        display: inline-block;
        text-decoration: none;
    }
</style>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;">Mi Colección Personal</h2>
        <p style="color: #666; font-size: 1.1rem;">Tu estantería virtual de juegos físicos.</p>
    </header>

    <?php if (empty($miColeccion)): ?>
        <div style="text-align: center; padding: 100px 0;">
            <span style="font-size: 4rem; display: block; margin-bottom: 20px;">💿</span>
            <p style="color:#999; margin-bottom: 20px;">Tu biblioteca está vacía.</p>
            <a href="buscar.php" class="btn-dash" style="display: inline-block; text-decoration: none;">
                + Empezar a añadir juegos
            </a>
        </div>
    <?php else: ?>
        <div class="dashboard-grid">
            <?php foreach ($miColeccion as $item): ?>
                <div class="dash-card">
                    <div class="dash-icon">
                        <?php if ($item->estado == 'jugado'): ?> 🎮
                        <?php elseif ($item->estado == 'jugando'): ?> 🕹️
                        <?php elseif ($item->estado == 'completado'): ?> ⭐
                        <?php else: ?> 📀
                        <?php endif; ?>
                    </div>
                    
                    <div class="dash-content">
                        <h3><?php echo htmlspecialchars($item->titulo); ?></h3>
                        <p><?php echo htmlspecialchars($item->plataforma); ?> - <?php echo htmlspecialchars($item->region); ?></p>
                        
                        <a href="juego_detalle.php?id=<?php echo $item->juego_id; ?>" class="btn-details">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>