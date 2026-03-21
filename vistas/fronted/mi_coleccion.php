<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/Coleccion.php';

$coleccionModel = new Coleccion($pdo);
$miEstanteria = $coleccionModel->obtenerColeccionUsuario($_SESSION['usuario_id']);

include '../../includes/header.php';
?>

<style>
    /* Mismo diseño de cuadrícula que en Buscar */
    .shelf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 30px;
        padding: 20px 0;
    }

    .game-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        flex-direction: column;
        border: 1px solid #eee;
    }

    .game-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        border-color: var(--graphite);
    }

    .game-cover {
        width: 100%;
        aspect-ratio: 3 / 4;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    /* Badge para la plataforma (arriba derecha) */
    .platform-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(28, 31, 38, 0.85);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        backdrop-filter: blur(4px);
    }

    .game-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .game-content h3 {
        font-size: 1.1rem;
        margin-bottom: 8px;
        font-weight: 800;
        color: var(--graphite);
        line-height: 1.3;
    }

    /* Estilos para los estados (Jugando, Pendiente, etc) */
    .status-indicator {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 15px;
        display: inline-block;
    }

    .status-jugando { color: #2ecc71; }
    .status-pendiente { color: #f1c40f; }
    .status-completado { color: #3498db; }

    .btn-details {
        display: block;
        width: 100%;
        padding: 10px;
        border: 1px solid var(--graphite);
        color: var(--graphite);
        text-align: center;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: 0.3s;
    }

    .btn-details:hover {
        background: var(--graphite);
        color: white;
    }
</style>

<div class="fade-up visible">
    <header style="margin-bottom: 40px; text-align: center;">
        <h2>Mi Bibioteca</h2>
        <p style="color: #666;">Gestiona tu colección personal y tus progresos.</p>
    </header>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; text-align: center; margin-bottom: 30px; border: 1px solid #c3e6cb;">
            ¡Juego añadido correctamente a tu colección!
        </div>
    <?php endif; ?>

    <div class="shelf-grid">
        <?php if (!empty($miEstanteria)): ?>
            <?php foreach ($miEstanteria as $item): ?>
                <div class="game-card">
                    <div class="game-cover">
                        <div class="platform-badge"><?php echo htmlspecialchars($item->plataforma); ?></div>
                        <span style="font-size: 4rem;">💿</span>
                    </div>
                    
                    <div class="game-content">
                        <div>
                            <span class="status-indicator status-<?php echo $item->estado; ?>">
                                ● <?php echo ucfirst($item->estado); ?>
                            </span>
                            <h3><?php echo htmlspecialchars($item->titulo); ?></h3>
                            <p style="font-size: 0.85rem; color: #777; margin-bottom: 15px;">
                                Edición: <?php echo htmlspecialchars($item->edicion_nombre); ?> (<?php echo $item->region; ?>)
                            </p>
                        </div>
                        
                        <a href="editar_item.php?id=<?php echo $item->id; ?>" class="btn-details">
                            Gestionar
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 100px 0;">
                <p style="color:#999; margin-bottom: 20px;">Tu biblioteca está vacía.</p>
                <a href="buscar.php" class="btn-dash" style="display: inline-block; text-decoration: none; background: var(--graphite); color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600;">
                    Empezar a añadir juegos
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
