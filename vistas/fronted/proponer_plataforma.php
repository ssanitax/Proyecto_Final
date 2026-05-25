<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 600px; margin: 0 auto;">
    <header style="text-align: center; margin-bottom: 30px;">
        <h2><?php echo $lang['frontend_propose_platform_title']; ?></h2>
        <p style="color: #666;"><?php echo $lang['frontend_propose_platform_desc']; ?></p>
    </header>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'missing_date'): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 14px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; text-align: center;">
            <?php echo $lang['frontend_propose_error_missing_date']; ?>
        </div>
    <?php endif; ?>

    <div class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee;">
        <form action="../../controllers/JuegoController.php?action=sugerir_plataforma_independiente" method="POST">
            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;"><?php echo $lang['frontend_propose_platform_label_name']; ?></label>
                
                <input type="text" name="nombre_plataforma" placeholder="<?php echo $lang['frontend_propose_platform_placeholder']; ?>" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
            </div>
            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;"><?php echo $lang['frontend_propose_platform_label_release']; ?></label>
                <input type="date" name="fecha_lanzamiento_plataforma" required
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
            </div>
            
            <button type="submit" class="btn-confirm" style="width:100%; border:none; cursor:pointer; background:var(--graphite); color:white; padding:15px; border-radius:50px; font-weight:700; text-transform: uppercase;">
                <?php echo $lang['frontend_propose_platform_submit']; ?>
            </button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php';?>