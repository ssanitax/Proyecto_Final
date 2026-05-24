<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$usuario_id = $_SESSION['usuario_id'];

// 1. Obtener parámetros de búsqueda y filtro
$busqueda = $_GET['search'] ?? '';
$plataforma_filtro = $_GET['plataforma'] ?? '';

// 2. Obtener plataformas que el usuario tiene en su colección para el select
$stmtPlats = $pdo->prepare("
    SELECT DISTINCT p.id, p.nombre 
    FROM plataformas p
    JOIN ediciones e ON p.id = e.plataforma_id
    JOIN coleccion_usuario cu ON e.id = cu.edicion_id
    WHERE cu.usuario_id = ?
    ORDER BY p.nombre ASC
");
$stmtPlats->execute([$usuario_id]);
$plataformas_usuario = $stmtPlats->fetchAll();

// 3. Una tarjeta por juego (agrupado); el detalle de cada copia está en coleccion_juego.php
$sql = "SELECT 
            e.juego_id,
            j.titulo,
            COUNT(cu.id) AS num_copias,
            COUNT(DISTINCT p.id) AS num_plataformas,
            MAX(p.nombre) AS plataforma_ejemplo,
            MAX(cu.fecha_adicion) AS ultima_adicion,
            (
                SELECT e2.imagen_portada
                FROM coleccion_usuario cu2
                JOIN ediciones e2 ON e2.id = cu2.edicion_id
                WHERE cu2.usuario_id = cu.usuario_id AND e2.juego_id = e.juego_id
                  AND e2.imagen_portada IS NOT NULL AND e2.imagen_portada != ''
                ORDER BY cu2.fecha_adicion DESC, cu2.id DESC
                LIMIT 1
            ) AS imagen_portada,
            rating_user.valoracion_juego_usuario
        FROM coleccion_usuario cu
        JOIN ediciones e ON cu.edicion_id = e.id
        JOIN juegos j ON e.juego_id = j.id
        JOIN plataformas p ON e.plataforma_id = p.id
        LEFT JOIN (
            SELECT t.usuario_id, t.juego_id, cu3.valoracion_personal AS valoracion_juego_usuario
            FROM (
                SELECT cu2.usuario_id, e2.juego_id, MAX(cu2.id) AS ultimo_id
                FROM coleccion_usuario cu2
                JOIN ediciones e2 ON e2.id = cu2.edicion_id
                WHERE cu2.valoracion_personal IS NOT NULL
                GROUP BY cu2.usuario_id, e2.juego_id
            ) t
            JOIN coleccion_usuario cu3 ON cu3.id = t.ultimo_id
        ) rating_user ON rating_user.usuario_id = cu.usuario_id AND rating_user.juego_id = e.juego_id
        WHERE cu.usuario_id = ?";

$params = [$usuario_id];

if (!empty($busqueda)) {
    $sql .= " AND j.titulo LIKE ?";
    $params[] = "%$busqueda%";
}

if (!empty($plataforma_filtro)) {
    $sql .= " AND p.id = ?";
    $params[] = $plataforma_filtro;
}

$sql .= " GROUP BY e.juego_id, j.titulo, rating_user.valoracion_juego_usuario
          ORDER BY ultima_adicion DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$items = $stmt->fetchAll();

include '../../includes/header.php';
?>

<style>
    .user-rating-badge {
        margin-top: 10px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 10px;
        border-radius: 999px;
        background: #f4f5f7;
        border: 1px solid #e5e7eb;
    }

    .user-rating-label {
        font-size: 0.62rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #6b7280;
    }

    .user-rating-value {
        font-size: 0.82rem;
        font-weight: 800;
        color: var(--graphite);
    }

    .user-rating-empty {
        margin-top: 10px;
        display: inline-block;
        font-size: 0.74rem;
        color: #9ca3af;
        font-style: italic;
    }

    .copies-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: #6366f1;
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
    }
</style>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 40px;">
        <h2 style="margin-bottom: 10px;"><?php echo $lang['frontend_collection_title']; ?></h2>
        <p style="color: #777;"><?php echo $lang['frontend_collection_desc']; ?></p>
    </header>

    <section style="background: white; padding: 25px; border-radius: 20px; margin-bottom: 40px; border: 1px solid #eee; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <form method="GET" action="" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 250px;">
                <input type="text" name="search" placeholder="<?php echo $lang['frontend_collection_search_placeholder']; ?>" 
                       value="<?php echo htmlspecialchars($busqueda); ?>"
                       style="width: 100%; padding: 12px 20px; border-radius: 12px; border: 1px solid #eee; outline: none; font-family: inherit;">
            </div>
            
            <div style="min-width: 200px;">
                <select name="plataforma" style="width: 100%; padding: 12px; border-radius: 12px; border: 1px solid #eee; outline: none; font-family: inherit; background: white;">
                    <option value=""><?php echo $lang['frontend_collection_all_platforms']; ?></option>
                    <?php foreach($plataformas_usuario as $plat): ?>
                        <option value="<?php echo $plat->id; ?>" <?php echo ($plataforma_filtro == $plat->id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($plat->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" style="padding: 12px 30px; border-radius: 50px; border: none; background: var(--graphite); color: white; cursor: pointer; font-weight: 700; text-transform: uppercase; font-size: 0.8rem; transition: 0.3s;">
                <?php echo $lang['frontend_collection_filter_button']; ?>
            </button>
            
            <?php if(!empty($busqueda) || !empty($plataforma_filtro)): ?>
                <a href="mi_coleccion.php" style="color: #e74c3c; text-decoration: none; font-size: 0.85rem; font-weight: 700;"><?php echo $lang['frontend_collection_clear_filters']; ?></a>
            <?php endif; ?>
        </form>
    </section>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 30px;">
        <?php if (empty($items)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 80px 0;">
                <span style="font-size: 3rem; display: block; margin-bottom: 20px;">🔍</span>
                <p style="color: #999; font-style: italic;"><?php echo $lang['frontend_collection_empty']; ?></p>
            </div>
        <?php else: ?>
            <?php foreach($items as $item): ?>
                <?php
                    $plataformaLabel = ((int)$item->num_plataformas > 1)
                        ? $lang['frontend_collection_multiple_platforms']
                        : htmlspecialchars($item->plataforma_ejemplo ?? '');
                ?>
                <div class="game-card" style="background: white; border-radius: 15px; overflow: hidden; border: 1px solid #eee; transition: 0.3s; display: flex; flex-direction: column;">
                    
                    <div style="width: 100%; aspect-ratio: 3/4; background: #f8f9fa; display: flex; align-items: center; justify-content: center; position: relative;">
                        <?php if ((int)$item->num_copias > 1): ?>
                            <span class="copies-badge"><?php echo sprintf($lang['frontend_collection_copies_badge'], (int)$item->num_copias); ?></span>
                        <?php endif; ?>
                        <div style="position: absolute; top: 12px; right: 12px; background: var(--graphite); color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase;">
                            <?php echo $plataformaLabel; ?>
                        </div>
                        
                        <?php if(!empty($item->imagen_portada)): ?>
                            <img src="../../img/portadas/<?php echo htmlspecialchars($item->imagen_portada); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        <?php else: ?>
                            <span style="font-size: 4rem;">🎮</span>
                        <?php endif; ?>
                    </div>

                    <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div style="text-align: left; margin-bottom: 15px;">
                            <h3 style="font-size: 1.1rem; margin: 0 0 8px 0; font-weight: 800; color: var(--graphite); line-height: 1.2;">
                                <?php echo htmlspecialchars($item->titulo); ?>
                            </h3>
                            <?php if ((int)$item->num_copias === 1): ?>
                                <p style="font-size: 0.8rem; color: #888; margin: 0;"><?php echo $lang['frontend_collection_single_copy']; ?></p>
                            <?php else: ?>
                                <p style="font-size: 0.8rem; color: #888; margin: 0;"><?php echo sprintf($lang['frontend_collection_copies_summary'], (int)$item->num_copias); ?></p>
                            <?php endif; ?>
                            <?php if ($item->valoracion_juego_usuario !== null): ?>
                                <div class="user-rating-badge">
                                    <span class="user-rating-label"><?php echo $lang['frontend_ratings_your_label']; ?></span>
                                    <span class="user-rating-value">⭐ <?php echo number_format((float)$item->valoracion_juego_usuario, 1); ?>/10</span>
                                </div>
                            <?php else: ?>
                                <span class="user-rating-empty"><?php echo $lang['frontend_ratings_no_personal']; ?></span>
                            <?php endif; ?>
                        </div>

                        <a href="coleccion_juego.php?juego_id=<?php echo (int)$item->juego_id; ?>" 
                           style="display: block; width: 100%; padding: 12px; border-radius: 50px; background: var(--graphite); color: white; text-decoration: none; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; text-align: center; transition: 0.3s;">
                            <?php echo ((int)$item->num_copias > 1) ? $lang['frontend_collection_view_copies'] : $lang['frontend_collection_view_copy']; ?>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>