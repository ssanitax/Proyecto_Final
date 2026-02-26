<?php
require_once '../../includes/auth.php';
redirigirSiNoLogueado();
require_once '../../config/config.php';

// 1. Capturamos el ID del juego de la URL
$id_juego = $_GET['id'] ?? null;

if (!$id_juego) {
    header('Location: buscar.php');
    exit();
}

// 2. Obtener datos del juego maestro
$stmt = $pdo->prepare("SELECT * FROM juegos WHERE id = ?");
$stmt->execute([$id_juego]);
$juego = $stmt->fetch();

if (!$juego) {
    die("El juego no existe en la base de datos maestra.");
}

// 3. Obtener las ediciones disponibles (ESTO ES LO QUE TE DABA ERROR)
$stmtEdic = $pdo->prepare("
    SELECT e.*, p.nombre as plataforma_nombre 
    FROM ediciones e 
    JOIN plataformas p ON e.plataforma_id = p.id 
    WHERE e.juego_id = ?
");
$stmtEdic->execute([$id_juego]);
$ediciones = $stmtEdic->fetchAll(); // Aquí se crea la variable $ediciones

include '../../includes/header.php';
?>

<style>
    .edition-selector {
        display: flex; 
        align-items: center; 
        padding: 20px; 
        border: 2px solid #eee; 
        border-radius: 12px; 
        cursor: pointer;
        transition: 0.3s;
        margin-bottom: 10px;
        text-align: left;
    }
    .edition-selector:hover {
        border-color: var(--graphite);
        background: #f9f9f9;
    }
    /* Estilo para cuando el radio button está seleccionado */
    .edition-selector:has(input:checked) {
        border-color: var(--graphite);
        background: #f0f2f5;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .platform-tag {
        background: var(--graphite);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 800;
        margin-bottom: 5px;
        display: inline-block;
        text-transform: uppercase;
    }
    .btn-add { 
        background: var(--graphite); 
        color: white; 
        padding: 15px; 
        border-radius: 50px; 
        font-weight: 600; 
        font-size: 1rem; 
        width: 100%; 
        border: none; 
        cursor: pointer;
        margin-top: 20px;
    }
</style>

<div class="fade-up visible" style="max-width: 900px; margin: 0 auto;">
    <div class="about-box" style="display: flex; gap: 40px; align-items: flex-start; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 250px;">
            <div style="background: #eee; aspect-ratio: 3/4; display: flex; align-items: center; justify-content: center; font-size: 6rem; border-radius: 15px; box-shadow: 0 10px 20px rgba(0,0,0,0.05);">
                🎮
            </div>
        </div>

        <div style="flex: 2; min-width: 300px; text-align: left;">
            <h2 style="text-align: left; margin-bottom: 5px;"><?php echo htmlspecialchars($juego->titulo); ?></h2>
            <p style="color: #888; font-weight: 600; margin-bottom: 20px;"><?php echo htmlspecialchars($juego->desarrollador); ?></p>
            
            <p style="color: #555; line-height: 1.6; margin-bottom: 30px;">
                <?php echo htmlspecialchars($juego->descripcion); ?>
            </p>
            
            <div style="border-top: 1px solid #eee; padding-top: 25px;">
                <h4 style="margin-bottom: 20px; font-size: 0.9rem; letter-spacing: 1px; color: #333;">EDICIONES DISPONIBLES</h4>
                
                <form action="../../controllers/ColeccionController.php?action=agregar" method="POST">
                    <div style="display: grid; gap: 10px;">
                        <?php if (empty($ediciones)): ?>
                            <p style="color: #999; font-style: italic;">No hay ediciones registradas para este juego.</p>
                        <?php else: ?>
                            <?php foreach($ediciones as $edic): ?>
                                <label class="edition-selector">
                                    <input type="checkbox" name="ediciones_ids[]" value="<?php echo $edic->id; ?>" style="margin-right: 20px; transform: scale(1.2);">
                                    <div>
                                        <span class="platform-tag"><?php echo htmlspecialchars($edic->plataforma_nombre); ?></span>
                                        <div style="font-weight: 700; color: var(--graphite);">
                                            <?php echo htmlspecialchars($edic->edicion_nombre); ?> 
                                            <span style="color: #888; font-weight: 400; font-size: 0.85rem; margin-left: 5px;">
                                                (<?php echo htmlspecialchars($edic->region); ?>)
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                            
                            <button type="submit" class="btn-add">
                                Confirmar y añadir a mi biblioteca
                            </button>
                        <?php endif; ?>
                    </div>
                </form>

                <div style="margin-top: 30px; text-align: center; border-top: 1px solid #f5f5f5; padding-top: 20px;">
                    <p style="font-size: 0.85rem; color: #999;">
                        ¿No ves tu edición o plataforma? 
                        <a href="registrar_nuevo.php" style="color: var(--graphite); font-weight: 600; text-decoration: none;">Propón una nueva edición</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
