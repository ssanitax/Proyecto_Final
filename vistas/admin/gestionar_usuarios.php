<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin();
require_once '../../config/config.php';

// Obtener todos los usuarios para admin
$usuarios = $pdo->query("SELECT id, nombre, email, rol FROM usuarios ORDER BY id ASC")->fetchAll();

// Mensajes de confirmación/errores de acción
$status = $_GET['status'] ?? '';
$errorKey = $_GET['error'] ?? '';

include '../../includes/admin_header.php';
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;"><?php echo $lang['admin_user_management']; ?></h2>
        <p style="color: #666;"><?php echo $lang['admin_user_management_desc']; ?></p>
    </header>

    <div style="max-width: 1100px; margin: 0 auto;">
        <?php if ($status === 'deleted'): ?>
            <p style="color: #2ecc71; font-weight: 700; margin-bottom: 15px; font-size: 0.95rem;">
                <?php echo $lang['admin_user_deleted']; ?>
            </p>
        <?php endif; ?>

        <?php if ($errorKey === 'cannot_delete' || $errorKey === 'cannot_delete_self'): ?>
            <p style="color: #e74c3c; font-weight: 700; margin-bottom: 15px; font-size: 0.95rem;">
                <?php echo $lang['admin_user_cannot_delete']; ?>
            </p>
        <?php endif; ?>

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
                    <tr>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($usuario->id); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($usuario->nombre); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($usuario->email); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($usuario->rol); ?></td>
                        <td style="padding: 12px; border-bottom: 1px solid #eee;">
                            <?php if ($usuario->rol !== 'admin' && $usuario->id != ($_SESSION['usuario_id'] ?? 0)): ?>
                                <a href="../../controllers/AdminController.php?action=eliminar_usuario&id=<?php echo urlencode($usuario->id); ?>" 
                                   style="color: #e74c3c; font-weight: 700; text-decoration: none;">
                                   <?php echo $lang['admin_user_delete']; ?>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>
