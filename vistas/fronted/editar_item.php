<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$id_coleccion = $_GET['id'] ?? null;

// 1. Obtener los datos actuales del juego en la estantería del usuario
$stmt = $pdo->prepare("
    SELECT cu.*, j.titulo, e.edicion_nombre, p.nombre as plataforma
    FROM coleccion_usuario cu
    JOIN ediciones e ON cu.edicion_id = e.id
    JOIN juegos j ON e.juego_id = j.id
    JOIN plataformas p ON e.plataforma_id = p.id
    WHERE cu.id = ? AND cu.usuario_id = ?
");
$stmt->execute([$id_coleccion, $_SESSION['usuario_id']]);
$item = $stmt->fetch();

if (!$item) {
    die($lang['frontend_edit_item_not_found']);
}

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 700px; margin: 0 auto;">
    <header style="text-align: center; margin-bottom: 40px;">
        <h2 style="margin-bottom: 10px;"><?php echo $lang['frontend_edit_item_title']; ?></h2>
        <p style="color: #666;"><?php echo sprintf($lang['frontend_edit_item_desc'], htmlspecialchars($item->titulo)); ?></p>
    </header>

    <div class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 30px;">
        <form action="../../controllers/ColeccionController.php?action=actualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $item->id; ?>">

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
                    <input type="number" name="valoracion" min="1" max="10" value="<?php echo $item->valoracion_personal; ?>" 
                           style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee;">
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
                <a href="../../controllers/ColeccionController.php?action=eliminar&id=<?php echo $item->id; ?>" 
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
        
        <form action="../../controllers/PrestamoController.php?action=registrar" method="POST">
            <input type="hidden" name="coleccion_id" value="<?php echo $item->id; ?>">
            
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
