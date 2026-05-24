<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin();
require_once '../../config/config.php';

$stmtJuegos = $pdo->query("
    SELECT j.id, j.titulo,
        (SELECT e.imagen_portada FROM ediciones e
         WHERE e.juego_id = j.id AND e.imagen_portada IS NOT NULL AND e.imagen_portada != ''
         LIMIT 1) AS imagen_portada
    FROM juegos j
    ORDER BY j.titulo ASC
");
$juegos = $stmtJuegos->fetchAll();

$portadasDir = __DIR__ . '/../../img/portadas';

$juegosSinPortada = [];
$juegosConPortada = [];
foreach ($juegos as $juego) {
    $archivo = !empty($juego->imagen_portada) ? basename($juego->imagen_portada) : '';
    $rutaPortada = $archivo !== '' ? $portadasDir . '/' . $archivo : '';

    // Solo cuenta como "con portada" si el archivo existe en img/portadas/
    // (la BD puede tener nombres del script de ejemplo sin imagen real subida)
    if ($archivo !== '' && is_file($rutaPortada)) {
        $juego->imagen_portada = $archivo;
        $juegosConPortada[] = $juego;
    } else {
        $juego->imagen_portada = null;
        $juegosSinPortada[] = $juego;
    }
}

include '../../includes/admin_header.php'; 
?>

<style>
    /* Estilo unificado para las tarjetas del admin [cite: 268-270, 899-900] */
    .dash-card {
        background: white;
        padding: 40px 30px;
        border-radius: 20px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: 0.3s ease;
        border: 1px solid #eee;
    }

    /* Estilo unificado para los botones de acción del admin  */
    .btn-dash {
        display: block;
        width: 100%;
        padding: 12px 25px;
        border-radius: 50px;
        background: var(--graphite);
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        text-align: center;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-dash:hover {
        background: #333;
        transform: translateY(-2px);
    }

    input {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #eee;
        margin-bottom: 15px;
        font-family: inherit;
        outline: none;
    }

    input:focus {
        border-color: var(--graphite);
    }

    select {
        width: 100%;
        padding: 12px;
        border-radius: 10px;
        border: 1px solid #eee;
        margin-bottom: 15px;
        font-family: inherit;
        outline: none;
        background: white;
    }

    select:focus {
        border-color: var(--graphite);
    }

    .cover-picker-section { margin-bottom: 18px; }

    .cover-picker-heading {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cover-picker-heading.without { color: #b45309; }
    .cover-picker-heading.with { color: #047857; }

    .cover-picker-heading .count {
        background: #f3f4f6;
        color: #666;
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 0.65rem;
    }

    .cover-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
        gap: 10px;
        max-height: 220px;
        overflow-y: auto;
        padding: 4px;
        margin-bottom: 4px;
    }

    .cover-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        padding: 10px 8px;
        border-radius: 12px;
        border: 2px solid #eee;
        background: #fafafa;
        cursor: pointer;
        transition: 0.2s;
        text-align: center;
    }

    .cover-item:hover { border-color: #ccc; background: #fff; }

    .cover-item input { display: none; margin: 0; }

    .cover-item:has(input:checked) {
        border-color: var(--graphite);
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .cover-item-thumb {
        width: 64px;
        height: 64px;
        border-radius: 8px;
        object-fit: cover;
        background: #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        overflow: hidden;
    }

    .cover-item-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cover-item.no-cover .cover-item-thumb {
        background: #fef3c7;
        color: #b45309;
        font-size: 0.65rem;
        font-weight: 800;
        line-height: 1.1;
        padding: 4px;
    }

    .cover-item-name {
        font-size: 0.65rem;
        font-weight: 700;
        line-height: 1.2;
        color: var(--graphite);
        word-break: break-word;
    }

    .cover-preview-box {
        display: none;
        margin-bottom: 15px;
        padding: 12px;
        border-radius: 12px;
        border: 1px solid #eee;
        background: #fafafa;
        text-align: center;
    }

    .cover-preview-box.visible { display: block; }

    .cover-preview-box img {
        max-width: 100%;
        max-height: 140px;
        border-radius: 8px;
        object-fit: contain;
        margin-top: 8px;
    }

    .cover-preview-label {
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #888;
    }

    .cover-preview-empty {
        font-size: 0.85rem;
        color: #999;
        font-style: italic;
        margin: 0;
    }

    .cover-picker-empty-msg {
        font-size: 0.8rem;
        color: #999;
        font-style: italic;
        padding: 8px 0;
    }
</style>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2><?php echo $lang['admin_direct_management_title']; ?></h2>
        <p style="color: #666;"><?php echo $lang['admin_direct_management_desc']; ?></p>
    </header>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 30px; font-weight: 600; border: 1px solid #a7f3d0;">
            <?php echo $lang['registro_exitoso']; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['cover_status']) && $_GET['cover_status'] == 'success'): ?>
        <div style="background: #dbeafe; color: #1d4ed8; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 30px; font-weight: 600; border: 1px solid #bfdbfe;">
            <?php echo $lang['admin_cover_uploaded']; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['cover_error'])): ?>
        <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 30px; font-weight: 600; border: 1px solid #fecaca;">
            <?php
                $errorMap = [
                    'invalid_game' => $lang['admin_cover_error_invalid_game'],
                    'upload' => $lang['admin_cover_error_upload'],
                    'type' => $lang['admin_cover_error_type'],
                    'filesystem' => $lang['admin_cover_error_filesystem']
                ];
                $errorKey = $_GET['cover_error'];
                echo $errorMap[$errorKey] ?? $lang['error_general'];
            ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; max-width: 1100px; margin: 0 auto;">
        
        <div class="dash-card">
            <h3 style="margin-bottom: 20px; font-weight: 800;"><?php echo $lang['admin_direct_new_platform']; ?></h3>
            <form action="../../controllers/AdminController.php?action=registrar_plataforma" method="POST">
                <input type="text" name="nombre" placeholder="<?php echo $lang['admin_direct_platform_placeholder']; ?>" required>
                <button type="submit" class="btn-dash"><?php echo $lang['admin_direct_save_platform']; ?></button>
            </form>
        </div>

        <div class="dash-card">
            <h3 style="margin-bottom: 20px; font-weight: 800;"><?php echo $lang['admin_direct_new_region']; ?></h3>
            <form action="../../controllers/AdminController.php?action=registrar_region" method="POST">
                <input type="text" name="nombre" placeholder="<?php echo $lang['admin_direct_region_placeholder']; ?>" required>
                <button type="submit" class="btn-dash"><?php echo $lang['admin_direct_save_region']; ?></button>
            </form>
        </div>

        <div class="dash-card">
            <h3 style="margin-bottom: 20px; font-weight: 800;"><?php echo $lang['admin_direct_new_game']; ?></h3>
            <form action="../../controllers/AdminController.php?action=registrar_juego" method="POST">
                <input type="text" name="titulo" placeholder="<?php echo $lang['admin_direct_game_title_placeholder']; ?>" required>
                <input type="text" name="desarrollador" placeholder="<?php echo $lang['admin_direct_game_dev_placeholder']; ?>">
                <button type="submit" class="btn-dash"><?php echo $lang['admin_direct_save_game']; ?></button>
            </form>
        </div>

        <div class="dash-card" style="grid-column: 1 / -1;">
            <h3 style="margin-bottom: 12px; font-weight: 800;"><?php echo $lang['admin_cover_title']; ?></h3>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 18px;"><?php echo $lang['admin_cover_desc']; ?></p>
            <form action="../../controllers/AdminController.php?action=subir_portada_juego" method="POST" enctype="multipart/form-data" id="cover-upload-form">
                <p style="font-size:0.85rem; font-weight:700; margin-bottom:14px; color:#444;">
                    <?php echo ($idiomaActual ?? 'es') === 'en' ? 'Click a game to select it:' : 'Haz clic en un juego para seleccionarlo:'; ?>
                </p>

                <div class="cover-picker-section">
                    <p class="cover-picker-heading without">
                        <?php echo ($idiomaActual ?? 'es') === 'en' ? 'No cover' : 'Sin portada'; ?>
                        <span class="count"><?php echo count($juegosSinPortada); ?></span>
                    </p>
                    <?php if (empty($juegosSinPortada)): ?>
                        <p class="cover-picker-empty-msg"><?php echo ($idiomaActual ?? 'es') === 'en' ? 'All games have a cover.' : 'Todos los juegos tienen portada.'; ?></p>
                    <?php else: ?>
                        <div class="cover-grid">
                            <?php $coverRadioRequired = true; foreach ($juegosSinPortada as $juego): ?>
                                <label class="cover-item no-cover">
                                    <input type="radio" name="juego_id" value="<?php echo (int)$juego->id; ?>"<?php if ($coverRadioRequired) { echo ' required'; $coverRadioRequired = false; } ?> data-portada="">
                                    <span class="cover-item-thumb"><?php echo ($idiomaActual ?? 'es') === 'en' ? 'NO PHOTO' : 'SIN FOTO'; ?></span>
                                    <span class="cover-item-name"><?php echo htmlspecialchars($juego->titulo); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="cover-picker-section">
                    <p class="cover-picker-heading with">
                        <?php echo ($idiomaActual ?? 'es') === 'en' ? 'Has cover' : 'Con portada'; ?>
                        <span class="count"><?php echo count($juegosConPortada); ?></span>
                    </p>
                    <?php if (empty($juegosConPortada)): ?>
                        <p class="cover-picker-empty-msg"><?php echo ($idiomaActual ?? 'es') === 'en' ? 'No games have a cover yet.' : 'Ningún juego tiene portada todavía.'; ?></p>
                    <?php else: ?>
                        <div class="cover-grid">
                            <?php
                            if (!isset($coverRadioRequired)) { $coverRadioRequired = true; }
                            foreach ($juegosConPortada as $juego):
                            ?>
                                <label class="cover-item has-cover">
                                    <input type="radio" name="juego_id" value="<?php echo (int)$juego->id; ?>"<?php if ($coverRadioRequired) { echo ' required'; $coverRadioRequired = false; } ?> data-portada="<?php echo htmlspecialchars($juego->imagen_portada); ?>">
                                    <span class="cover-item-thumb">
                                        <img src="../../img/portadas/<?php echo htmlspecialchars($juego->imagen_portada); ?>" alt="">
                                    </span>
                                    <span class="cover-item-name"><?php echo htmlspecialchars($juego->titulo); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="cover-preview-box" class="cover-preview-box" aria-live="polite">
                    <div class="cover-preview-label" id="cover-preview-label"></div>
                    <img id="cover-preview-img" src="" alt="">
                    <p id="cover-preview-empty" class="cover-preview-empty" style="display:none;"></p>
                </div>

                <input type="file" name="portada" accept="image/jpeg,image/png,image/webp" required>
                <button type="submit" class="btn-dash"><?php echo $lang['admin_cover_upload_button']; ?></button>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('cover-upload-form');
    if (!form) return;

    var radios = form.querySelectorAll('input[name="juego_id"]');
    var box = document.getElementById('cover-preview-box');
    var img = document.getElementById('cover-preview-img');
    var label = document.getElementById('cover-preview-label');
    var empty = document.getElementById('cover-preview-empty');
    var labelWith = <?php echo json_encode($lang['admin_cover_preview_label'] ?? 'Portada actual'); ?>;
    var labelEmpty = <?php echo json_encode($lang['admin_cover_preview_empty'] ?? 'Este juego aún no tiene portada.'); ?>;
    var basePath = '../../img/portadas/';

    function updatePreview() {
        var selected = form.querySelector('input[name="juego_id"]:checked');
        if (!selected) {
            box.classList.remove('visible');
            return;
        }

        var portada = selected.getAttribute('data-portada') || '';
        box.classList.add('visible');

        if (portada) {
            label.textContent = labelWith;
            img.src = basePath + portada;
            img.style.display = 'block';
            empty.style.display = 'none';
        } else {
            label.textContent = '';
            img.src = '';
            img.style.display = 'none';
            empty.textContent = labelEmpty;
            empty.style.display = 'block';
        }
    }

    radios.forEach(function (radio) {
        radio.addEventListener('change', updatePreview);
    });
})();
</script>

<?php include '../../includes/admin_footer.php'; ?>