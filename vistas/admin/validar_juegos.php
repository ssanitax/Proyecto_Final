<?php
require_once '../../includes/auth.php';
// Seguridad: Solo el administrador de Bengala puede entrar aquí [cite: 185, 816]
redirigirSiNoAdmin(); 

require_once '../../config/config.php';

// Consulta unificada: Traemos datos del usuario, de la propuesta y detectamos si la plataforma es oficial o nueva [cite: 700, 1177]
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
        <p style="color: #666;">Revisa, corrige y aprueba las nuevas entradas al catálogo maestro.</p>
    </header>

    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'aprobado'): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                ¡Propuesta aprobada e insertada en el catálogo oficial! ✅
            </div>
        <?php elseif ($_GET['status'] == 'rechazado'): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: 600;">
                La propuesta ha sido rechazada y eliminada de la cola. ❌
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="about-box" style="padding: 0; overflow: hidden; background: white; border-radius: 20px; border: 1px solid #eee;">
        <?php if (empty($pendientes)): ?>
            <div style="padding: 80px; text-align: center;">
                <span style="font-size: 3rem; display: block; margin-bottom: 20px;">✅</span>
                <p style="color: #999; font-style: italic;">Todo en orden. No hay propuestas pendientes.</p>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #fafafa; border-bottom: 2px solid #f0f0f0;">
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;">Usuario</th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;">Datos a Corregir / Aprobar</th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase;">Acciones</th>
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
                                        <div style="margin-top:10px; background:#e0f2fe; color:#0369a1; padding:5px 10px; border-radius:5px; font-size:0.65rem; font-weight:800;">
                                            JUEGO YA EXISTENTE
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 20px;">
                                    <div style="margin-bottom: 15px;">
                                        <label style="font-size: 0.6rem; font-weight: 800; color: #aaa; display:block;">PLATAFORMA / SISTEMA</label>
                                        <input type="text" name="corregir_plataforma" 
                                               value="<?php echo htmlspecialchars($p->plataforma_oficial ?? $p->plataforma_nombre_nueva); ?>" 
                                               style="width: 100%; padding: 8px; border: 1px solid <?php echo $p->plataforma_oficial ? '#eee' : '#fde68a'; ?>; border-radius: 5px; background: <?php echo $p->plataforma_oficial ? 'white' : '#fffbeb'; ?>; font-weight: 700;">
                                        <?php if(!$p->plataforma_oficial && $p->plataforma_nombre_nueva): ?>
                                            <small style="color:#b45309; font-size:0.65rem; font-weight:600;">⚠️ Esta plataforma se creará de cero.</small>
                                        <?php endif; ?>
                                    </div>

                                    <div style="margin-bottom: 15px;">
                                        <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;">TÍTULO / PROPUESTA</label>
                                        <input type="text" name="corregir_titulo" value="<?php echo htmlspecialchars($p->titulo); ?>" 
                                               style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px; font-weight: 700;">
                                    </div>
                                    
                                    <div style="display: flex; gap: 10px;">
                                        <div style="flex: 1;">
                                            <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;">DESARROLLADOR</label>
                                            <input type="text" name="corregir_dev" value="<?php echo htmlspecialchars($p->desarrollador); ?>" 
                                                   style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px; font-size: 0.8rem;">
                                        </div>
                                        <div style="flex: 1;">
                                            <label style="font-size: 0.6rem; font-weight: 800; color: #aaa;">REGIÓN</label>
                                            <input type="text" name="corregir_region" value="<?php echo htmlspecialchars($p->region ?? ''); ?>" 
                                                   style="width: 100%; padding: 8px; border: 1px solid #eee; border-radius: 5px; font-size: 0.8rem;">
                                        </div>
                                    </div>
                                </td>
                                <td style="padding: 20px;">
                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                        <button type="submit" style="background: #1c1f26; color: white; border: none; padding: 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; cursor: pointer; transition: 0.3s;"
                                                onmouseover="this.style.background='#333';" onmouseout="this.style.background='#1c1f26';">
                                            APROBAR CON CAMBIOS
                                        </button>
                                        <a href="../../controllers/AdminController.php?action=rechazar&id=<?php echo $p->id; ?>" 
                                           style="background: #fee2e2; color: #991b1b; text-align: center; padding: 10px; border-radius: 50px; text-decoration: none; font-size: 0.7rem; font-weight: 700;"
                                           onclick="return confirm('¿Seguro que quieres rechazar esta propuesta?')">
                                            RECHAZAR
                                        </a>
                                    </div>
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