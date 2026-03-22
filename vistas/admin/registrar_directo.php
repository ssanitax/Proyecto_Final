<?php
require_once '../../includes/auth.php';
redirigirSiNoAdmin();
require_once '../../config/config.php';

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
</style>

<div class="fade-up visible">
    <header style="text-align: center; margin-bottom: 50px;">
        <h2>Gestión Maestra de Contenido 🏗️</h2>
        <p style="color: #666;">Añade elementos oficiales al sistema de forma inmediata.</p>
    </header>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 12px; text-align: center; margin-bottom: 30px; font-weight: 600; border: 1px solid #a7f3d0;">
            ¡Registro completado con éxito en la base de datos oficial! ✅
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; max-width: 1100px; margin: 0 auto;">
        
        <div class="dash-card">
            <h3 style="margin-bottom: 20px; font-weight: 800;">🎮 Nueva Plataforma</h3>
            <form action="../../controllers/AdminController.php?action=registrar_plataforma" method="POST">
                <input type="text" name="nombre" placeholder="Ej: PlayStation 5, Xbox..." required>
                <button type="submit" class="btn-dash">Guardar Sistema</button>
            </form>
        </div>

        <div class="dash-card">
            <h3 style="margin-bottom: 20px; font-weight: 800;">🌍 Nueva Región</h3>
            <form action="../../controllers/AdminController.php?action=registrar_region" method="POST">
                <input type="text" name="nombre" placeholder="Ej: NTSC-J, PAL-ESP..." required>
                <button type="submit" class="btn-dash">Guardar Región</button>
            </form>
        </div>

        <div class="dash-card">
            <h3 style="margin-bottom: 20px; font-weight: 800;">📦 Nuevo Título Maestro</h3>
            <form action="../../controllers/AdminController.php?action=registrar_juego" method="POST">
                <input type="text" name="titulo" placeholder="Título del juego" required>
                <input type="text" name="desarrollador" placeholder="Empresa / Desarrollador">
                <button type="submit" class="btn-dash">Guardar Título</button>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/admin_footer.php'; ?>