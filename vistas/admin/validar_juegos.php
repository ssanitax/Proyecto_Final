<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin();
require_once '../../config/config.php';

// Consulta corregida para traer el nombre de la plataforma sugerida
$sql = "SELECT jp.*, u.nombre as nombre_usuario, 
               ep.region, ep.plataforma_nombre_nueva, ep.juego_id_real,
               p.nombre as plataforma_oficial 
        FROM juegos_pendientes jp
        JOIN usuarios u ON jp.usuario_id = u.id
        LEFT JOIN ediciones_pendientes ep ON ep.juego_pendiente_id = jp.id
        LEFT JOIN plataformas p ON ep.plataforma_id = p.id
        WHERE jp.estado = 'pendiente'
        ORDER BY jp.created_at DESC";

$stmt = $pdo->query($sql);
$pendientes = $stmt->fetchAll();

include '../../includes/admin_header.php'; 
?>

<div class="fade-up visible">
    <header style="margin-bottom: 40px; text-align: left;">
        <h2><?php echo $lang['admin_validate_title']; ?></h2>
        <p style="color: #666;"><?php echo $lang['admin_validate_desc']; ?></p>
    </header>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'aprobado'): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 30px; font-weight: 600; border: 1px solid #a7f3d0;">
                <?php echo $lang['propuesta_aprobada']; ?>
            </div>
        <?php elseif ($_GET['status'] == 'rechazado'): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 30px; font-weight: 600; border: 1px solid #fecaca;">
                <?php echo $lang['propuesta_rechazada']; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="about-box" style="padding: 0; overflow: hidden; background: white; border-radius: 20px; border: 1px solid #eee;">
        <?php if (empty($pendientes)): ?>
            <div style="padding: 80px; text-align: center;">
                <span style="font-size: 3rem; display: block; margin-bottom: 20px;">✅</span>
                <p style="color: #999; font-style: italic;"><?php echo $lang['admin_validate_no_pending']; ?></p>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #fafafa; border-bottom: 2px solid #f0f0f0;">
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;"><?php echo $lang['admin_validate_table_user']; ?></th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;"><?php echo $lang['admin_validate_table_info']; ?></th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;"><?php echo $lang['admin_validate_table_action']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendientes as $p): ?>
                        <tr style="border-bottom: 1px solid #f5f5f5;">
                            <form action="../../controllers/AdminController.php?action=aprobar&id=<?php echo $p->id; ?>" method="POST">
                                <td style="padding: 20px;">
                                    <strong><?php echo htmlspecialchars($p->nombre_usuario); ?></strong><br>
                                    <small style="color: #999;"><?php echo date('d/m/Y', strtotime($p->created_at)); ?></small>
                                    <?php if($p->juego_id_real): ?>
                                        <div style="margin-top:8px; background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:4px; font-size:0.6rem; font-weight:800; display:inline-block;"><?php echo $lang['admin_validate_badge_existing']; ?></div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 20px;">
                                    <div style="margin-bottom: 10px;">
                                        <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;"><?php echo $lang['admin_validate_label_platform']; ?></label>
                                        <input type="text" name="corregir_plataforma" 
                                               value="<?php echo htmlspecialchars($p->plataforma_oficial ?? $p->plataforma_nombre_nueva); ?>" 
                                               style="width: 100%; padding: 8px; border: 1px solid <?php echo $p->plataforma_oficial ? '#eee' : '#fde68a'; ?>; border-radius: 5px; background: <?php echo $p->plataforma_oficial ? 'white' : '#fffbeb'; ?>; font-weight: 700;">
                                    </div>
                                    <div style="margin-bottom: 10px;">
                                        <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;"><?php echo $lang['admin_validate_label_title']; ?></label>
                                        <input type="text" name="corregir_titulo" value="<?php echo htmlspecialchars($p->titulo); ?>" 
                                               style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px; font-weight: 700;">
                                    </div>
                                    <div style="display: flex; gap: 10px;">
                                        <div style="flex: 1;">
                                            <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;"><?php echo $lang['admin_validate_label_dev']; ?></label>
                                            <input type="text" name="corregir_dev" value="<?php echo htmlspecialchars($p->desarrollador); ?>" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px;">
                                        </div>
                                        <div style="flex: 1;">
                                            <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;"><?php echo $lang['admin_validate_label_region']; ?></label>
                                            <input type="text" name="corregir_region" value="<?php echo htmlspecialchars($p->region ?? ''); ?>" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px;">
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 20px;">
                                    <button type="submit" style="background: #1c1f26; color: white; border: none; padding: 10px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; width: 100%; cursor: pointer; margin-bottom: 8px;"><?php echo $lang['admin_validate_btn_approve']; ?></button>
                                    <a href="../../controllers/AdminController.php?action=rechazar&id=<?php echo $p->id; ?>" style="display:block; text-align:center; color: #e74c3c; font-size: 0.7rem; font-weight: 700; text-decoration: none;"><?php echo $lang['admin_validate_btn_reject']; ?></a>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>