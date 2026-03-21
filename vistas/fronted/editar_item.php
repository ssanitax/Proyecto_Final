<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
require_once '../../config/config.php';

// Capturamos el ID del registro en la colección
$id_coleccion = $_GET['id'] ?? null;

// 1. Obtener los datos actuales del juego en la estantería del usuario
$stmt = $pdo->prepare("
    SELECT cu.*, j.titulo, e.edicion_nombre, p.nombre as plataforma
    FROM coleccion_usuario cu
    JOIN ediciones e ON cu.edicion_id = e.id
    JOIN juegos j ON e.juego_id = j.id
    JOIN plataformas p ON e.plataforma_id = p.id
    WHERE cu.id = ? AND cu.usuario_id = ?
");
$stmt->execute([$id_coleccion, $_SESSION['usuario_id']]);
$item = $stmt->fetch();

if (!$item) {
    die("Juego no encontrado en tu colección.");
}

include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 600px; margin: 0 auto;">
    <header style="text-align: center; margin-bottom: 30px;">
        <h2>Gestionar Juego</h2>
        <p style="color: #666;">Actualiza el estado de <strong><?php echo htmlspecialchars($item->titulo); ?></strong></p>
    </header>

    <div class="about-box">
        <form action="../../controllers/ColeccionController.php?action=actualizar" method="POST">
            <input type="hidden" name="id" value="<?php echo $item->id; ?>">

            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;">ESTADO ACTUAL</label>
                <select name="estado" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
                    <option value="pendiente" <?php echo $item->estado == 'pendiente' ? 'selected' : ''; ?>>Pendiente (En bibioteca)</option>
                    <option value="jugando" <?php echo $item->estado == 'jugando' ? 'selected' : ''; ?>>Jugando ahora</option>
                    <option value="completado" <?php echo $item->estado == 'completado' ? 'selected' : ''; ?>>Completado / Terminado</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;">VALORACIÓN (1-10)</label>
                <input type="number" name="valoracion" min="1" max="10" value="<?php echo $item->valoracion_personal; ?>" 
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
            </div>

            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;">NOTAS PERSONALES</label>
                <textarea name="notas" rows="4" style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit; resize: none;"><?php echo htmlspecialchars($item->notas); ?></textarea>
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="submit" class="btn-dash" style="flex: 2; border: none; cursor: pointer;">Guardar cambios</button>
                <a href="../../controllers/ColeccionController.php?action=eliminar&id=<?php echo $item->id; ?>" 
                   style="flex: 1; background: #fee2e2; color: #991b1b; text-decoration: none; display: flex; align-items: center; justify-content: center; border-radius: 50px; font-weight: 600; font-size: 0.85rem;"
                   onclick="return confirm('¿Seguro que quieres eliminar este juego de tu estantería?')">
                    Eliminar
                </a>
            </div>
        </form>
    </div>
</div>

<div class="fade-up visible" style="max-width: 600px; margin: 30px auto;">
    <div class="about-box" style="border-top: 4px solid var(--graphite);">
        <h3 style="font-size: 1.1rem; margin-bottom: 20px; text-align: left;">🤝 Prestar este juego</h3>
        <form action="../../controllers/PrestamoController.php?action=registrar" method="POST">
            <input type="hidden" name="coleccion_id" value="<?php echo $item->id; ?>">
            
            <div class="form-group" style="margin-bottom: 15px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.75rem; display: block; margin-bottom: 8px;">¿A QUIÉN SE LO PRESTAS?</label>
                <input type="text" name="nombre_persona" placeholder="Nombre de tu amigo" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
            </div>

            <div class="form-group" style="margin-bottom: 15px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.75rem; display: block; margin-bottom: 8px;">FECHA DEL PRÉSTAMO</label>
                <input type="date" name="fecha_prestamo" value="<?php echo date('Y-m-d'); ?>" required 
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd;">
            </div>

            <button type="submit" class="btn-dash" style="width: 100%; background: #ebf5ff; color: #007bff; border: 1px solid #007bff;">
                Registrar Préstamo
            </button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
