<?php
require_once '../../includes/auth.php';
redirigirSiNoLogueado();
require_once '../../config/config.php';
require_once '../../models/Juego.php';

$juegoModel = new Juego($pdo);
$resultados = [];
$busquedaRealizada = false;

// Lógica de búsqueda: Procesamos si el usuario ha enviado el parámetro 'q'
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $termino = htmlspecialchars($_GET['q']);
    $resultados = $juegoModel->buscarPorTitulo($termino);
    $busquedaRealizada = true;
} else {
    // Si no hay búsqueda, mostramos todos por defecto (Fase 1: Catálogo) 
    $resultados = $juegoModel->obtenerTodos();
}

include '../../includes/header.php';
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;">Añadir a mi Estantería</h2>
        [cite_start]<p style="color: #666; font-size: 1.1rem;">Localiza el título para gestionar tu colección personal[cite: 13, 191].</p>
        
        <form action="buscar.php" method="GET" style="margin-top: 35px; max-width: 700px; margin-left: auto; margin-right: auto; display: flex; gap: 15px;">
            <input type="text" name="q" 
                   placeholder="Ej: Zelda, Final Fantasy, Metal Gear..." 
                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>"
                   style="flex-grow: 1; padding: 15px 25px; border-radius: 50px; border: 1px solid #ddd; font-family: 'Inter', sans-serif; font-size: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.02);">
            
            <button type="submit" class="btn-dash" style="border: none; cursor: pointer; padding: 0 35px; background: var(--graphite); color: white; border-radius: 50px; font-weight: 600;">
                Buscar
            </button>
        </form>
    </header>

    <div class="projects-grid">
        <?php if (!empty($resultados)): ?>
            <?php foreach ($resultados as $juego): ?>
                <div class="project-card">
                    <div class="project-image" style="display:flex; align-items:center; justify-content:center; background:#eee; font-size:3rem;">
                        🎮
                    </div>
                    <div class="project-info">
                        <div class="tech-tags"><?php echo htmlspecialchars($juego->desarrollador); ?></div>
                        <h3><?php echo htmlspecialchars($juego->titulo); ?></h3>
                        <p style="font-size: 0.85rem; color: #777;">
                            [cite_start]Información técnica del maestro de base de datos[cite: 27].
                        </p>
                        <div class="card-buttons">
                            <a href="juego_detalle.php?id=<?php echo $juego->id; ?>">Ver ediciones</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <div style="font-size: 4rem; margin-bottom: 20px;">🔍</div>
                <h3>No hemos encontrado resultados <?php echo isset($_GET['q']) ? 'para "' . htmlspecialchars($_GET['q']) . '"' : ''; ?></h3>
                <p style="color: #666; margin-bottom: 30px;">Prueba con otros términos o registra el juego manualmente.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="about-wrapper" style="margin-top: 60px; padding-bottom: 40px;">
        <div class="about-box" style="border: 2px dashed var(--silver); background: rgba(255,255,255,0.5);">
            <h3>¿Falta algún juego en el catálogo?</h3>
            <p>Puedes proponer el registro de un nuevo título. [cite_start]Un administrador verificará los datos para mantener la calidad del proyecto[cite: 212].</p>
            <div style="margin-top: 25px;">
                <a href="registrar_nuevo.php" class="btn-dash" style="display: inline-block; text-decoration: none; background: var(--graphite); color: white; padding: 12px 25px; border-radius: 50px; font-weight: 600;">
                    Proponer registro de nuevo juego
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
