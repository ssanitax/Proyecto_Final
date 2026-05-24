<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$juego_id = $_GET['juego_id'] ?? null;

if (!$juego_id) {
    header('Location: buscar.php');
    exit();
}

$stmt = $pdo->prepare("SELECT titulo FROM juegos WHERE id = ?");
$stmt->execute([$juego_id]);
$titulo_juego = $stmt->fetchColumn();

$plataformas = $pdo->query("SELECT * FROM plataformas ORDER BY nombre ASC")->fetchAll();

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 600px; margin: 0 auto;">
    <header style="text-align: center; margin-bottom: 30px;">
        <h2><?php echo $lang['frontend_propose_edition_title']; ?></h2>
        <p style="color: #666;"><?php echo $lang['frontend_propose_edition_desc']; ?> <strong><?php echo htmlspecialchars($titulo_juego); ?></strong></p>
    </header>

    <div class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee;">
        <form action="../../controllers/JuegoController.php?action=proponer_edicion_existente" method="POST">
            <input type="hidden" name="juego_id" value="<?php echo $juego_id; ?>">

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;"><?php echo $lang['frontend_propose_edition_label_platform']; ?></label>
                <select name="plataforma_id" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
                    <?php foreach($plataformas as $p): ?>
                        <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->nombre); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;"><?php echo $lang['frontend_propose_edition_label_edition_name']; ?></label>
                <input type="text" name="edicion_nombre" placeholder="<?php echo $lang['frontend_propose_edition_placeholder_edition']; ?>" required
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
            </div>

            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 8px;"><?php echo $lang['frontend_propose_edition_label_regional_lock']; ?></label>
                <p style="font-size: 0.8rem; color: #777; margin-bottom: 12px; line-height: 1.4;"><?php echo $lang['frontend_propose_edition_regional_lock_help']; ?></p>
                <label style="display: block; margin-bottom: 8px; font-size: 0.9rem; cursor: pointer;">
                    <input type="radio" name="bloqueo_regional" value="0" checked style="margin-right: 8px;">
                    <?php echo $lang['frontend_propose_edition_regional_lock_no']; ?>
                </label>
                <label style="display: block; font-size: 0.9rem; cursor: pointer;">
                    <input type="radio" name="bloqueo_regional" value="1" style="margin-right: 8px;">
                    <?php echo $lang['frontend_propose_edition_regional_lock_yes']; ?>
                </label>
            </div>

            <button type="submit"
                style="width: 100%; padding: 15px; border-radius: 50px;
                    background: var(--graphite); color: white; font-weight: 700;
                    text-transform: uppercase; border: none; cursor: pointer;
                    transition: 0.3s;"
                onmouseover="this.style.background='#333'; this.style.transform='translateY(-2px)';"
                onmouseout="this.style.background='var(--graphite)'; this.style.transform='translateY(0)';"
            >
                <?php echo $lang['frontend_propose_edition_submit']; ?>
            </button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
