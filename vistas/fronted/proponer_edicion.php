<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$juego_id = (int)($_GET['juego_id'] ?? 0);

if ($juego_id <= 0) {
    header('Location: buscar.php');
    exit();
}

$stmt = $pdo->prepare("SELECT titulo, fecha_lanzamiento FROM juegos WHERE id = ?");
$stmt->execute([$juego_id]);
$juego = $stmt->fetch();

if (!$juego) {
    header('Location: buscar.php');
    exit();
}

$plataformas = $pdo->query("SELECT id, nombre FROM plataformas ORDER BY nombre ASC")->fetchAll();
include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 600px; margin: 0 auto;">
    <header style="text-align: center; margin-bottom: 30px;">
        <h2><?php echo $lang['frontend_propose_edition_title']; ?></h2>
        <p style="color: #666;"><?php echo $lang['frontend_propose_edition_desc']; ?> <strong><?php echo htmlspecialchars($juego->titulo); ?></strong></p>
    </header>

    <?php if (isset($_GET['error'])): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:14px;border-radius:12px;margin-bottom:20px;font-weight:600;text-align:center;">
            <?php
                $errores = [
                    'missing_platform' => $lang['frontend_register_game_error_platform'],
                    'missing_date' => $lang['frontend_register_game_error_date'],
                ];
                echo $errores[$_GET['error']] ?? $lang['error_general'];
            ?>
        </div>
    <?php endif; ?>

    <div class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee;">
        <form action="../../controllers/JuegoController.php?action=proponer_edicion_existente" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="juego_id" value="<?php echo $juego_id; ?>">

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;"><?php echo $lang['frontend_propose_edition_label_platform']; ?> *</label>
                <select name="plataforma_id" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
                    <option value=""><?php echo $lang['frontend_register_game_select_platform']; ?></option>
                    <?php foreach ($plataformas as $p): ?>
                        <option value="<?php echo (int)$p->id; ?>"><?php echo htmlspecialchars($p->nombre); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;"><?php echo $lang['frontend_register_game_label_release_date']; ?> *</label>
                <input type="date" name="fecha_lanzamiento" required
                       value="<?php echo htmlspecialchars($juego->fecha_lanzamiento ?? ''); ?>"
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
            </div>

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;"><?php echo $lang['frontend_propose_edition_label_edition_name']; ?></label>
                <input type="text" name="edicion_nombre" placeholder="<?php echo $lang['frontend_propose_edition_placeholder_edition']; ?>"
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
            </div>

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;"><?php echo $lang['frontend_register_game_label_cover']; ?></label>
                <p style="font-size: 0.8rem; color: #777; margin-bottom: 10px;"><?php echo $lang['frontend_register_game_cover_help']; ?></p>
                <input type="file" name="portada" accept="image/jpeg,image/png,image/webp" style="width: 100%;">
            </div>

            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 8px;"><?php echo $lang['frontend_propose_edition_label_regional_lock']; ?></label>
                <p style="font-size: 0.8rem; color: #777; margin-bottom: 12px;"><?php echo $lang['frontend_propose_edition_regional_lock_help_v2']; ?></p>
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; cursor: pointer;">
                    <input type="radio" name="bloqueo_regional" value="0" checked style="margin-right: 8px;">
                    <?php echo $lang['frontend_propose_edition_regional_lock_no']; ?>
                </label>
                <label style="display: block; font-size: 0.9rem; cursor: pointer;">
                    <input type="radio" name="bloqueo_regional" value="1" style="margin-right: 8px;">
                    <?php echo $lang['frontend_propose_edition_regional_lock_yes']; ?>
                </label>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; border-radius: 50px; background: var(--graphite); color: white; font-weight: 700; border: none; cursor: pointer;">
                <?php echo $lang['frontend_propose_edition_submit']; ?>
            </button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
