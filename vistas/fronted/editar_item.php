<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../includes/catalogo.php';

$id_coleccion = $_GET['id'] ?? null;

$stmt = $pdo->prepare("
    SELECT cu.*, j.titulo, e.juego_id, e.edicion_nombre, e.imagen_portada, p.nombre as plataforma
    FROM coleccion_usuario cu
    JOIN ediciones e ON cu.edicion_id = e.id
    JOIN juegos j ON e.juego_id = j.id
    JOIN plataformas p ON e.plataforma_id = p.id
    WHERE cu.id = ? AND cu.usuario_id = ?
");
$stmt->execute([$id_coleccion, $_SESSION['usuario_id']]);
$item = $stmt->fetch();

$idiomas = todosLosIdiomas($pdo);
$regiones = regionesParaSelector($pdo);

if (!$item) {
    die($lang['frontend_edit_item_not_found']);
}

$returnTo = urlRetornoFrontendSegura($_GET['return_to'] ?? '')
    ?? ('coleccion_juego.php?juego_id=' . (int)$item->juego_id);

include '../../includes/header.php';
?>

<style>
    .rating-panel {
        background: #f8f9fb;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 14px;
    }

    .rating-panel-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        gap: 10px;
        flex-wrap: wrap;
    }

    .rating-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border: 1px solid #d1d5db;
        background: white;
        color: #4b5563;
    }

    .rating-input {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        font-size: 1rem;
        font-weight: 700;
        color: var(--graphite);
        background: white;
    }

    .rating-input:focus {
        outline: none;
        border-color: var(--graphite);
        box-shadow: 0 0 0 3px rgba(28, 31, 38, 0.08);
    }

    .rating-help {
        margin-top: 8px;
        font-size: 0.78rem;
        color: #6b7280;
        line-height: 1.4;
    }
</style>

<div class="fade-up visible" style="max-width: 700px; margin: 0 auto;">
    <a href="coleccion_juego.php?juego_id=<?php echo (int)$item->juego_id; ?>" style="display: inline-block; color: #666; text-decoration: none; font-weight: 700; font-size: 0.85rem; margin-bottom: 20px;">
        ← <?php echo $lang['frontend_collection_back']; ?>
    </a>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'no_region'): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:14px;border-radius:12px;margin-bottom:20px;font-weight:600;text-align:center;">
            <?php echo $lang['frontend_game_detail_region_required']; ?>
        </div>
    <?php endif; ?>

    <header style="text-align: center; margin-bottom: 40px;">
        <h2 style="margin-bottom: 10px;"><?php echo $lang['frontend_edit_item_title']; ?></h2>
        <p style="color: #666;"><?php echo sprintf($lang['frontend_edit_item_desc'], htmlspecialchars($item->titulo)); ?></p>
    </header>

    <div class="about-box" style="padding: 18px; background: white; border-radius: 20px; border: 1px solid #eee; margin-bottom: 20px; display: flex; justify-content: center;">
        <div style="width: min(260px, 100%); aspect-ratio: 3/4; border-radius: 14px; overflow: hidden; background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
            <?php if (!empty($item->imagen_portada)): ?>
                <img src="../../img/portadas/<?php echo htmlspecialchars($item->imagen_portada); ?>" alt="<?php echo htmlspecialchars($item->titulo); ?>" style="width:100%; height:100%; object-fit:contain; object-position:center; display:block; background:#e8eaed;">
            <?php else: ?>
                <span style="font-size: 4rem;">🎮</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;">
        <form action="../../controllers/ColeccionController.php?action=actualizar" method="POST"
              data-scroll-return="<?php echo htmlspecialchars($returnTo, ENT_QUOTES); ?>">
            <input type="hidden" name="id" value="<?php echo $item->id; ?>">
            <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($returnTo, ENT_QUOTES); ?>">

            <?php if (!empty($idiomas)): ?>
            <div class="form-group" style="text-align: left; margin-bottom: 20px;">
                <label style="font-weight: 800; font-size: 0.75rem; color: var(--graphite); display: block; margin-bottom: 10px; text-transform: uppercase;"><?php echo $lang['frontend_edit_item_label_language']; ?></label>
                <p style="font-size: 0.8rem; color: #888; margin: 0 0 10px;"><?php echo $lang['frontend_game_detail_language_help']; ?></p>
                <select name="idioma_id" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee; font-family: inherit;">
                    <option value=""><?php echo $lang['frontend_game_detail_select_language']; ?></option>
                    <?php foreach ($idiomas as $idioma): ?>
                        <option value="<?php echo (int)$idioma->id; ?>" <?php echo ((int)$item->idioma_id === (int)$idioma->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($idioma->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if (!empty($regiones)): ?>
            <div class="form-group" style="text-align: left; margin-bottom: 20px;">
                <label style="font-weight: 800; font-size: 0.75rem; color: var(--graphite); display: block; margin-bottom: 10px; text-transform: uppercase;"><?php echo $lang['frontend_game_detail_label_region_copy']; ?></label>
                <p style="font-size: 0.8rem; color: #888; margin: 0 0 10px;"><?php echo $lang['frontend_game_detail_region_help']; ?></p>
                <select name="region_copia" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee; font-family: inherit;">
                    <option value=""><?php echo $lang['frontend_game_detail_region_none']; ?></option>
                    <?php foreach ($regiones as $reg): ?>
                        <option value="<?php echo htmlspecialchars($reg->nombre); ?>" <?php echo (($item->region ?? '') === $reg->nombre) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($reg->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div class="form-group" style="text-align: left;">
                    <label style="font-weight: 800; font-size: 0.75rem; color: var(--graphite); display: block; margin-bottom: 10px; text-transform: uppercase;"><?php echo $lang['frontend_edit_item_label_status']; ?></label>
                    <select name="estado" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee; font-family: inherit;">
                        <option value="pendiente" <?php echo $item->estado == 'pendiente' ? 'selected' : ''; ?>><?php echo $lang['frontend_edit_item_status_pending']; ?></option>
                        <option value="jugando" <?php echo $item->estado == 'jugando' ? 'selected' : ''; ?>><?php echo $lang['frontend_edit_item_status_playing']; ?></option>
                        <option value="completado" <?php echo $item->estado == 'completado' ? 'selected' : ''; ?>><?php echo $lang['frontend_edit_item_status_completed']; ?></option>
                    </select>
                </div>

                <div class="form-group" style="text-align: left;">
                    <label style="font-weight: 800; font-size: 0.75rem; color: var(--graphite); display: block; margin-bottom: 10px; text-transform: uppercase;"><?php echo $lang['frontend_edit_item_label_rating']; ?></label>
                    <div class="rating-panel">
                        <div class="rating-panel-head">
                            <span class="rating-chip"><?php echo $lang['frontend_ratings_your_label']; ?></span>
                            <span class="rating-chip">1-10</span>
                        </div>
                        <input type="number" name="valoracion" min="1" max="10" value="<?php echo $item->valoracion_personal; ?>" class="rating-input">
                        <p class="rating-help"><?php echo $lang['frontend_edit_item_rating_scope']; ?></p>
                    </div>
                </div>
            </div>

            <div class="form-group" style="text-align: left; margin-bottom: 30px;">
                <label style="font-weight: 800; font-size: 0.75rem; color: var(--graphite); display: block; margin-bottom: 10px; text-transform: uppercase;"><?php echo $lang['frontend_edit_item_label_notes']; ?></label>
                <textarea name="notas" rows="4" placeholder="<?php echo $lang['frontend_edit_item_notes_placeholder']; ?>" 
                          style="width: 100%; padding: 15px; border-radius: 10px; border: 1px solid #eee; font-family: inherit; resize: none;"><?php echo htmlspecialchars($item->notas); ?></textarea>
            </div>

            <div style="display: flex; gap: 15px; align-items: center;">
                <button type="submit" class="btn-submit" 
                        style="flex: 2; padding: 15px; border-radius: 50px; background: var(--graphite); color: white; border: none; font-weight: 700; cursor: pointer; text-transform: uppercase; transition: 0.3s;"
                        onmouseover="this.style.background='#333'; this.style.transform='translateY(-2px)';"
                        onmouseout="this.style.background='var(--graphite)'; this.style.transform='translateY(0)';"
                >
                    <?php echo $lang['frontend_edit_item_save']; ?>
                </button>
                <a href="../../controllers/ColeccionController.php?action=eliminar&id=<?php echo (int)$item->id; ?>&return_to=<?php echo urlencode($returnTo); ?>" 
                   style="flex: 1; text-align: center; color: #e74c3c; font-weight: 700; text-decoration: none; font-size: 0.85rem;"
                   onclick="return confirm('<?php echo $lang['frontend_edit_item_confirm_delete']; ?>')">
                    <?php echo $lang['frontend_edit_item_delete']; ?>
                </a>
            </div>
        </form>
    </div>

    <div class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee; border-top: 5px solid var(--graphite);">
        <h3 style="font-size: 1.2rem; margin-bottom: 20px; text-align: left; color: var(--graphite);"><?php echo $lang['frontend_edit_loan_title']; ?></h3>
        <p style="color: #777; font-size: 0.9rem; margin-bottom: 25px; text-align: left;"><?php echo $lang['frontend_edit_loan_desc']; ?></p>
        
        <form action="../../controllers/PrestamoController.php?action=registrar" method="POST"
              data-scroll-return="<?php echo htmlspecialchars($returnTo, ENT_QUOTES); ?>">
            <input type="hidden" name="coleccion_id" value="<?php echo $item->id; ?>">
            <input type="hidden" name="return_to" value="<?php echo htmlspecialchars($returnTo, ENT_QUOTES); ?>">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
                <div class="form-group" style="text-align: left;">
                    <label style="font-weight: 800; font-size: 0.75rem; color: var(--graphite); display: block; margin-bottom: 8px;"><?php echo $lang['frontend_edit_loan_label_person']; ?></label>
                    <input type="text" name="nombre_persona" placeholder="<?php echo $lang['frontend_edit_loan_label_person_placeholder']; ?>" required 
                           style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee;">
                </div>

                <div class="form-group" style="text-align: left;">
                    <label style="font-weight: 800; font-size: 0.75rem; color: var(--graphite); display: block; margin-bottom: 8px;"><?php echo $lang['frontend_edit_loan_label_date']; ?></label>
                    <input type="date" name="fecha_prestamo" value="<?php echo date('Y-m-d'); ?>" required 
                           style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee;">
                </div>
            </div>

            <button type="submit" 
                    style="width: 100%; padding: 15px; border-radius: 50px; background: white; color: var(--graphite); border: 2px solid var(--graphite); font-weight: 700; cursor: pointer; text-transform: uppercase; transition: 0.3s;"
                    onmouseover="this.style.background='var(--graphite)'; this.style.color='white';"
                    onmouseout="this.style.background='white'; this.style.color='var(--graphite)';"
            >
                <?php echo $lang['frontend_edit_loan_submit']; ?>
            </button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
