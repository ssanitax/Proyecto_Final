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
        <h2>Validaciones con Corrección</h2>
        <p style="color: #666;">Revisa y edita los datos antes de hacerlos oficiales.</p>
    </header>

    <div class="about-box" style="padding: 0; overflow: hidden; background: white; border-radius: 20px; border: 1px solid #eee;">
        <?php if (empty($pendientes)): ?>
            <div style="padding: 80px; text-align: center;">
                <span style="font-size: 3rem; display: block; margin-bottom: 20px;">✅</span>
                <p style="color: #999; font-style: italic;">No hay propuestas en espera.</p>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #fafafa; border-bottom: 2px solid #f0f0f0;">
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;">Usuario</th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;">Información a Validar</th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;">Acción</th>
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
                                        <div style="margin-top:8px; background:#e0f2fe; color:#0369a1; padding:4px 8px; border-radius:4px; font-size:0.6rem; font-weight:800; display:inline-block;">JUEGO EXISTENTE</div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 20px;">
                                    <div style="margin-bottom: 10px;">
                                        <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;">PLATAFORMA</label>
                                        <input type="text" name="corregir_plataforma" 
                                               value="<?php echo htmlspecialchars($p->plataforma_oficial ?? $p->plataforma_nombre_nueva); ?>" 
                                               style="width: 100%; padding: 8px; border: 1px solid <?php echo $p->plataforma_oficial ? '#eee' : '#fde68a'; ?>; border-radius: 5px; background: <?php echo $p->plataforma_oficial ? 'white' : '#fffbeb'; ?>; font-weight: 700;">
                                    </div>
                                    <div style="margin-bottom: 10px;">
                                        <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;">TÍTULO</label>
                                        <input type="text" name="corregir_titulo" value="<?php echo htmlspecialchars($p->titulo); ?>" 
                                               style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px; font-weight: 700;">
                                    </div>
                                    <div style="display: flex; gap: 10px;">
                                        <div style="flex: 1;">
                                            <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;">DEV</label>
                                            <input type="text" name="corregir_dev" value="<?php echo htmlspecialchars($p->desarrollador); ?>" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px;">
                                        </div>
                                        <div style="flex: 1;">
                                            <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;">REGIÓN</label>
                                            <input type="text" name="corregir_region" value="<?php echo htmlspecialchars($p->region ?? ''); ?>" style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px;">
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 20px;">
                                    <button type="submit" style="background: #1c1f26; color: white; border: none; padding: 10px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; width: 100%; cursor: pointer; margin-bottom: 8px;">APROBAR</button>
                                    <a href="../../controllers/AdminController.php?action=rechazar&id=<?php echo $p->id; ?>" style="display:block; text-align:center; color: #e74c3c; font-size: 0.7rem; font-weight: 700; text-decoration: none;">RECHAZAR</a>
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