<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin();
require_once '../../config/config.php';

$usuarios = $pdo->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY id ASC")->fetchAll();

$status = $_GET['status'] ?? '';
$errorKey = $_GET['error'] ?? '';

$etiquetasRol = [
    'usuario' => $lang['admin_user_role_user'],
    'admin' => $lang['admin_user_role_admin'],
    'super_admin' => $lang['admin_user_role_super'],
];

include '../../includes/admin_header.php';
?>

<style>
    .user-admin-form {
        background: white;
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 32px;
        max-width: 520px;
    }
    .user-forms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }
    .user-admin-form input,
    .user-admin-form select {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #eee;
        margin-bottom: 12px;
        font-family: inherit;
        background: white;
    }
    .role-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
    }
    .role-badge.usuario { background: #f3f4f6; color: #4b5563; }
    .role-badge.admin { background: #dbeafe; color: #1e40af; }
    .role-badge.super_admin { background: #ede9fe; color: #5b21b6; }
</style>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;"><?php echo $lang['admin_user_management']; ?></h2>
        <p style="color: #666;"><?php echo esSuperAdmin() ? $lang['admin_user_management_desc_super'] : $lang['admin_user_management_desc']; ?></p>
    </header>

    <div style="max-width: 1100px; margin: 0 auto;">
        <?php if ($status === 'deleted'): ?>
            <p style="color: #2ecc71; font-weight: 700; margin-bottom: 15px;"><?php echo $lang['admin_user_deleted']; ?></p>
        <?php endif; ?>
        <?php if ($status === 'user_created'): ?>
            <p style="color: #2ecc71; font-weight: 700; margin-bottom: 15px;"><?php echo $lang['admin_user_user_created']; ?></p>
        <?php endif; ?>
        <?php if ($status === 'admin_created'): ?>
            <p style="color: #2ecc71; font-weight: 700; margin-bottom: 15px;"><?php echo $lang['admin_user_admin_created']; ?></p>
        <?php endif; ?>
        <?php if ($status === 'super_admin_created'): ?>
            <p style="color: #2ecc71; font-weight: 700; margin-bottom: 15px;"><?php echo $lang['admin_user_super_admin_created']; ?></p>
        <?php endif; ?>

        <?php
        $errores = [
            'cannot_delete' => $lang['admin_user_cannot_delete'],
            'cannot_delete_self' => $lang['admin_user_cannot_delete_self'],
            'cannot_delete_admin' => $lang['admin_user_cannot_delete_admin'],
            'cannot_delete_super' => $lang['admin_user_cannot_delete_super'],
            'no_permission' => $lang['admin_user_no_permission'],
            'user_fields' => $lang['admin_user_user_fields'],
            'user_password_mismatch' => $lang['passwords_not_match'],
            'user_password_short' => $lang['password_too_short'],
            'user_email_exists' => $lang['email_already_registered'],
            'not_found' => $lang['admin_user_not_found'],
        ];
        if ($errorKey && isset($errores[$errorKey])): ?>
            <p style="color: #e74c3c; font-weight: 700; margin-bottom: 15px;"><?php echo $errores[$errorKey]; ?></p>
        <?php endif; ?>

        <div class="user-forms-grid">
            <div class="user-admin-form">
                <h3 style="margin: 0 0 8px; font-weight: 800;"><?php echo $lang['admin_user_create_user_title']; ?></h3>
                <p style="color: #666; font-size: 0.85rem; margin-bottom: 16px;"><?php echo $lang['admin_user_create_user_desc']; ?></p>
                <form action="../../controllers/AdminController.php?action=crear_usuario" method="POST">
                    <input type="text" name="nombre" placeholder="<?php echo $lang['admin_user_table_name']; ?>" required>
                    <input type="email" name="email" placeholder="<?php echo $lang['admin_user_table_email']; ?>" required>
                    <input type="password" name="password" placeholder="<?php echo $lang['admin_user_password']; ?>" required minlength="6">
                    <input type="password" name="password_confirm" placeholder="<?php echo $lang['admin_user_password_confirm']; ?>" required minlength="6">
                    <button type="submit" class="btn-dash" style="width:100%;padding:12px;border-radius:50px;border:none;background:var(--graphite);color:white;font-weight:700;cursor:pointer;">
                        <?php echo $lang['admin_user_create_user_submit']; ?>
                    </button>
                </form>
            </div>

            <?php if (esSuperAdmin()): ?>
            <div class="user-admin-form">
                <h3 style="margin: 0 0 8px; font-weight: 800;"><?php echo $lang['admin_user_create_admin_title']; ?></h3>
                <p style="color: #666; font-size: 0.85rem; margin-bottom: 16px;"><?php echo $lang['admin_user_create_admin_desc']; ?></p>
                <form action="../../controllers/AdminController.php?action=crear_admin" method="POST">
                    <input type="text" name="nombre" placeholder="<?php echo $lang['admin_user_table_name']; ?>" required>
                    <input type="email" name="email" placeholder="<?php echo $lang['admin_user_table_email']; ?>" required>
                    <select name="rol" required>
                        <option value="admin"><?php echo $lang['admin_user_role_admin']; ?></option>
                        <option value="super_admin"><?php echo $lang['admin_user_role_super']; ?></option>
                    </select>
                    <input type="password" name="password" placeholder="<?php echo $lang['admin_user_password']; ?>" required minlength="6">
                    <input type="password" name="password_confirm" placeholder="<?php echo $lang['admin_user_password_confirm']; ?>" required minlength="6">
                    <button type="submit" class="btn-dash" style="width:100%;padding:12px;border-radius:50px;border:none;background:#5b21b6;color:white;font-weight:700;cursor:pointer;">
                        <?php echo $lang['admin_user_create_admin_submit']; ?>
                    </button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 12px; border-bottom: 2px solid #ccc; text-align: left;"><?php echo $lang['admin_user_table_id']; ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #ccc; text-align: left;"><?php echo $lang['admin_user_table_name']; ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #ccc; text-align: left;"><?php echo $lang['admin_user_table_email']; ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #ccc; text-align: left;"><?php echo $lang['admin_user_table_role']; ?></th>
                    <th style="padding: 12px; border-bottom: 2px solid #ccc; text-align: left;"><?php echo $lang['admin_user_table_actions']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <?php
                        $rolClass = preg_replace('/[^a-z_]/', '', $usuario->rol);
                        $rolLabel = $etiquetasRol[$usuario->rol] ?? $usuario->rol;
                        $puedeBorrar = puedeEliminarUsuario($usuario->rol, $usuario->id);
                    ?>
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo (int)$usuario->id; ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($usuario->nombre); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($usuario->email); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <span class="role-badge <?php echo htmlspecialchars($rolClass); ?>"><?php echo htmlspecialchars($rolLabel); ?></span>
                        </td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <?php if ($puedeBorrar): ?>
                                <a href="../../controllers/AdminController.php?action=eliminar_usuario&id=<?php echo (int)$usuario->id; ?>"
                                   style="color: #e74c3c; font-weight: 700; text-decoration: none;"
                                   onclick="return confirm('<?php echo htmlspecialchars($lang['admin_user_confirm_delete'], ENT_QUOTES); ?>');">
                                   <?php echo $lang['admin_user_delete']; ?>
                                </a>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>
