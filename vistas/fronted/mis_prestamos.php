<?php
require_once '../../includes/auth.php';
redirigirSiNoLogueado();
require_once '../../config/config.php';
require_once '../../models/Prestamo.php';

$prestamoModel = new Prestamo($pdo);
$prestamos = $prestamoModel->obtenerPrestamosUsuario($_SESSION['usuario_id']);

include '../../includes/header.php';
?>

<div class="fade-up visible">
    <h2>Control de Préstamos</h2>
    <p style="text-align:center; color:#666; margin-bottom:40px;">Gestiona los juegos que has prestado a otras personas.</p>

    <div class="about-box" style="max-width: 900px; margin: 0 auto;">
        <?php if (empty($prestamos)): ?>
            <p>No tienes ningún préstamo activo ahora mismo.</p>
        <?php else: ?>
            <table style="width:100%; border-collapse: collapse; text-align:left;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--graphite);">
                        <th style="padding:15px;">Juego</th>
                        <th>Prestado a</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prestamos as $p): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding:15px;"><strong><?php echo $p->titulo; ?></strong> (<?php echo $p->edicion_nombre; ?>)</td>
                        <td><?php echo $p->nombre_persona; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($p->fecha_prestamo)); ?></td>
                        <td>
                            <span class="badge <?php echo $p->devuelto ? 'status-completado' : 'status-pendiente'; ?>">
                                <?php echo $p->devuelto ? 'Devuelto' : 'En préstamo'; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
