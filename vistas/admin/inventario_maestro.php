<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin();
$pdo = $GLOBALS['pdo'];

// 1. Obtener todas las plataformas
$plataformas = $pdo->query("SELECT * FROM plataformas ORDER BY nombre ASC")->fetchAll();

// 2. Catálogo maestro de regiones (fallback a ediciones si la tabla aún no existe)
$regiones = [];
try {
    $regiones = $pdo->query("SELECT id, nombre FROM regiones ORDER BY nombre ASC")->fetchAll();
} catch (PDOException $e) {
    $legacy = $pdo->query("SELECT DISTINCT region AS nombre FROM ediciones WHERE region IS NOT NULL AND region != '' ORDER BY region ASC")->fetchAll();
    foreach ($legacy as $row) {
        $row->id = null;
        $regiones[] = $row;
    }
}

// 2b. Idiomas del catálogo
$idiomas = [];
try {
    $idiomas = $pdo->query("SELECT * FROM idiomas ORDER BY nombre ASC")->fetchAll();
} catch (PDOException $e) {
    $idiomas = [];
}

// 3. Juegos maestro (agrupados, para borrado completo del título)
$juegosCatalogo = $pdo->query("
    SELECT j.id, j.titulo, j.desarrollador, COUNT(e.id) AS num_ediciones
    FROM juegos j
    LEFT JOIN ediciones e ON e.juego_id = j.id
    GROUP BY j.id, j.titulo, j.desarrollador
    ORDER BY j.titulo ASC
")->fetchAll();

// 4. Obtener todas las ediciones (Juego + Plataforma + Región)
$ediciones = $pdo->query("
    SELECT e.*, j.titulo as juego_titulo, p.nombre as plataforma_nombre 
    FROM ediciones e
    JOIN juegos j ON e.juego_id = j.id
    JOIN plataformas p ON e.plataforma_id = p.id
    ORDER BY j.titulo ASC
")->fetchAll();

include '../../includes/admin_header.php';
?>

<style>
    .admin-section { background: white; border-radius: 20px; border: 1px solid #eee; margin-bottom: 40px; overflow: hidden; }
    .section-header { background: #fafafa; padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .section-header h3 { font-size: 1rem; font-weight: 800; text-transform: uppercase; color: var(--graphite); margin: 0; }
    
    table { width: 100%; border-collapse: collapse; text-align: left; }
    th { padding: 15px 20px; font-size: 0.7rem; text-transform: uppercase; color: #aaa; letter-spacing: 1px; border-bottom: 2px solid #f9f9f9; }
    td { padding: 15px 20px; font-size: 0.9rem; border-bottom: 1px solid #f9f9f9; vertical-align: middle; }
    
    .btn-delete { color: #e74c3c; text-decoration: none; font-weight: 700; font-size: 0.75rem; border: 1px solid #fee2e2; padding: 5px 12px; border-radius: 6px; transition: 0.3s; }
    .btn-delete:hover { background: #e74c3c; color: white; }
    
    .badge-count { background: var(--graphite); color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; margin-left: 10px; }
</style>

<div class="fade-up visible">
    <header style="margin-bottom: 40px;">
        <h2><?php echo $lang['admin_inventory_title']; ?></h2>
        <p style="color: #666;"><?php echo $lang['admin_inventory_desc']; ?></p>
    </header>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'delete_game'): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; font-weight: 600;">
            <?php echo $lang['error_delete_game']; ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; font-weight: 600;">
            <?php echo $lang['elemento_eliminado']; ?>
        </div>
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'covers_cleaned'): ?>
        <div style="background: #dbeafe; color: #1d4ed8; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; font-weight: 600;">
            <?php echo sprintf($lang['admin_covers_cleaned'], (int)($_GET['n'] ?? 0)); ?>
        </div>
    <?php endif; ?>
    <?php if (esSuperAdmin()): ?>
        <p style="margin-bottom: 24px; text-align: center; display: flex; flex-wrap: wrap; gap: 16px; justify-content: center;">
            <a href="../../controllers/AdminController.php?action=limpiar_juegos_sin_ediciones"
               style="font-size: 0.85rem; font-weight: 700; color: #666;"
               onclick="return confirm('<?php echo htmlspecialchars($lang['admin_orphan_games_clean_confirm'], ENT_QUOTES); ?>');">
                <?php echo $lang['admin_orphan_games_clean_link']; ?>
            </a>
            <a href="../../controllers/AdminController.php?action=limpiar_portadas_huerfanas"
               style="font-size: 0.85rem; font-weight: 700; color: #666;"
               onclick="return confirm('<?php echo htmlspecialchars($lang['admin_covers_clean_confirm'], ENT_QUOTES); ?>');">
                <?php echo $lang['admin_covers_clean_link']; ?>
            </a>
        </p>
    <?php endif; ?>
    <?php if(isset($_GET['status']) && $_GET['status'] == 'orphan_games_cleaned'): ?>
        <div style="background: #dbeafe; color: #1d4ed8; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; font-weight: 600;">
            <?php echo sprintf($lang['admin_orphan_games_cleaned'], (int)($_GET['n'] ?? 0)); ?>
        </div>
    <?php endif; ?>

    <!-- SECCIÓN 1: PLATAFORMAS -->
    <div class="admin-section">
        <div class="section-header">
            <h3><?php echo $lang['admin_section_platforms']; ?> <span class="badge-count"><?php echo count($plataformas); ?></span></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th><?php echo $lang['admin_table_name']; ?></th>
                    <th style="text-align: right;"><?php echo $lang['admin_table_action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($plataformas as $plat): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($plat->nombre); ?></strong></td>
                        <td style="text-align: right;">
                            <a href="../../controllers/AdminController.php?action=eliminar_plataforma&id=<?php echo $plat->id; ?>" 
                               class="btn-delete" onclick="return confirm('<?php echo $lang['admin_confirm_delete_platform']; ?>')"><?php echo $lang['admin_user_delete']; ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- SECCIÓN 2: REGIONES -->
    <div class="admin-section">
        <div class="section-header">
            <h3><?php echo $lang['admin_section_regions']; ?> <span class="badge-count"><?php echo count($regiones); ?></span></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th><?php echo $lang['admin_table_region']; ?></th>
                    <th style="text-align: right;"><?php echo $lang['admin_table_action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($regiones)): ?>
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999; padding: 40px;">
                            No hay regiones registradas en el sistema.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($regiones as $reg): ?>
                        <tr>
                            <td>
                                <strong>🌍 <?php echo htmlspecialchars($reg->nombre); ?></strong>
                            </td>
                            <td style="text-align: right;">
                                <a href="../../controllers/AdminController.php?action=eliminar_region&nombre=<?php echo urlencode($reg->nombre); ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('<?php echo sprintf($lang['admin_confirm_delete_region'], htmlspecialchars($reg->nombre)); ?>')">
                                   <?php echo $lang['admin_user_delete']; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- SECCIÓN 3: IDIOMAS -->
    <div class="admin-section">
        <div class="section-header">
            <h3><?php echo $lang['admin_section_languages']; ?> <span class="badge-count"><?php echo count($idiomas); ?></span></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th><?php echo $lang['admin_table_language']; ?></th>
                    <th style="text-align: right;"><?php echo $lang['admin_table_action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($idiomas)): ?>
                    <tr>
                        <td colspan="2" style="text-align: center; color: #999; padding: 40px;">
                            <?php echo ($idiomaActual ?? 'es') === 'en' ? 'No languages registered yet.' : 'No hay idiomas registrados todavía.'; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($idiomas as $idioma): ?>
                        <tr>
                            <td><strong>🗣️ <?php echo htmlspecialchars($idioma->nombre); ?></strong></td>
                            <td style="text-align: right;">
                                <a href="../../controllers/AdminController.php?action=eliminar_idioma&id=<?php echo (int)$idioma->id; ?>"
                                   class="btn-delete"
                                   onclick="return confirm('<?php echo sprintf($lang['admin_confirm_delete_language'], htmlspecialchars($idioma->nombre)); ?>')">
                                   <?php echo $lang['admin_user_delete']; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- SECCIÓN 4: JUEGOS (borrado del título completo) -->
    <div class="admin-section">
        <div class="section-header">
            <h3><?php echo $lang['admin_section_games']; ?> <span class="badge-count"><?php echo count($juegosCatalogo); ?></span></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th><?php echo $lang['admin_table_game']; ?></th>
                    <th><?php echo $lang['admin_table_editions_count']; ?></th>
                    <th style="text-align: right;"><?php echo $lang['admin_table_action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($juegosCatalogo)): ?>
                    <tr>
                        <td colspan="3" style="text-align: center; color: #999; padding: 40px;">
                            <?php echo ($idiomaActual ?? 'es') === 'en' ? 'No games in the catalog.' : 'No hay juegos en el catálogo.'; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($juegosCatalogo as $juego): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($juego->titulo); ?></strong>
                                <?php if (!empty($juego->desarrollador)): ?>
                                    <br><span style="color: #888; font-size: 0.8rem;"><?php echo htmlspecialchars($juego->desarrollador); ?></span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo (int)$juego->num_ediciones; ?></td>
                            <td style="text-align: right;">
                                <a href="../../controllers/AdminController.php?action=eliminar_juego&id=<?php echo (int)$juego->id; ?>"
                                   class="btn-delete"
                                   onclick="return confirm('<?php echo htmlspecialchars(sprintf($lang['admin_confirm_delete_game'], $juego->titulo), ENT_QUOTES); ?>');">
                                    <?php echo $lang['admin_delete_game_full']; ?>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- SECCIÓN 5: EDICIONES (solo una consola/región) -->
    <div class="admin-section">
        <div class="section-header">
            <h3><?php echo $lang['admin_section_editions']; ?> <span class="badge-count"><?php echo count($ediciones); ?></span></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th><?php echo $lang['admin_table_game']; ?></th>
                    <th>Plataforma</th>
                    <th>Región</th>
                    <th>Detalle Edición</th>
                    <th style="text-align: right;"><?php echo $lang['admin_table_action']; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ediciones)): ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #999; padding: 40px;">
                            <?php echo ($idiomaActual ?? 'es') === 'en' ? 'No editions registered.' : 'No hay ediciones registradas.'; ?>
                        </td>
                    </tr>
                <?php else: ?>
                <?php foreach($ediciones as $e): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($e->juego_titulo); ?></strong></td>
                        <td><span style="background: #eee; padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800;"><?php echo htmlspecialchars($e->plataforma_nombre); ?></span></td>
                        <td><?php echo htmlspecialchars($e->region); ?></td>
                        <td style="color: #777; font-size: 0.8rem;"><?php echo htmlspecialchars($e->edicion_nombre); ?></td>
                        <td style="text-align: right;">
                            <a href="../../controllers/AdminController.php?action=eliminar_edicion&id=<?php echo $e->id; ?>" 
                               class="btn-delete" onclick="return confirm('<?php echo htmlspecialchars($lang['admin_confirm_delete_edition'], ENT_QUOTES); ?>')"><?php echo $lang['admin_delete_edition_only']; ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>