<?php
require_once '../../includes/auth.php';
redirigirSiNoLogueado();
require_once '../../config/config.php';

$id_juego = $_GET['id'] ?? null;

// 1. Obtener datos del juego maestro
$stmt = $pdo->prepare("SELECT * FROM juegos WHERE id = ?");
$stmt->execute([$id_juego]);
$juego = $stmt->fetch();

// 2. Obtener las ediciones disponibles para este juego (PS2, Switch, etc.)
$stmtEdic = $pdo->prepare("
    SELECT e.*, p.nombre as plataforma_nombre 
    FROM ediciones e 
    JOIN plataformas p ON e.plataforma_id = p.id 
    WHERE e.juego_id = ?
");
$stmtEdic->execute([$id_juego]);
$ediciones = $stmtEdic->fetchAll();

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 800px; margin: 0 auto;">
    <div class="about-box" style="display: flex; gap: 30px; align-items: flex-start;">
        <div style="flex: 1; background: #eee; aspect-ratio: 3/4; display: flex; align-items: center; justify-content: center; font-size: 5rem; border-radius: 10px;">
            🎮
        </div>

        <div style="flex: 2;">
            <h2 style="text-align: left; margin-bottom: 10px;"><?php echo htmlspecialchars($juego->titulo); ?></h2>
            <p style="color: #666; margin-bottom: 20px;"><?php echo htmlspecialchars($juego->descripcion); ?></p>
            
            <div style="border-top: 1px solid #eee; padding-top: 20px;">
                <h4 style="margin-bottom: 15px;">Selecciona tu edición para añadir:</h4>
                
                <form action="../../controllers/ColeccionController.php?action=agregar" method="POST">
                    <input type="hidden" name="usuario_id" value="<?php echo $_SESSION['usuario_id']; ?>">
                    
                    <div style="display: grid; gap: 10px;">
                        <?php foreach($ediciones as $edic): ?>
                            <label style="display: flex; align-items: center; padding: 15px; border: 1px solid #ddd; border-radius: 10px; cursor: pointer; transition: 0.3s;" class="edition-selector">
                                <input type="radio" name="edicion_id" value="<?php echo $edic->id; ?>" required style="margin-right: 15px;">
                                <div>
                                    <strong><?php echo $edic->plataforma_nombre; ?></strong> - 
                                    <?php echo $edic->edicion_nombre; ?> (<?php echo $edic->region; ?>)
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="btn-add" style="margin-top: 25px; width: 100%; border: none; cursor: pointer;">
                        Confirmar y añadir a mi biblioteca
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .edition-selector:hover { background: #f9f9f9; border-color: var(--graphite); }
    .btn-add { background: var(--graphite); color: white; padding: 15px; border-radius: 50px; font-weight: 600; font-size: 1rem; }
</style>

<?php include '../../includes/footer.php'; ?>
