<?php
require_once '../../includes/auth.php';
redirigirSiNoUsuario();
include '../../includes/header.php';
?>

<div class="fade-up visible" style="max-width: 600px; margin: 0 auto;">
    <header style="text-align: center; margin-bottom: 30px;">
        <h2>Sugerir Nueva Plataforma</h2>
        <p style="color: #666;">¿Falta alguna consola en Bengala? Pídela aquí.</p>
    </header>

    <div class="about-box" style="padding: 40px; background: white; border-radius: 20px; border: 1px solid #eee;">
        <form action="../../controllers/JuegoController.php?action=sugerir_plataforma_independiente" method="POST">
            <div class="form-group" style="margin-bottom: 25px; text-align: left;">
                <label style="font-weight: 800; font-size: 0.8rem; display: block; margin-bottom: 10px;">NOMBRE DE LA CONSOLA / PLATAFORMA</label>
                
                <input type="text" name="nombre_plataforma" placeholder="Ej: PlayStation 5, Sega Saturn..." required 
                       style="width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #ddd; font-family: inherit;">
            </div>
            
            <button type="submit" class="btn-confirm" style="width:100%; border:none; cursor:pointer; background:var(--graphite); color:white; padding:15px; border-radius:50px; font-weight:700; text-transform: uppercase;">
                Enviar Sugerencia
            </button>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php';?>