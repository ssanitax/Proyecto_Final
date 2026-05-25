<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$plataformas = $pdo->query("SELECT id, nombre FROM plataformas ORDER BY nombre ASC")->fetchAll();
$idiomas = [];
try {
    $idiomas = $pdo->query("SELECT id, nombre FROM idiomas ORDER BY nombre ASC")->fetchAll();
} catch (PDOException $e) {
    $idiomas = [];
}

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 700px; margin: 0 auto;">
    <h2><?php echo $lang['frontend_register_game_title']; ?></h2>
    <p style="text-align:center; color:#666; margin-bottom:30px;">
        <?php echo $lang['frontend_register_game_desc']; ?>
    </p>

    <?php if (isset($_GET['error'])): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:14px;border-radius:12px;margin-bottom:20px;font-weight:600;text-align:center;">
            <?php
                $errores = [
                    'missing_title' => $lang['frontend_register_game_error_title'],
                    'missing_platform' => $lang['frontend_register_game_error_platform'],
                    'missing_date' => $lang['frontend_register_game_error_date'],
                    'missing_language' => $lang['frontend_register_game_error_language'],
                ];
                echo $errores[$_GET['error']] ?? $lang['error_general'];
            ?>
        </div>
    <?php endif; ?>

    <form action="../../controllers/JuegoController.php?action=proponer" method="POST" enctype="multipart/form-data" class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee;">
        <h3 style="text-align:left; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:10px;"><?php echo $lang['frontend_register_game_master_data']; ?></h3>

        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_title']; ?> *</label>
            <input type="text" name="titulo" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
        </div>

        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_developer']; ?></label>
            <input type="text" name="desarrollador" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
        </div>

        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_release_date']; ?> *</label>
            <input type="date" name="fecha_lanzamiento" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
        </div>

        <h3 style="text-align:left; margin:30px 0 20px 0; border-bottom:1px solid #eee; padding-bottom:10px;"><?php echo $lang['frontend_register_game_edition_data']; ?></h3>

        <div class="form-group" style="margin-bottom:20px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_platform']; ?> *</label>
            <?php if (empty($plataformas)): ?>
                <p style="color:#b45309;font-size:0.85rem;"><?php echo $lang['frontend_register_game_no_platforms']; ?></p>
            <?php else: ?>
            <select name="plataforma_id" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                <option value=""><?php echo $lang['frontend_register_game_select_platform']; ?></option>
                <?php foreach ($plataformas as $p): ?>
                    <option value="<?php echo (int)$p->id; ?>"><?php echo htmlspecialchars($p->nombre); ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_languages_available']; ?></label>
            <p style="font-size:0.8rem;color:#777;margin:0 0 10px;"><?php echo $lang['frontend_register_game_languages_available_help']; ?></p>
            <?php if (!empty($idiomas)): ?>
            <div style="display:flex; flex-wrap:wrap; gap:10px;">
                <?php foreach ($idiomas as $idioma): ?>
                    <label style="display:flex; align-items:center; gap:6px; padding:8px 12px; background:#f4f5f7; border-radius:8px; font-size:0.85rem; cursor:pointer;">
                        <input type="checkbox" name="idiomas_disponibles[]" value="<?php echo (int)$idioma->id; ?>" checked>
                        <?php echo htmlspecialchars($idioma->nombre); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <p style="font-size:0.75rem;color:#999;margin:10px 0 0;"><?php echo $lang['frontend_register_game_languages_available_hint']; ?></p>
            <?php else: ?>
            <p style="color:#b45309;font-size:0.85rem;"><?php echo $lang['frontend_register_game_no_languages_catalog']; ?></p>
            <?php endif; ?>
        </div>

        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_cover']; ?></label>
            <p style="font-size:0.8rem;color:#777;margin:0 0 10px;"><?php echo $lang['frontend_register_game_cover_help']; ?></p>
            <input type="file" name="portada" accept="image/jpeg,image/png,image/webp" style="width:100%;">
        </div>

        <div class="form-group" style="margin-bottom:10px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem; display:block; margin-bottom:8px;"><?php echo $lang['frontend_register_game_label_regional_lock']; ?></label>
            <p style="font-size:0.8rem; color:#777; margin-bottom:12px;"><?php echo $lang['frontend_register_game_regional_lock_help_v2']; ?></p>
            <label style="display:block; margin-bottom:8px; font-size:0.9rem; cursor:pointer;">
                <input type="radio" name="bloqueo_regional" value="0" checked style="margin-right:8px;">
                <?php echo $lang['frontend_register_game_regional_lock_no']; ?>
            </label>
            <label style="display:block; font-size:0.9rem; cursor:pointer;">
                <input type="radio" name="bloqueo_regional" value="1" style="margin-right:8px;">
                <?php echo $lang['frontend_register_game_regional_lock_yes']; ?>
            </label>
        </div>

        <button type="submit" class="btn-submit" style="margin-top:30px;width:100%;padding:15px;border-radius:50px;background:var(--graphite);color:white;font-weight:700;border:none;cursor:pointer;">
            <?php echo $lang['frontend_register_game_submit']; ?>
        </button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
