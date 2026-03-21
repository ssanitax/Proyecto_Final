<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$id_juego = $_GET['id'] ?? null;

if (!$id_juego) {
    header('Location: buscar.php');
    exit();
}

// 1. Datos generales del juego (Maestro)
$stmt = $pdo->prepare("SELECT * FROM juegos WHERE id = ?");
$stmt->execute([$id_juego]);
$juego = $stmt->fetch();

if (!$juego) { die("Juego no encontrado."); }

// 2. Obtener las variantes físicas (Consola + Región)
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

<div class="fade-up visible" style="max-width: 900px; margin: 0 auto;">
    <div class="about-box" style="display: flex; gap: 40px; align-items: flex-start; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 280px;">
            <div style="background: #1c1f26; aspect-ratio: 3/4; display: flex; align-items: center; justify-content: center; font-size: 6rem; border-radius: 15px; color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                🎮
            </div>
            <p style="margin-top: 20px; color: #888; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Base de datos #<?php echo $juego->id; ?></p>
        </div>

        <div style="flex: 1.5; min-width: 320px; text-align: left;">
            <h2 style="text-align: left; margin-bottom: 5px;"><?php echo htmlspecialchars($juego->titulo); ?></h2>
            <p style="color: #666; margin-bottom: 30px;"><?php echo htmlspecialchars($juego->desarrollador); ?></p>
            
            <div style="border-top: 1px solid #eee; padding-top: 25px;">
                <h4 style="margin-bottom: 20px; font-size: 0.85rem; color: #333; font-weight: 800;">SELECCIONA TU VERSIÓN FÍSICA</h4>
                
                <form action="../../controllers/ColeccionController.php?action=agregar" method="POST">
                    <div style="display: grid; gap: 12px;">
                        <?php if (empty($ediciones)): ?>
                            <p style="color: #999; font-style: italic; padding: 20px; background: #f9f9f9; border-radius: 10px;">No hay consolas registradas para este título.</p>
                        <?php else: ?>
                            <?php foreach($ediciones as $edic): ?>
                                <label class="version-card">
                                    <input type="radio" name="edicion_id" value="<?php echo $edic->id; ?>" required>
                                    <div class="version-details">
                                        <div class="plat-name"><?php echo htmlspecialchars($edic->plataforma_nombre); ?></div>
                                        <div class="edic-info">
                                            <?php echo htmlspecialchars($edic->edicion_nombre); ?> 
                                            <span class="region-pill"><?php echo htmlspecialchars($edic->region); ?></span>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                            
                            <button type="submit" class="btn-confirm">Añadir a mi Biblioteca</button>
                        <?php endif; ?>
                    </div>
                </form>

                <div style="margin-top: 40px; padding: 20px; background: #f4f5f7; border-radius: 12px; text-align: center;">
                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">¿Tienes este juego en otra consola o región?</p>
                    <a href="proponer_edicion.php?juego_id=<?php echo $juego->id; ?>" style="color: var(--graphite); font-weight: 800; text-decoration: none; font-size: 0.85rem;">
                        + Sugerir plataforma/idioma faltante
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .version-card {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        border: 2px solid #eee;
        border-radius: 12px;
        cursor: pointer;
        transition: 0.2s ease-in-out;
    }
    .version-card:hover { border-color: var(--silver); background: #fafafa; }
    .version-card input[type="radio"] { margin-right: 20px; accent-color: var(--graphite); transform: scale(1.2); }
    
    /* Efecto cuando está seleccionado */
    .version-card:has(input:checked) { border-color: var(--graphite); background: #f0f2f5; }

    .plat-name { font-weight: 800; font-size: 1rem; color: var(--graphite); text-transform: uppercase; }
    .edic-info { font-size: 0.85rem; color: #666; margin-top: 3px; }
    .region-pill { font-size: 0.7rem; background: #ddd; padding: 2px 6px; border-radius: 4px; margin-left: 5px; font-weight: 600; }
    
    .btn-confirm {
        margin-top: 20px;
        background: var(--graphite);
        color: white;
        padding: 18px;
        border: none;
        border-radius: 50px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-confirm:hover { background: #333; transform: translateY(-2px); }
</style>

<?php include '../../includes/footer.php'; ?>
