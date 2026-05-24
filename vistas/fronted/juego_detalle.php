<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';
require_once '../../models/Valoracion.php';

$id_juego = $_GET['id'] ?? null;

if (!$id_juego) {
    header('Location: buscar.php');
    exit();
}

// 1. Datos generales del juego (Maestro)
$stmt = $pdo->prepare("SELECT * FROM juegos WHERE id = ?");
$stmt->execute([$id_juego]);
$juego = $stmt->fetch();

if (!$juego) { die($lang['frontend_game_detail_not_found']); }

$stmtPortada = $pdo->prepare("SELECT imagen_portada FROM ediciones WHERE juego_id = ? AND imagen_portada IS NOT NULL AND imagen_portada != '' ORDER BY id DESC LIMIT 1");
$stmtPortada->execute([$id_juego]);
$portadaJuego = $stmtPortada->fetchColumn();

// 2. Obtener las variantes físicas (Consola + Región)
$stmtEdic = $pdo->prepare("
    SELECT e.*, p.nombre as plataforma_nombre 
    FROM ediciones e 
    JOIN plataformas p ON e.plataforma_id = p.id 
    WHERE e.juego_id = ?
");
$stmtEdic->execute([$id_juego]);
$ediciones = $stmtEdic->fetchAll();

if (empty($ediciones)) {
    header('Location: buscar.php');
    exit();
}

$idiomas = [];
try {
    $stmtIdiomasJuego = $pdo->prepare(
        "SELECT i.id, i.nombre FROM idiomas i
         INNER JOIN juego_idiomas ji ON ji.idioma_id = i.id
         WHERE ji.juego_id = ?
         ORDER BY i.nombre ASC"
    );
    $stmtIdiomasJuego->execute([$id_juego]);
    $idiomas = $stmtIdiomasJuego->fetchAll();
    if (empty($idiomas)) {
        $idiomas = $pdo->query("SELECT id, nombre FROM idiomas ORDER BY nombre ASC")->fetchAll();
    }
} catch (PDOException $e) {
    try {
        $idiomas = $pdo->query("SELECT id, nombre FROM idiomas ORDER BY nombre ASC")->fetchAll();
    } catch (PDOException $e2) {
        $idiomas = [];
    }
}

$valoracionModel = new Valoracion($pdo);
$resumenValoraciones = $valoracionModel->obtenerResumenJuegoParaUsuario($id_juego, $_SESSION['usuario_id']);

$mediaGlobal = $resumenValoraciones && $resumenValoraciones->media_global !== null
    ? number_format((float)$resumenValoraciones->media_global, 1)
    : null;

$totalValoraciones = $resumenValoraciones ? (int)$resumenValoraciones->total_valoraciones : 0;

$mediaUsuario = $resumenValoraciones && $resumenValoraciones->media_usuario !== null
    ? number_format((float)$resumenValoraciones->media_usuario, 1)
    : null;

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 900px; margin: 0 auto;">
    <div class="about-box" style="display: flex; gap: 40px; align-items: flex-start; flex-wrap: wrap;">
        
        <div style="flex: 1; min-width: 280px;">
            <div style="background: #1c1f26; aspect-ratio: 3/4; display: flex; align-items: center; justify-content: center; font-size: 6rem; border-radius: 15px; color: white; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
                <?php if (!empty($portadaJuego)): ?>
                    <img src="../../img/portadas/<?php echo htmlspecialchars($portadaJuego); ?>" alt="<?php echo htmlspecialchars($juego->titulo); ?>" style="width: 100%; height: 100%; object-fit: cover; display: block;">
                <?php else: ?>
                    🎮
                <?php endif; ?>
            </div>
            <p style="margin-top: 20px; color: #888; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;"><?php echo $lang['frontend_game_detail_database_id']; ?><?php echo $juego->id; ?></p>
        </div>

        <div style="flex: 1.5; min-width: 320px; text-align: left;">
            <h2 style="text-align: left; margin-bottom: 5px;"><?php echo htmlspecialchars($juego->titulo); ?></h2>
            <p style="color: #666; margin-bottom: 30px;"><?php echo htmlspecialchars($juego->desarrollador); ?></p>

            <div class="ratings-summary">
                <div class="rating-box">
                    <div class="rating-label"><?php echo $lang['frontend_ratings_average_label']; ?></div>
                    <?php if ($mediaGlobal !== null): ?>
                        <div class="rating-value">⭐ <?php echo $mediaGlobal; ?>/10</div>
                        <div class="rating-meta"><?php echo sprintf($lang['frontend_ratings_votes'], $totalValoraciones); ?></div>
                    <?php else: ?>
                        <div class="rating-meta"><?php echo $lang['frontend_ratings_no_data']; ?></div>
                    <?php endif; ?>
                </div>

                <div class="rating-box">
                    <div class="rating-label"><?php echo $lang['frontend_ratings_your_label']; ?></div>
                    <?php if ($mediaUsuario !== null): ?>
                        <div class="rating-value">🎯 <?php echo $mediaUsuario; ?>/10</div>
                    <?php else: ?>
                        <div class="rating-meta"><?php echo $lang['frontend_ratings_no_personal']; ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div style="border-top: 1px solid #eee; padding-top: 25px;">
                <h4 style="margin-bottom: 20px; font-size: 0.85rem; color: #333; font-weight: 800;"><?php echo $lang['frontend_game_detail_select_version']; ?></h4>

                <?php if (isset($_GET['error']) && $_GET['error'] === 'no_language'): ?>
                    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:10px;margin-bottom:16px;font-size:0.85rem;font-weight:600;">
                        <?php echo $lang['frontend_game_detail_language_required']; ?>
                    </div>
                <?php endif; ?>
                
                <form action="../../controllers/ColeccionController.php?action=agregar" method="POST">
                    <input type="hidden" name="juego_id" value="<?php echo (int)$juego->id; ?>">
                    <div style="display: grid; gap: 12px;">
                        <?php if (empty($ediciones)): ?>
                            <p style="color: #999; font-style: italic; padding: 20px; background: #f9f9f9; border-radius: 10px;"><?php echo $lang['frontend_game_detail_no_consoles']; ?></p>
                        <?php else: ?>
                            <?php foreach($ediciones as $edic): ?>
                                <label class="version-card">
                                    <input type="radio" name="edicion_id" value="<?php echo $edic->id; ?>" required>
                                    <div class="version-details">
                                        <div class="plat-name"><?php echo htmlspecialchars($edic->plataforma_nombre); ?></div>
                                        <div class="edic-info">
                                            <?php echo htmlspecialchars($edic->edicion_nombre); ?> 
                                            <?php if (!empty($edic->region)): ?>
                                            <span class="region-pill"><?php echo htmlspecialchars($edic->region); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>

                            <?php if (!empty($idiomas)): ?>
                                <div style="margin-top: 8px; text-align: left;">
                                    <label style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: #666; display: block; margin-bottom: 8px;">
                                        <?php echo $lang['frontend_game_detail_label_language']; ?>
                                    </label>
                                    <?php if (count($idiomas) === 1): ?>
                                        <input type="hidden" name="idioma_id" value="<?php echo (int)$idiomas[0]->id; ?>">
                                        <p style="margin: 0; padding: 12px; background: #f4f5f7; border-radius: 10px; font-weight: 700; color: var(--graphite);">
                                            <?php echo htmlspecialchars($idiomas[0]->nombre); ?>
                                        </p>
                                    <?php else: ?>
                                    <select name="idioma_id" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
                                        <option value=""><?php echo $lang['frontend_game_detail_select_language']; ?></option>
                                        <?php foreach ($idiomas as $idioma): ?>
                                            <option value="<?php echo (int)$idioma->id; ?>"><?php echo htmlspecialchars($idioma->nombre); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <p style="font-size: 0.8rem; color: #999; font-style: italic; margin-top: 8px; text-align: left;">
                                    <?php echo $lang['frontend_game_detail_no_languages']; ?>
                                </p>
                            <?php endif; ?>
                            
                            <button type="submit" class="btn-confirm"><?php echo $lang['frontend_game_detail_add_library']; ?></button>
                        <?php endif; ?>
                    </div>
                </form>

                <div style="margin-top: 40px; padding: 20px; background: #f4f5f7; border-radius: 12px; text-align: center;">
                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;"><?php echo $lang['frontend_game_detail_suggest_question']; ?></p>
                    <a href="proponer_edicion.php?juego_id=<?php echo $juego->id; ?>" style="color: var(--graphite); font-weight: 800; text-decoration: none; font-size: 0.85rem;">
                        <?php echo $lang['frontend_game_detail_suggest_link']; ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ratings-summary {
        display: grid;
        grid-template-columns: repeat(2, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }

    .rating-box {
        background: #f8f9fb;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 14px;
    }

    .rating-label {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .rating-value {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--graphite);
    }

    .rating-meta {
        font-size: 0.82rem;
        color: #7a7a7a;
    }

    .version-card {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        border: 2px solid #eee;
        border-radius: 12px;
        cursor: pointer;
        transition: 0.2s ease-in-out;
    }
    .version-card:hover { border-color: var(--silver); background: #fafafa; }
    .version-card input[type="radio"] { margin-right: 20px; accent-color: var(--graphite); transform: scale(1.2); }
    
    /* Efecto cuando está seleccionado */
    .version-card:has(input:checked) { border-color: var(--graphite); background: #f0f2f5; }

    .plat-name { font-weight: 800; font-size: 1rem; color: var(--graphite); text-transform: uppercase; }
    .edic-info { font-size: 0.85rem; color: #666; margin-top: 3px; }
    .region-pill { font-size: 0.7rem; background: #ddd; padding: 2px 6px; border-radius: 4px; margin-left: 5px; font-weight: 600; }
    
    .btn-confirm {
        margin-top: 20px;
        background: var(--graphite);
        color: white;
        padding: 18px;
        border: none;
        border-radius: 50px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .btn-confirm:hover { background: #333; transform: translateY(-2px); }

    @media (max-width: 700px) {
        .ratings-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php include '../../includes/footer.php'; ?>
