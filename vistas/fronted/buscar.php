<?php
require_once '../../includes/auth.php';
redirigirSiNoLogueado();
require_once '../../config/config.php';
require_once '../../models/Juego.php';

$juegoModel = new Juego($pdo);
$resultados = [];

if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $resultados = $juegoModel->buscarPorTitulo(htmlspecialchars($_GET['q']));
} else {
    $resultados = $juegoModel->obtenerTodos();
}

include '../../includes/header.php';
?>

<style>
    /* Estilos mejorados para las tarjetas tipo Portafolio */
    .shelf-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 30px;
        padding: 20px 0;
    }

    .game-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        flex-direction: column;
        border: 1px solid #eee;
    }

    .game-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        border-color: var(--graphite);
    }

    .game-cover {
        width: 100%;
        aspect-ratio: 3 / 4;
        background: #f0f0f0;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .game-cover img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .game-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    .dev-badge {
        background: rgba(28, 31, 38, 0.8);
        color: white;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        backdrop-filter: blur(4px);
    }

    .game-content {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .game-content h3 {
        font-size: 1.1rem;
        margin-bottom: 10px;
        font-weight: 800;
        color: var(--graphite);
        line-height: 1.3;
    }

    .btn-add {
        display: block;
        width: 100%;
        padding: 12px;
        background: var(--graphite);
        color: white;
        text-align: center;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: 0.3s;
        margin-top: 15px;
    }

    .btn-add:hover {
        background: #333;
        letter-spacing: 0.5px;
    }

    .search-container {
        max-width: 800px;
        margin: 0 auto 50px auto;
        text-align: center;
    }

    .search-input {
        width: 100%;
        padding: 18px 30px;
        border-radius: 50px;
        border: 2px solid #eee;
        font-size: 1rem;
        transition: 0.3s;
        box-shadow: 0 5px 15px rgba(0,0,0,0.02);
    }

    .search-input:focus {
        outline: none;
        border-color: var(--graphite);
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
</style>

<div class="fade-up visible">
    <div class="search-container">
        <h2 style="margin-bottom: 15px;">Explorar Catálogo</h2>
        <p style="color: #777; margin-bottom: 30px;">Busca por título o desarrolladora para añadir a tu estantería.</p>
        
        <form action="buscar.php" method="GET">
            <input type="text" name="q" class="search-input" 
                   placeholder="Busca un juego (ej. Metal Gear, Zelda...)"
                   value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
        </form>
    </div>

    <div class="shelf-grid">
        <?php foreach ($resultados as $juego): ?>
            <div class="game-card">
                <div class="game-cover">
                    <div class="game-overlay">
                        <span class="dev-badge"><?php echo htmlspecialchars($juego->desarrollador); ?></span>
                    </div>
                    <span style="font-size: 4rem;">🎮</span>
                </div>
                
                <div class="game-content">
                    <h3><?php echo htmlspecialchars($juego->titulo); ?></h3>
                    
                    <a href="juego_detalle.php?id=<?php echo $juego->id; ?>" class="btn-add">
                        + Añadir Juego
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (empty($resultados)): ?>
        <div style="text-align:center; padding: 100px 0;">
            <p style="color:#999;">No hemos encontrado resultados.</p>
            <a href="registrar_nuevo.php" style="color:var(--graphite); font-weight:600;">¿Quieres proponer este juego?</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../../includes/footer.php'; ?>
