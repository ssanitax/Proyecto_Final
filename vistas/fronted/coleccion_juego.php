<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$usuario_id = (int)$_SESSION['usuario_id'];
$juego_id = (int)($_GET['juego_id'] ?? 0);

if ($juego_id <= 0) {
    header('Location: mi_coleccion.php');
    exit();
}

$sql = "SELECT cu.*, j.titulo, e.juego_id, e.edicion_nombre, e.region, e.imagen_portada,
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
$portada = null;
foreach ($copias as $c) {
    if (!empty($c->imagen_portada)) {
        $portada = $c->imagen_portada;
        break;
    }
}

$estadosConservacion = [
    'nuevo' => $lang['frontend_collection_condition_new'] ?? 'Nuevo',
    'como_nuevo' => $lang['frontend_collection_condition_like_new'] ?? 'Como nuevo',
    'bueno' => $lang['frontend_collection_condition_good'] ?? 'Bueno',
    'usado' => $lang['frontend_collection_condition_used'] ?? 'Usado',
    'sin_caja' => $lang['frontend_collection_condition_no_box'] ?? 'Sin caja',
];

include '../../includes/header.php';
?>

<style>
    .copies-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
    }
    .copy-card {
        background: white;
        border-radius: 16px;
        border: 1px solid #eee;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .copy-card.on-loan { border-left: 4px solid #f1c40f; }
    .copy-head {
        padding: 18px 18px 12px;
        border-bottom: 1px solid #f5f5f5;
    }
    .copy-meta {
        padding: 0 18px 18px;
        font-size: 0.82rem;
        color: #666;
        line-height: 1.6;
        flex-grow: 1;
    }
    .copy-meta dt {
        font-size: 0.62rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #9ca3af;
        margin-top: 10px;
    }
    .copy-meta dd { margin: 2px 0 0 0; font-weight: 600; color: var(--graphite); }
    .loan-banner {
        margin: 0 18px 12px;
        padding: 10px 12px;
        background: #fff8e1;
        border-radius: 8px;
        font-size: 0.8rem;
        color: #92400e;
    }
    .btn-manage {
        display: block;
        margin: 0 18px 18px;
        padding: 12px;
        border-radius: 50px;
        background: var(--graphite);
        color: white;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        text-align: center;
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
        <div class="game-hero-cover">
            <?php if ($portada): ?>
                <img src="../../img/portadas/<?php echo htmlspecialchars($portada); ?>" alt="" style="width:100%;height:100%;object-fit:cover;">
            <?php else: ?>
                <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:3rem;">🎮</div>
            <?php endif; ?>
        </div>
        <div>
            <h2 style="margin: 0 0 8px 0;"><?php echo htmlspecialchars($titulo); ?></h2>
            <p style="color: #666; margin: 0;">
                <?php echo sprintf($lang['frontend_collection_game_detail_desc'], count($copias)); ?>
            </p>
        </div>
    </div>

    <div class="copies-grid">
        <?php foreach ($copias as $idx => $copia): ?>
            <?php $enPrestamo = !empty($copia->prestado_a); ?>
            <article class="copy-card<?php echo $enPrestamo ? ' on-loan' : ''; ?>">
                <div class="copy-head">
                    <span style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase; color: #9ca3af;">
                        <?php echo sprintf($lang['frontend_collection_copy_number'], $idx + 1); ?>
                    </span>
                    <h3 style="margin: 6px 0 0; font-size: 1rem; font-weight: 800;">
                        <?php echo htmlspecialchars($copia->plataforma_nombre); ?>
                    </h3>
                    <p style="margin: 4px 0 0; font-size: 0.8rem; color: #888;">
                        <?php echo htmlspecialchars($copia->edicion_nombre); ?>
                        <?php if (!empty($copia->region)): ?>
                            • <?php echo htmlspecialchars($copia->region); ?>
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
                    <dt><?php echo $lang['frontend_edit_item_label_status']; ?></dt>
                    <dd><?php echo htmlspecialchars($copia->estado); ?></dd>

                    <?php if (!empty($copia->estado_conservacion)): ?>
                    <dt><?php echo $lang['frontend_collection_label_condition']; ?></dt>
                    <dd><?php echo htmlspecialchars($estadosConservacion[$copia->estado_conservacion] ?? $copia->estado_conservacion); ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($copia->idioma_nombre)): ?>
                    <dt><?php echo $lang['frontend_collection_language_label']; ?></dt>
                    <dd><?php echo htmlspecialchars($copia->idioma_nombre); ?></dd>
                    <?php endif; ?>

                    <dt><?php echo $lang['frontend_collection_label_added']; ?></dt>
                    <dd><?php echo date('d/m/Y', strtotime($copia->fecha_adicion)); ?></dd>

                    <?php if (!empty($copia->notas)): ?>
                    <dt><?php echo $lang['frontend_edit_item_label_notes']; ?></dt>
                    <dd style="font-weight: 500; white-space: pre-wrap;"><?php echo htmlspecialchars($copia->notas); ?></dd>
                    <?php endif; ?>
                </dl>

                <a href="editar_item.php?id=<?php echo (int)$copia->id; ?>" class="btn-manage">
                    <?php echo $lang['frontend_collection_manage_copy']; ?>
                </a>
            </article>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
