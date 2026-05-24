<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$stmtPlat = $pdo->query("SELECT * FROM plataformas ORDER BY nombre ASC");
$plataformas = $stmtPlat->fetchAll();

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 700px; margin: 0 auto;">
    <h2><?php echo $lang['frontend_register_game_title']; ?></h2>
    <p style="text-align:center; color:#666; margin-bottom:30px;">
        <?php echo $lang['frontend_register_game_desc']; ?>
    </p>

    <form action="../../controllers/JuegoController.php?action=proponer" method="POST" class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee;">
        <h3 style="text-align:left; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:10px;"><?php echo $lang['frontend_register_game_master_data']; ?></h3>
        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_title']; ?></label>
            <input type="text" name="titulo" placeholder="<?php echo $lang['frontend_register_game_placeholder_title']; ?>" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
        </div>

        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_developer']; ?></label>
            <input type="text" name="desarrollador" placeholder="<?php echo $lang['frontend_register_game_placeholder_developer']; ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
        </div>

        <h3 style="text-align:left; margin:30px 0 20px 0; border-bottom:1px solid #eee; padding-bottom:10px;"><?php echo $lang['frontend_register_game_edition_data']; ?></h3>
        <div class="form-group" style="margin-bottom:20px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;"><?php echo $lang['frontend_register_game_label_platform']; ?></label>
            <select name="plataforma_id" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd; font-family: inherit;">
                <?php foreach($plataformas as $p): ?>
                    <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->nombre); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group" style="margin-bottom:10px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem; display:block; margin-bottom:8px;"><?php echo $lang['frontend_register_game_label_regional_lock']; ?></label>
            <p style="font-size:0.8rem; color:#777; margin-bottom:12px; line-height:1.4;"><?php echo $lang['frontend_register_game_regional_lock_help']; ?></p>
            <label style="display:block; margin-bottom:8px; font-size:0.9rem; cursor:pointer;">
                <input type="radio" name="bloqueo_regional" value="0" checked style="margin-right:8px;">
                <?php echo $lang['frontend_register_game_regional_lock_no']; ?>
            </label>
            <label style="display:block; font-size:0.9rem; cursor:pointer;">
                <input type="radio" name="bloqueo_regional" value="1" style="margin-right:8px;">
                <?php echo $lang['frontend_register_game_regional_lock_yes']; ?>
            </label>
        </div>

        <button type="submit"
            style="margin-top: 30px; width: 100%; padding: 15px; border-radius: 50px;
                background: var(--graphite); color: white; font-weight: 700;
                text-transform: uppercase; border: none; cursor: pointer;
                transition: 0.3s;"
            onmouseover="this.style.background='#333'; this.style.transform='translateY(-2px)';"
            onmouseout="this.style.background='var(--graphite)'; this.style.transform='translateY(0)';"
        >
            <?php echo $lang['frontend_register_game_submit']; ?>
        </button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
