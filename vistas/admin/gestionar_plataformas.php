<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin();
$pdo = $GLOBALS['pdo'];

// 1. PROCESAR EL ALTA DE UNA NUEVA PLATAFORMA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_plataforma'])) {
    $nombre = htmlspecialchars(trim($_POST['nombre_plataforma']));
    if (!empty($nombre)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO plataformas (nombre) VALUES (?)");
            $stmt->execute([$nombre]);
            $mensaje = $lang['admin_platform_added'];
        } catch (PDOException $e) {
            $error = $lang['admin_platform_exists'];
        }
    }
}

// 2. OBTENER TODAS LAS PLATAFORMAS ACTUALES [cite: 799]
$plataformas = $pdo->query("SELECT * FROM plataformas ORDER BY nombre ASC")->fetchAll();

include '../../includes/admin_header.php'; 
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;"><?php echo $lang['admin_platform_management']; ?></h2>
        <p style="color: #666;"><?php echo $lang['admin_platform_management_desc']; ?></p>
    </header>

    <div style="max-width: 600px; margin: 0 auto;">
        <div class="dash-card" style="margin-bottom: 40px; padding: 30px;">
            <h3 style="margin-bottom: 20px; font-size: 1.1rem;"><?php echo $lang['admin_add_platform_title']; ?></h3>
            
            <?php if (isset($mensaje)): ?>
                <p style="color: #2ecc71; font-weight: 700; margin-bottom: 15px; font-size: 0.9rem;"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p style="color: #e74c3c; font-weight: 700; margin-bottom: 15px; font-size: 0.9rem;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="" method="POST" style="width: 100%;">
                <input type="text" name="nombre_plataforma" placeholder="<?php echo $lang['admin_platform_placeholder']; ?>" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee; margin-bottom: 15px; font-family: inherit;">
                <button type="submit" class="btn-dash" style="width: 100%; border: none; cursor: pointer;"><?php echo $lang['admin_platform_save']; ?></button>
            </form>
        </div>

        <div class="dash-card" style="padding: 30px;">
            <h3 style="margin-bottom: 20px; font-size: 1.1rem;"><?php echo $lang['admin_platforms_list_title']; ?></h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                <?php foreach ($plataformas as $plat): ?>
                    <span style="background: #f0f0f0; padding: 8px 15px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; color: var(--graphite);">
                        <?php echo htmlspecialchars($plat->nombre); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>