<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin(); // Nueva función de seguridad que creamos [cite: 876, 877]

require_once '../../config/config.php';

// 1. PROCESAR EL ALTA DE UNA NUEVA PLATAFORMA
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_plataforma'])) {
    $nombre = htmlspecialchars(trim($_POST['nombre_plataforma']));
    if (!empty($nombre)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO plataformas (nombre) VALUES (?)");
            $stmt->execute([$nombre]);
            $mensaje = "Plataforma añadida con éxito.";
        } catch (PDOException $e) {
            $error = "Esa plataforma ya existe.";
        }
    }
}

// 2. OBTENER TODAS LAS PLATAFORMAS ACTUALES [cite: 799]
$plataformas = $pdo->query("SELECT * FROM plataformas ORDER BY nombre ASC")->fetchAll();

include '../../includes/admin_header.php'; 
?>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2 style="margin-bottom: 10px;">Gestión de Sistemas 🎮</h2>
        <p style="color: #666;">Añade nuevas consolas o sistemas al catálogo oficial de Bengala.</p>
    </header>

    <div style="max-width: 600px; margin: 0 auto;">
        <div class="dash-card" style="margin-bottom: 40px; padding: 30px;">
            <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Añadir Nueva Plataforma</h3>
            
            <?php if (isset($mensaje)): ?>
                <p style="color: #2ecc71; font-weight: 700; margin-bottom: 15px; font-size: 0.9rem;"><?php echo $mensaje; ?></p>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <p style="color: #e74c3c; font-weight: 700; margin-bottom: 15px; font-size: 0.9rem;"><?php echo $error; ?></p>
            <?php endif; ?>

            <form action="" method="POST" style="width: 100%;">
                <input type="text" name="nombre_plataforma" placeholder="Ej: PlayStation 5, Xbox Series X, etc." required 
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #eee; margin-bottom: 15px; font-family: inherit;">
                <button type="submit" class="btn-dash" style="width: 100%; border: none; cursor: pointer;">Guardar Plataforma</button>
            </form>
        </div>

        <div class="dash-card" style="padding: 30px;">
            <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Sistemas Registrados</h3>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; justify-content: center;">
                <?php foreach ($plataformas as $plat): ?>
                    <span style="background: #f0f0f0; padding: 8px 15px; border-radius: 50px; font-size: 0.85rem; font-weight: 600; color: var(--graphite);">
                        <?php echo htmlspecialchars($plat->nombre); ?>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>