<?php
require_once '../../includes/auth.php';
redirigirSiNoLogueado();
require_once '../../config/config.php';
require_once '../../models/Coleccion.php';

$coleccionModel = new Coleccion($pdo);
$miEstanteria = $coleccionModel->obtenerColeccionUsuario($_SESSION['usuario_id']);

include '../../includes/header.php';
?>

<h2>Mi Estantería Personal</h2>

<div class="shelf-grid">
    <?php foreach ($miEstanteria as $item): ?>
    <div class="game-card">
        <div class="game-image">
            <img src="../../assets/img/portadas/<?php echo $item->imagen_portada; ?>" alt="<?php echo $item->titulo; ?>">
        </div>
        <div class="game-info">
            <span class="badge"><?php echo $item->plataforma; ?></span>
            <h3><?php echo $item->titulo; ?></h3>
            <p class="badge status-<?php echo $item->estado; ?>">
                <?php echo ucfirst($item->estado); ?>
            </p>
            <div style="margin-top: 10px;">
                <a href="juego_detalle.php?id=<?php echo $item->id; ?>" style="font-size: 0.8rem; color: var(--dark-gray);">Ver detalles</a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($miEstanteria)): ?>
        <p>Tu estantería está vacía. ¡Empieza a añadir juegos!</p>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
