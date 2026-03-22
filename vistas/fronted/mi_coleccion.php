<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/Coleccion.php';

$coleccionModel = new Coleccion($pdo);
// Obtenemos la colección del usuario
$miColeccion = $coleccionModel->obtenerColeccionUsuario($_SESSION['usuario_id']);

include '../../includes/header.php';
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 15px;">Mi Colección Personal</h2>
        <p style="color: #777; font-size: 1.1rem;">Tu estantería virtual de juegos físicos.</p>
    </header>

    <?php if (isset($_GET['status'])): ?>
        <div style="max-width: 800px; margin: 0 auto 30px auto;">
            <?php if ($_GET['status'] == 'success'): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 12px; text-align: center; border: 1px solid #c3e6cb; font-weight: 600;">
                    ✅ ¡Genial! El juego se ha añadido a tu estantería.
                </div>
            <?php elseif ($_GET['status'] == 'exists'): ?>
                <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 12px; text-align: center; border: 1px solid #ffeeba; font-weight: 600;">
                    Aviso: Este juego ya estaba en tu biblioteca. 💿
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($miColeccion)): ?>
        <div style="text-align: center; padding: 100px 0;">
            <span style="font-size: 4rem; display: block; margin-bottom: 20px;">🏠</span>
            <p style="color:#999; margin-bottom: 20px;">Tu biblioteca está vacía.</p>
            <a href="buscar.php" style="display: inline-block; text-decoration: none; background: var(--graphite); color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600; text-transform: uppercase; font-size: 0.8rem;">
                + Empezar a añadir juegos
            </a>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 30px; padding: 20px 0;">
            <?php foreach ($miColeccion as $item): ?>
                <div class="game-card" style="background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; display: flex; flex-direction: column; transition: 0.3s;" 
                     onmouseover="this.style.transform='translateY(-10px)'; this.style.borderColor='var(--graphite)';" 
                     onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#eee';">
                    
                    <div style="width: 100%; aspect-ratio: 3/4; background: #f8f9fa; display: flex; align-items: center; justify-content: center; position: relative;">
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(28, 31, 38, 0.9); color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                            <?php echo htmlspecialchars($item->plataforma); ?>
                        </div>
                        
                        <span style="font-size: 4rem;">
                            <?php 
                            if ($item->estado == 'completado') echo '⭐';
                            elseif ($item->estado == 'jugando') echo '🕹️';
                            else echo '💿';
                            ?>
                        </span>
                    </div>

                    <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div style="text-align: left; margin-bottom: 15px;">
                            <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: <?php echo $item->estado == 'jugando' ? '#2ecc71' : '#aaa'; ?>;">
                                ● <?php echo htmlspecialchars($item->estado); ?>
                            </span>
                            <h3 style="font-size: 1.1rem; margin: 8px 0 4px 0; font-weight: 800; color: var(--graphite); line-height: 1.2;">
                                <?php echo htmlspecialchars($item->titulo); ?>
                            </h3>
                            <p style="font-size: 0.8rem; color: #888;">
                                <?php echo htmlspecialchars($item->edicion_nombre); ?> • <?php echo htmlspecialchars($item->region); ?>
                            </p>
                        </div>

                        <a href="editar_item.php?id=<?php echo $item->id; ?>" 
                           style="display: block; width: 100%; padding: 12px; border-radius: 50px; background: var(--graphite); color: white; text-decoration: none; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; text-align: center; transition: 0.3s;"
                           onmouseover="this.style.background='#333';"
                           onmouseout="this.style.background='var(--graphite)';"
                        >
                            Gestionar Copia
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>