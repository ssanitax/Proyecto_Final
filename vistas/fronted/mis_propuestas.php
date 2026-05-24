<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/JuegoPendiente.php';

$modeloPendiente = new JuegoPendiente($pdo);
$misPropuestas = $modeloPendiente->obtenerPropuestasPorUsuario($_SESSION['usuario_id']);

include '../../includes/header.php';
?>

<div class="fade-up visible">
    <header style="margin-bottom: 40px; text-align: center;">
        <h2 style="margin-bottom: 10px;"><?php echo $lang['frontend_proposals_title']; ?></h2>
        <p style="color: #666; font-size: 1.1rem;"><?php echo $lang['frontend_proposals_desc']; ?></p>
    </header>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-bottom: 60px;">
        
        <a href="registrar_nuevo.php" class="dash-card" style="padding: 30px; text-decoration: none;">
            <div style="font-size: 2.5rem; margin-bottom: 15px;">🆕</div>
            <h4 style="margin-bottom: 8px; color: var(--graphite); font-size: 1.2rem;"><?php echo $lang['frontend_proposals_new_game_title']; ?></h4>
            <p style="font-size: 0.85rem; color: #777; min-height: 40px;"><?php echo $lang['frontend_proposals_new_game_desc']; ?></p>
            
            <span style="display: block; width: 100%; padding: 12px; border-radius: 50px; 
                         background: var(--graphite); color: white; font-weight: 700; 
                         text-transform: uppercase; font-size: 0.75rem; text-align: center; 
                         margin-top: 20px; transition: 0.3s;"
                  onmouseover="this.style.background='#333'; this.style.transform='translateY(-2px)';"
                  onmouseout="this.style.background='var(--graphite)'; this.style.transform='translateY(0)';"
            >
                <?php echo $lang['frontend_proposals_new_game_button']; ?>
            </span>
        </a>

        <a href="proponer_plataforma.php" class="dash-card" style="padding: 30px; text-decoration: none;">
            <div style="font-size: 2.5rem; margin-bottom: 15px;">🎮</div>
            <h4 style="margin-bottom: 8px; color: var(--graphite); font-size: 1.2rem;"><?php echo $lang['frontend_proposals_new_platform_title']; ?></h4>
            <p style="font-size: 0.85rem; color: #777; min-height: 40px;"><?php echo $lang['frontend_proposals_new_platform_desc']; ?></p>
            
            <span style="display: block; width: 100%; padding: 12px; border-radius: 50px; 
                        background: var(--graphite); color: white; font-weight: 700; 
                        text-transform: uppercase; font-size: 0.75rem; text-align: center; 
                        margin-top: 20px; transition: 0.3s;"
                onmouseover="this.style.background='#333'; this.style.transform='translateY(-2px)';"
                onmouseout="this.style.background='var(--graphite)'; this.style.transform='translateY(0)';"
            >
                <?php echo $lang['frontend_proposals_new_platform_button']; ?>
            </span>
        </a>

        <a href="proponer_region.php" class="dash-card" style="padding: 30px; text-decoration: none;">
            <div style="font-size: 2.5rem; margin-bottom: 15px;">🌍</div>
            <h4 style="margin-bottom: 8px; color: var(--graphite); font-size: 1.2rem;"><?php echo $lang['frontend_proposals_new_region_title']; ?></h4>
            <p style="font-size: 0.85rem; color: #777; min-height: 40px;"><?php echo $lang['frontend_proposals_new_region_desc']; ?></p>
            
            <span style="display: block; width: 100%; padding: 12px; border-radius: 50px; 
                        background: var(--graphite); color: white; font-weight: 700; 
                        text-transform: uppercase; font-size: 0.75rem; text-align: center; 
                        margin-top: 20px; transition: 0.3s;"
                onmouseover="this.style.background='#333'; this.style.transform='translateY(-2px)';"
                onmouseout="this.style.background='var(--graphite)'; this.style.transform='translateY(0)';"
            >
                <?php echo $lang['frontend_proposals_new_region_button']; ?>
            </span>
        </a>
    </div>

    <div class="about-box" style="padding: 0; overflow: hidden; background: white; border-radius: 20px; border: 1px solid #eee;">
        <h3 style="padding: 20px; border-bottom: 1px solid #f0f0f0; font-size: 1rem; text-align: left;"><?php echo $lang['frontend_proposals_history_title']; ?></h3>
        
        <?php if (empty($misPropuestas)): ?>
            <div style="padding: 60px; text-align: center;">
                <p style="color: #999; font-style: italic;"><?php echo $lang['frontend_proposals_empty']; ?></p>
            </div>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #fafafa; border-bottom: 2px solid #f0f0f0;">
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; color: #888;"><?php echo $lang['frontend_proposals_table_header']; ?></th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; color: #888;"><?php echo $lang['frontend_proposals_table_status']; ?></th>
                        <th style="padding: 20px; font-size: 0.75rem; text-transform: uppercase; color: #888;"><?php echo $lang['frontend_proposals_table_date']; ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($misPropuestas as $p): ?>
                        <tr style="border-bottom: 1px solid #f5f5f5;">
                            <td style="padding: 20px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span style="background: #eee; color: var(--graphite); padding: 4px 8px; border-radius: 4px; font-size: 0.65rem; font-weight: 800;">
                                        <?php echo htmlspecialchars($p->plataforma_nombre ?? 'N/A'); ?>
                                    </span>
                                    <strong><?php echo htmlspecialchars($p->titulo); ?></strong>
                                </div>
                                <div style="font-size: 0.75rem; color: #888; margin-top: 4px;">
                                    <?php if (!empty($p->region)): ?>
                                        <?php echo $lang['frontend_proposals_region_label']; ?> <?php echo htmlspecialchars($p->region); ?>
                                    <?php elseif (!empty($p->bloqueo_regional)): ?>
                                        <?php echo $lang['frontend_proposals_regional_lock_label']; ?> <?php echo $lang['frontend_proposals_regional_lock_yes']; ?>
                                    <?php elseif (isset($p->bloqueo_regional)): ?>
                                        <?php echo $lang['frontend_proposals_regional_lock_label']; ?> <?php echo $lang['frontend_proposals_regional_lock_no']; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td style="padding: 20px;">
                                <?php if ($p->estado == 'pendiente'): ?>
                                    <span style="color: #92400e; font-weight: 700; font-size: 0.75rem;"><?php echo $lang['frontend_proposals_status_reviewing']; ?></span>
                                <?php elseif ($p->estado == 'aprobado'): ?>
                                    <span style="color: #065f46; font-weight: 700; font-size: 0.75rem;"><?php echo $lang['frontend_proposals_status_approved']; ?></span>
                                <?php else: ?>
                                    <span style="color: #991b1b; font-weight: 700; font-size: 0.75rem;"><?php echo $lang['frontend_proposals_status_rejected']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: 20px; font-size: 0.85rem; color: #999;">
                                <?php echo date('d/m/Y', strtotime($p->created_at)); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>