<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/Usuario.php';

$usuarioModel = new Usuario($pdo);
$usuario = $usuarioModel->obtenerPorId((int)$_SESSION['usuario_id']);

if (!$usuario || $usuario->rol !== 'usuario') {
    header('Location: dashboard.php');
    exit();
}

$status = $_GET['status'] ?? '';
$errorKey = $_GET['error'] ?? '';

include '../../includes/header.php';
?>

<style>
    .profile-wrap { max-width: 640px; margin: 0 auto; }
    .profile-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #eee;
        padding: 28px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .profile-card h3 {
        margin: 0 0 8px;
        font-size: 1.05rem;
        font-weight: 800;
    }
    .profile-card p.desc {
        color: #666;
        font-size: 0.85rem;
        margin: 0 0 20px;
        line-height: 1.5;
    }
    .profile-card.danger { border-color: #fecaca; }
    .profile-card.danger h3 { color: #b91c1c; }
    .form-group { margin-bottom: 14px; text-align: left; }
    .form-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #888;
        margin-bottom: 6px;
    }
    .form-group input {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #eee;
        font-family: inherit;
    }
    .form-group input:focus {
        outline: none;
        border-color: var(--graphite);
    }
    .form-group input[readonly] {
        background: #f9fafb;
        color: #666;
    }
    .password-pair {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }
    @media (max-width: 560px) {
        .password-pair { grid-template-columns: 1fr; }
    }
    .btn-profile {
        width: 100%;
        padding: 14px;
        border-radius: 50px;
        border: none;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        cursor: pointer;
        margin-top: 8px;
    }
    .btn-profile.primary { background: var(--graphite); color: white; }
    .btn-profile.danger { background: #dc2626; color: white; }
    .alert {
        padding: 14px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        text-align: center;
    }
    .alert.ok { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert.err { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<div class="fade-up visible profile-wrap">
    <header style="text-align: center; margin-bottom: 36px;">
        <h2 style="margin-bottom: 8px;"><?php echo $lang['frontend_profile_title']; ?></h2>
        <p style="color: #666;"><?php echo $lang['frontend_profile_desc']; ?></p>
    </header>

    <?php
    $mensajesOk = [
        'name_updated' => $lang['frontend_profile_name_updated'],
        'password_updated' => $lang['frontend_profile_password_updated'],
    ];
    $mensajesError = [
        'name_empty' => $lang['frontend_profile_name_empty'],
        'profile_password_required' => $lang['frontend_profile_password_required'],
        'profile_password_mismatch' => $lang['frontend_profile_password_mismatch'],
        'profile_password_wrong' => $lang['frontend_profile_password_wrong'],
        'new_password_empty' => $lang['frontend_profile_new_password_empty'],
        'new_password_mismatch' => $lang['frontend_profile_new_password_mismatch'],
        'new_password_short' => $lang['password_too_short'],
        'new_password_same' => $lang['frontend_profile_new_password_same'],
        'save_failed' => $lang['frontend_profile_save_failed'],
        'delete_failed' => $lang['frontend_profile_delete_failed'],
    ];
    if ($status && isset($mensajesOk[$status])): ?>
        <div class="alert ok"><?php echo $mensajesOk[$status]; ?></div>
    <?php endif;
    if ($errorKey && isset($mensajesError[$errorKey])): ?>
        <div class="alert err"><?php echo $mensajesError[$errorKey]; ?></div>
    <?php endif; ?>

    <div class="profile-card">
        <h3><?php echo $lang['frontend_profile_section_account']; ?></h3>
        <p class="desc"><?php echo $lang['frontend_profile_email_readonly']; ?></p>
        <div class="form-group">
            <label><?php echo $lang['frontend_profile_label_email']; ?></label>
            <input type="email" value="<?php echo htmlspecialchars($usuario->email); ?>" readonly>
        </div>
    </div>

    <div class="profile-card">
        <h3><?php echo $lang['frontend_profile_section_name']; ?></h3>
        <p class="desc"><?php echo $lang['frontend_profile_name_desc']; ?></p>
        <form action="../../controllers/PerfilController.php?action=actualizar_nombre" method="POST">
            <div class="form-group">
                <label><?php echo $lang['frontend_profile_label_name']; ?></label>
                <input type="text" name="nombre" value="<?php echo htmlspecialchars($usuario->nombre); ?>" required maxlength="100">
            </div>
            <div class="password-pair">
                <div class="form-group">
                    <label><?php echo $lang['frontend_profile_label_current_password']; ?></label>
                    <input type="password" name="password_actual" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label><?php echo $lang['frontend_profile_label_current_password_confirm']; ?></label>
                    <input type="password" name="password_actual_confirm" required autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn-profile primary"><?php echo $lang['frontend_profile_save_name']; ?></button>
        </form>
    </div>

    <div class="profile-card">
        <h3><?php echo $lang['frontend_profile_section_password']; ?></h3>
        <p class="desc"><?php echo $lang['frontend_profile_password_desc']; ?></p>
        <form action="../../controllers/PerfilController.php?action=actualizar_password" method="POST">
            <div class="password-pair">
                <div class="form-group">
                    <label><?php echo $lang['frontend_profile_label_current_password']; ?></label>
                    <input type="password" name="password_actual" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label><?php echo $lang['frontend_profile_label_current_password_confirm']; ?></label>
                    <input type="password" name="password_actual_confirm" required autocomplete="current-password">
                </div>
            </div>
            <div class="form-group">
                <label><?php echo $lang['frontend_profile_label_new_password']; ?></label>
                <input type="password" name="password_nueva" required minlength="6" autocomplete="new-password">
            </div>
            <div class="form-group">
                <label><?php echo $lang['frontend_profile_label_new_password_confirm']; ?></label>
                <input type="password" name="password_nueva_confirm" required minlength="6" autocomplete="new-password">
            </div>
            <button type="submit" class="btn-profile primary"><?php echo $lang['frontend_profile_save_password']; ?></button>
        </form>
    </div>

    <div class="profile-card danger">
        <h3><?php echo $lang['frontend_profile_section_delete']; ?></h3>
        <p class="desc"><?php echo $lang['frontend_profile_delete_desc']; ?></p>
        <form action="../../controllers/PerfilController.php?action=eliminar_cuenta" method="POST"
              onsubmit="return confirm('<?php echo htmlspecialchars($lang['frontend_profile_delete_confirm'], ENT_QUOTES); ?>');">
            <div class="password-pair">
                <div class="form-group">
                    <label><?php echo $lang['frontend_profile_label_current_password']; ?></label>
                    <input type="password" name="password_actual" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label><?php echo $lang['frontend_profile_label_current_password_confirm']; ?></label>
                    <input type="password" name="password_actual_confirm" required autocomplete="current-password">
                </div>
            </div>
            <button type="submit" class="btn-profile danger"><?php echo $lang['frontend_profile_delete_button']; ?></button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
