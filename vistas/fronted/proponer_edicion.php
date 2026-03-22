<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

$juego_id = $_GET['juego_id'] ?? null;

if (!$juego_id) {
    header('Location: buscar.php');
    exit();
}

// 1. Obtener nombre del juego maestro
$stmt = $pdo->prepare("SELECT titulo FROM juegos WHERE id = ?");
$stmt->execute([$juego_id]);
$titulo_juego = $stmt->fetchColumn();

// 2. Obtener plataformas para el select
$plataformas = $pdo->query("SELECT * FROM plataformas ORDER BY nombre ASC")->fetchAll();

// 3. Obtener regiones dinámicas del sistema (para que refleje los borrados del admin) 
$stmtReg = $pdo->query("SELECT DISTINCT region FROM ediciones WHERE region IS NOT NULL AND region != '' ORDER BY region ASC");
$regionesExistentes = $stmtReg->fetchAll();

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 600px; margin: 0 auto;">
    <header style="text-align: center; margin-bottom: 30px;">
        <h2>Proponer Nueva Edición</h2>
        <p style="color: #666;">Añade una versión física para <strong><?php echo htmlspecialchars($titulo_juego); ?></strong></p>
    </header>

    <div class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee;">
        <form action="../../controllers/JuegoController.php?action=proponer_edicion_existente" method="POST">
            <input type="hidden" name="juego_id" value="<?php echo $juego_id; ?>">

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;">PLATAFORMA</label>
                <select name="plataforma_id" required style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
                    <?php foreach($plataformas as $p): ?>
                        <option value="<?php echo $p->id; ?>"><?php echo htmlspecialchars($p->nombre); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;">NOMBRE DE LA EDICIÓN</label>
                <input type="text" name="edicion_nombre" placeholder="Ej: Black Label, Platinum, Collector's Edition..." required 
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
            </div>

            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;">REGIÓN</label>
                <select name="region" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
                    <?php if (empty($regionesExistentes)): ?>
                        <option value="">No hay regiones disponibles</option>
                    <?php else: ?>
                        <?php foreach($regionesExistentes as $r): ?>
                            <option value="<?php echo htmlspecialchars($r->region); ?>">
                                <?php echo htmlspecialchars($r->region); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <button type="submit" 
                style="width: 100%; padding: 15px; border-radius: 50px; 
                    background: var(--graphite); color: white; font-weight: 700; 
                    text-transform: uppercase; border: none; cursor: pointer; 
                    transition: 0.3s;"
                onmouseover="this.style.background='#333'; this.style.transform='translateY(-2px)';"
                onmouseout="this.style.background='var(--graphite)'; this.style.transform='translateY(0)';"
            >
                Enviar para validación
            </button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>