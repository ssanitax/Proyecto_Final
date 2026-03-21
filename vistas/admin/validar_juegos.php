<?php
require_once '../../includes/auth.php';
// Seguridad: Solo el administrador de Bengala puede entrar aquí
if (!esAdmin()) {
    header('Location: ../fronted/dashboard.php');
    exit();
}

require_once '../../config/config.php';

// Consulta para obtener las propuestas pendientes de los usuarios
$sql = "SELECT jp.*, u.nombre as nombre_usuario, ep.region, p.nombre as plataforma_nombre 
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
        <h2>Panel de Control: Validaciones</h2>
        <p style="color: #666;">Revisa y aprueba las nuevas entradas al catálogo maestro.</p>
    </header>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'aprobado'): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 600;">
            ¡Juego aprobado e insertado en el catálogo oficial!
        </div>
    <?php endif; ?>

    <div class="about-box" style="padding: 0; overflow: hidden; background: white; border-radius: 20px; border: 1px solid #eee;">
        <?php if (empty($pendientes)): ?>
            <div style="padding: 80px; text-align: center;">
                <span style="font-size: 3rem; display: block; margin-bottom: 20px;">✅</span>
                <p style="color: #999; font-style: italic;">No hay propuestas pendientes de revisión.</p>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #fafafa; border-bottom: 2px solid #f0f0f0;">
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Propuesto por</th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Título y Plataforma</th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendientes as $p): ?>
                        <tr style="border-bottom: 1px solid #f5f5f5;">
                            <td style="padding: 20px;">
                                <strong style="color: var(--graphite);"><?php echo htmlspecialchars($p->nombre_usuario); ?></strong><br>
                                <small style="color: #999;"><?php echo date('d/m/Y H:i', strtotime($p->created_at)); ?></small>
                            </td>
                            <td style="padding: 20px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="background: #eee; padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800;">
                                        <?php echo htmlspecialchars($p->plataforma_nombre ?? 'N/A'); ?>
                                    </span>
                                    <strong style="font-size: 1rem;"><?php echo htmlspecialchars($p->titulo); ?></strong>
                                </div>
                                <div style="font-size: 0.8rem; color: #777; margin-top: 5px;">
                                    Desarrollador: <?php echo htmlspecialchars($p->desarrollador); ?> | Región: <?php echo htmlspecialchars($p->region ?? 'N/A'); ?>
                                </div>
                            </td>
                            <td style="padding: 20px;">
                                <a href="../../controllers/AdminController.php?action=aprobar&id=<?php echo $p->id; ?>" 
                                   style="background: var(--graphite); color: white; padding: 8px 20px; border-radius: 50px; text-decoration: none; font-size: 0.8rem; font-weight: 700; transition: 0.3s;"
                                   onclick="return confirm('¿Confirmas que los datos son correctos para el catálogo oficial?')">
                                    APROBAR
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>