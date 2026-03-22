<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

// Necesitamos las plataformas para el select
$stmt = $pdo->query("SELECT * FROM plataformas ORDER BY nombre ASC");
$plataformas = $stmt->fetchAll();

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 700px; margin: 0 auto;">
    <h2>Proponer Nuevo Juego</h2>
    <p style="text-align:center; color:#666; margin-bottom:30px;">
        Tu propuesta será revisada por un administrador antes de ser pública.
    </p>

    <form action="../../controllers/JuegoController.php?action=proponer" method="POST" class="about-box">
        <h3 style="text-align:left; margin-bottom:20px; border-bottom:1px solid #eee; padding-bottom:10px;">Datos del Maestro</h3>
        
        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;">TÍTULO DEL JUEGO</label>
            <input type="text" name="titulo" placeholder="Ej: Silent Hill 2" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
        </div>

        <div class="form-group" style="margin-bottom:15px; text-align:left;">
            <label style="font-weight:600; font-size:0.8rem;">DESARROLLADOR</label>
            <input type="text" name="desarrollador" placeholder="Ej: Konami" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
        </div>

        <h3 style="text-align:left; margin:30px 0 20px 0; border-bottom:1px solid #eee; padding-bottom:10px;">Datos de la Primera Edición</h3>
        
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div class="form-group" style="text-align:left;">
                <label style="font-weight:600; font-size:0.8rem;">PLATAFORMA</label>
                <select name="plataforma_id" required style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                    <?php foreach($plataformas as $p): ?>
                        <option value="<?php echo $p->id; ?>"><?php echo $p->nombre; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="text-align:left;">
                <label style="font-weight:600; font-size:0.8rem;">REGIÓN</label>
                <select name="region" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ddd;">
                    <option value="PAL">PAL (Europa)</option>
                    <option value="NTSC-U">NTSC-U (USA)</option>
                    <option value="NTSC-J">NTSC-J (Japón)</option>
                </select>
            </div>
        </div>

        <button type="submit" 
            style="margin-top: 30px; width: 100%; padding: 15px; border-radius: 50px; 
                background: var(--graphite); color: white; font-weight: 700; 
                text-transform: uppercase; border: none; cursor: pointer; 
                transition: 0.3s;"
            onmouseover="this.style.background='#333'; this.style.transform='translateY(-2px)';"
            onmouseout="this.style.background='var(--graphite)'; this.style.transform='translateY(0)';"
    >
        Enviar propuesta
    </button>
    </form>
</div>

<?php include '../../includes/footer.php'; ?>
