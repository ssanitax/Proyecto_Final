<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/JuegoPendiente.php';

// Iniciamos el modelo y traemos las propuestas del usuario actual
$modeloPendiente = new JuegoPendiente($pdo);
$misPropuestas = $modeloPendiente->obtenerPropuestasPorUsuario($_SESSION['usuario_id']);

include '../../includes/header.php';
?>

<div class="fade-up visible">
    <header style="margin-bottom: 40px; text-align: center;">
        <h2 style="margin-bottom: 10px;">Mis Propuestas al Catálogo</h2>
        <p style="color: #666; font-size: 1.1rem;">Consulta el estado de los juegos que has sugerido para la comunidad.</p>
    </header>

    <div class="about-box" style="padding: 0; overflow: hidden; background: white; border-radius: 20px; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <?php if (empty($misPropuestas)): ?>
            <div style="text-align: center; padding: 100px 0;">
                <span style="font-size: 4rem; display: block; margin-bottom: 20px;">📩</span>
                <p style="color: #999; margin-bottom: 20px; font-style: italic;">No has enviado ninguna propuesta todavía.</p>
                <a href="registrar_nuevo.php" class="btn-dash" style="display: inline-block; text-decoration: none; background: var(--graphite); color: white; padding: 12px 30px; border-radius: 50px; font-weight: 600;">
                    + Sugerir un juego
                </a>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #fafafa; border-bottom: 2px solid #f0f0f0;">
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #888;">Videojuego y Sistema</th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #888;">Estado de Revisión</th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; color: #888;">Fecha de Envío</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($misPropuestas as $p): ?>
                        <tr style="border-bottom: 1px solid #f5f5f5;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='white'">
                            <td style="padding: 20px;">
                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 5px;">
                                    <span style="background: #eee; color: var(--graphite); padding: 4px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase;">
                                        <?php echo htmlspecialchars($p->plataforma_nombre ?? 'N/A'); ?>
                                    </span>
                                    <strong style="font-size: 1.05rem; color: var(--graphite);"><?php echo htmlspecialchars($p->titulo); ?></strong>
                                </div>
                                <div style="font-size: 0.8rem; color: #777;">
                                    Edición: <?php echo htmlspecialchars($p->edicion_nombre ?? 'Estándar'); ?> | Región: <?php echo htmlspecialchars($p->region ?? 'N/A'); ?>
                                </div>
                            </td>
                            <td style="padding: 20px;">
                                <?php if ($p->estado == 'pendiente'): ?>
                                    <span style="color: #92400e; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; gap: 6px;">
                                        <span style="height: 8px; width: 8px; background: #f1c40f; border-radius: 50%;"></span> EN REVISIÓN
                                    </span>
                                <?php elseif ($p->estado == 'aprobado'): ?>
                                    <span style="color: #065f46; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; gap: 6px;">
                                        <span style="height: 8px; width: 8px; background: #2ecc71; border-radius: 50%;"></span> ACEPTADO ✅
                                    </span>
                                <?php else: ?>
                                    <span style="color: #991b1b; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; gap: 6px;">
                                        <span style="height: 8px; width: 8px; background: #e74c3c; border-radius: 50%;"></span> RECHAZADO ❌
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 20px; font-size: 0.9rem; color: #666;">
                                <?php echo date('d/m/Y', strtotime($p->created_at));?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>