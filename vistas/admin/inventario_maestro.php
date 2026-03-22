<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin();
require_once '../../config/config.php';

// 1. Obtener todas las plataformas
$plataformas = $pdo->query("SELECT * FROM plataformas ORDER BY nombre ASC")->fetchAll();

// 2. Obtener regiones distintas usadas actualmente (ya que no tienen tabla propia)
$regiones = $pdo->query("SELECT DISTINCT region FROM ediciones WHERE region IS NOT NULL AND region != '' ORDER BY region ASC")->fetchAll();

// 3. Obtener todas las ediciones (Juego + Plataforma + Región)
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
        <h2>Inventario Maestro del Sistema</h2>
        <p style="color: #666;">Gestiona y limpia los datos oficiales de la base de datos.</p>
    </header>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center; font-weight: 600;">
            Elemento eliminado correctamente del catálogo oficial.
        </div>
    <?php endif; ?>

    <!-- SECCIÓN 1: PLATAFORMAS -->
    <div class="admin-section">
        <div class="section-header">
            <h3>Consolas y Plataformas <span class="badge-count"><?php echo count($plataformas); ?></span></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nombre del Sistema</th>
                    <th style="text-align: right;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($plataformas as $plat): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($plat->nombre); ?></strong></td>
                        <td style="text-align: right;">
                            <a href="../../controllers/AdminController.php?action=eliminar_plataforma&id=<?php echo $plat->id; ?>" 
                               class="btn-delete" onclick="return confirm('ATENCIÓN: Borrar una plataforma eliminará todos los juegos asociados a ella. ¿Continuar?')">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- SECCIÓN 2: REGIONES -->
    <div class="admin-section">
        <div class="section-header">
            <h3>Regiones Activas en el Sistema <span class="badge-count"><?php echo count($regiones); ?></span></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Nombre de la Región</th>
                    <th style="text-align: right;">Acción</th>
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
                                <strong>🌍 <?php echo htmlspecialchars($reg->region); ?></strong>
                            </td>
                            <td style="text-align: right;">
                                <a href="../../controllers/AdminController.php?action=eliminar_region&nombre=<?php echo urlencode($reg->region); ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('¿Seguro que quieres eliminar la región <?php echo htmlspecialchars($reg->region); ?>? Esto borrará todos los juegos asociados.')">
                                   Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- SECCIÓN 3: EDICIONES (Vínculo Juego-Consola) -->
    <div class="admin-section">
        <div class="section-header">
            <h3>Catálogo de Ediciones <span class="badge-count"><?php echo count($ediciones); ?></span></h3>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Videojuego</th>
                    <th>Plataforma</th>
                    <th>Región</th>
                    <th>Detalle Edición</th>
                    <th style="text-align: right;">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($ediciones as $e): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($e->juego_titulo); ?></strong></td>
                        <td><span style="background: #eee; padding: 3px 8px; border-radius: 4px; font-size: 0.7rem; font-weight: 800;"><?php echo htmlspecialchars($e->plataforma_nombre); ?></span></td>
                        <td><?php echo htmlspecialchars($e->region); ?></td>
                        <td style="color: #777; font-size: 0.8rem;"><?php echo htmlspecialchars($e->edicion_nombre); ?></td>
                        <td style="text-align: right;">
                            <a href="../../controllers/AdminController.php?action=eliminar_edicion&id=<?php echo $e->id; ?>" 
                               class="btn-delete" onclick="return confirm('¡ADVERTENCIA CRÍTICA! Borrar esta plataforma eliminará PERMANENTEMENTE todos los juegos (ediciones) asociados a ella en el catálogo y en las bibliotecas de los usuarios. ¿Estás seguro?')">Borrar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>