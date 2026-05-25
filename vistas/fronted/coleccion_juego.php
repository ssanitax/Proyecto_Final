<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../includes/portadas.php';

$usuario_id = (int)$_SESSION['usuario_id'];
$juego_id = (int)($_GET['juego_id'] ?? 0);

if ($juego_id <= 0) {
    header('Location: mi_coleccion.php');
    exit();
}

$sql = "SELECT cu.*, j.titulo, e.juego_id, e.id AS edicion_id, e.edicion_nombre, e.region AS region_edicion,
               e.bloqueo_regional, e.anio, e.imagen_portada, j.fecha_lanzamiento,
               p.fecha_lanzamiento AS plataforma_fecha_lanzamiento,
               p.nombre AS plataforma_nombre, i.nombre AS idioma_nombre,
               pr.nombre_persona AS prestado_a, pr.fecha_prestamo AS fecha_prestamo_activo
        FROM coleccion_usuario cu
        JOIN ediciones e ON cu.edicion_id = e.id
        JOIN juegos j ON e.juego_id = j.id
        JOIN plataformas p ON e.plataforma_id = p.id
        LEFT JOIN idiomas i ON cu.idioma_id = i.id
        LEFT JOIN prestamos pr ON pr.coleccion_id = cu.id AND pr.devuelto = FALSE
        WHERE cu.usuario_id = ? AND e.juego_id = ?
        ORDER BY cu.fecha_adicion DESC, cu.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario_id, $juego_id]);
$copias = $stmt->fetchAll();

if (empty($copias)) {
    header('Location: mi_coleccion.php');
    exit();
}

$titulo = $copias[0]->titulo;
$portada = portadaMasRecienteEntreCopias($copias);

include '../../includes/header.php';
?>

<style>
    .copies-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        max-width: 760px;
    }
    .copy-card {
        background: white;
        border-radius: 14px;
        border: 1px solid #eee;
        overflow: hidden;
        display: flex;
        flex-direction: row;
        align-items: stretch;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    .copy-card.on-loan {
        border-left: 4px solid #f1c40f;
    }
    .copy-cover {
        width: 96px;
        min-width: 96px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }
    .copy-cover img {
        width: 100%;
        height: 100%;
        min-height: 128px;
        object-fit: contain;
        object-position: center;
        display: block;
    }
    .copy-body {
        flex: 1;
        display: flex;
        flex-direction: column;
        padding: 14px 16px;
        min-width: 0;
    }
    .copy-head {
        padding: 0;
        border: none;
        margin-bottom: 8px;
    }
    .copy-head h3 {
        margin: 4px 0 0;
        font-size: 0.95rem;
        font-weight: 800;
        line-height: 1.25;
    }
    .copy-meta {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px 16px;
        font-size: 0.78rem;
        color: #666;
        line-height: 1.4;
        flex-grow: 1;
        margin: 0;
    }
    .copy-meta dt {
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #9ca3af;
        margin: 0;
    }
    .copy-meta dd {
        margin: 2px 0 0 0;
        font-weight: 600;
        color: var(--graphite);
    }
    .copy-meta .meta-full {
        grid-column: 1 / -1;
    }
    .loan-banner {
        margin: 0 0 10px;
        padding: 8px 10px;
        background: #fff8e1;
        border-radius: 8px;
        font-size: 0.75rem;
        color: #92400e;
    }
    .btn-manage {
        display: inline-block;
        align-self: flex-start;
        margin-top: 10px;
        padding: 10px 18px;
        border-radius: 50px;
        background: var(--graphite);
        color: white;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.7rem;
        text-transform: uppercase;
        text-align: center;
    }
    .btn-manage:hover {
        background: #333;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #666;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 24px;
    }
    .back-link:hover { color: var(--graphite); }
    .game-hero {
        display: flex;
        gap: 24px;
        align-items: flex-start;
        margin-bottom: 32px;
        flex-wrap: wrap;
    }
    .game-hero-cover {
        width: 140px;
        aspect-ratio: 3/4;
        border-radius: 12px;
        overflow: hidden;
        background: #f3f4f6;
        flex-shrink: 0;
    }
</style>

<div class="fade-up visible">
    <a href="mi_coleccion.php" class="back-link">← <?php echo $lang['frontend_collection_back']; ?></a>

    <div class="game-hero">
        <?php echo htmlPortada($portada, 'hero'); ?>
        <div>
            <h2 style="margin: 0 0 8px 0;"><?php echo htmlspecialchars($titulo); ?></h2>
            <p style="color: #666; margin: 0;">
                <?php echo sprintf($lang['frontend_collection_game_detail_desc'], count($copias)); ?>
            </p>
        </div>
    </div>

    <div class="copies-list">
        <?php foreach ($copias as $idx => $copia): ?>
            <?php $enPrestamo = !empty($copia->prestado_a); ?>
            <article class="copy-card<?php echo $enPrestamo ? ' on-loan' : ''; ?>">
                <div class="copy-cover">
                    <?php if (!empty($copia->imagen_portada)): ?>
                        <img src="../../img/portadas/<?php echo htmlspecialchars($copia->imagen_portada); ?>" alt="">
                    <?php else: ?>
                        <span aria-hidden="true">🎮</span>
                    <?php endif; ?>
                </div>
                <div class="copy-body">
                    <div class="copy-head">
                        <span style="font-size: 0.62rem; font-weight: 800; text-transform: uppercase; color: #9ca3af;">
                            <?php echo sprintf($lang['frontend_collection_copy_number'], $idx + 1); ?>
                        </span>
                        <h3><?php echo htmlspecialchars($copia->plataforma_nombre); ?></h3>
                        <p style="margin: 2px 0 0; font-size: 0.78rem; color: #888;">
                            <?php echo htmlspecialchars($copia->edicion_nombre); ?>
                            <?php
                                $regionCopia = !empty($copia->region) ? $copia->region : ($copia->region_edicion ?? '');
                                if ($regionCopia !== ''):
                            ?>
                                · <?php echo htmlspecialchars($regionCopia); ?>
                            <?php elseif (!empty($copia->bloqueo_regional)): ?>
                                · <em><?php echo $lang['frontend_collection_region_pending']; ?></em>
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if ($enPrestamo): ?>
                        <div class="loan-banner">
                            📤 <?php echo $lang['frontend_collection_on_loan']; ?>
                            <strong><?php echo htmlspecialchars($copia->prestado_a); ?></strong>
                            (<?php echo date('d/m/Y', strtotime($copia->fecha_prestamo_activo)); ?>)
                        </div>
                    <?php endif; ?>

                    <dl class="copy-meta">
                        <div>
                            <dt><?php echo $lang['frontend_edit_item_label_status']; ?></dt>
                            <dd><?php echo htmlspecialchars($copia->estado); ?></dd>
                        </div>
                        <?php if (!empty($copia->idioma_nombre)): ?>
                        <div>
                            <dt><?php echo $lang['frontend_collection_language_label']; ?></dt>
                            <dd><?php echo htmlspecialchars($copia->idioma_nombre); ?></dd>
                        </div>
                        <?php endif; ?>
                        <div>
                            <dt><?php echo $lang['frontend_collection_label_added']; ?></dt>
                            <dd><?php echo date('d/m/Y', strtotime($copia->fecha_adicion)); ?></dd>
                        </div>
                        <?php if (!empty($copia->notas)): ?>
                        <div class="meta-full">
                            <dt><?php echo $lang['frontend_edit_item_label_notes']; ?></dt>
                            <dd style="font-weight: 500; white-space: pre-wrap;"><?php echo htmlspecialchars($copia->notas); ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>

                    <a href="editar_item.php?id=<?php echo (int)$copia->id; ?>" class="btn-manage">
                        <?php echo $lang['frontend_collection_manage_copy']; ?>
                    </a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
