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

$juegosSinPortada = [];
$juegosConPortada = [];
foreach ($juegos as $juego) {
    if (!empty($juego->imagen_portada)) {
        $juegosConPortada[] = $juego;
    } else {
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

    .cover-legend {
        display: flex;
        gap: 16px;
        font-size: 0.75rem;
        margin: -8px 0 14px 0;
        font-weight: 600;
    }

    .cover-legend-without { color: #b45309; }
    .cover-legend-with { color: #047857; }

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

        <div class="dash-card">
            <h3 style="margin-bottom: 12px; font-weight: 800;"><?php echo $lang['admin_cover_title']; ?></h3>
            <p style="color: #666; font-size: 0.9rem; margin-bottom: 18px;"><?php echo $lang['admin_cover_desc']; ?></p>
            <form action="../../controllers/AdminController.php?action=subir_portada_juego" method="POST" enctype="multipart/form-data">
                <div class="cover-legend">
                    <span class="cover-legend-without">● <?php echo $lang['admin_cover_legend_without']; ?> (<?php echo count($juegosSinPortada); ?>)</span>
                    <span class="cover-legend-with">● <?php echo $lang['admin_cover_legend_with']; ?> (<?php echo count($juegosConPortada); ?>)</span>
                </div>
                <select name="juego_id" id="cover-game-select" required>
                    <option value="" data-portada=""><?php echo $lang['admin_cover_select_game']; ?></option>
                    <?php if (!empty($juegosSinPortada)): ?>
                        <optgroup label="<?php echo $lang['admin_cover_group_without']; ?>">
                            <?php foreach ($juegosSinPortada as $juego): ?>
                                <option value="<?php echo (int)$juego->id; ?>" data-portada="">
                                    ○ <?php echo htmlspecialchars($juego->titulo); ?> — <?php echo $lang['admin_cover_option_no_photo']; ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                    <?php if (!empty($juegosConPortada)): ?>
                        <optgroup label="<?php echo $lang['admin_cover_group_with']; ?>">
                            <?php foreach ($juegosConPortada as $juego): ?>
                                <option value="<?php echo (int)$juego->id; ?>" data-portada="<?php echo htmlspecialchars($juego->imagen_portada); ?>">
                                    ✓ <?php echo htmlspecialchars($juego->titulo); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                </select>

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
    var select = document.getElementById('cover-game-select');
    var box = document.getElementById('cover-preview-box');
    var img = document.getElementById('cover-preview-img');
    var label = document.getElementById('cover-preview-label');
    var empty = document.getElementById('cover-preview-empty');
    var labelWith = <?php echo json_encode($lang['admin_cover_preview_label']); ?>;
    var labelEmpty = <?php echo json_encode($lang['admin_cover_preview_empty']); ?>;
    var basePath = '../../img/portadas/';

    function updatePreview() {
        var option = select.options[select.selectedIndex];
        var portada = option ? option.getAttribute('data-portada') : '';

        if (!select.value) {
            box.classList.remove('visible');
            return;
        }

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

    select.addEventListener('change', updatePreview);
})();
</script>

<?php include '../../includes/admin_footer.php'; ?>