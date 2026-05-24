<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/auth.php';

class AdminController {
    private $pdo;

    public function __construct($pdo) {
        // Seguridad: Solo administradores
        if (!esAdmin()) {
            header('Location: ../vistas/fronted/dashboard.php');
            exit();
        }
        $this->pdo = $pdo;
    }

    /**
     * APROBAR PROPUESTA: Maneja Juegos, Ediciones y Plataformas nuevas
     */
    public function aprobarPropuesta() {
        $id_pendiente = $_GET['id'] ?? null;
        if (!$id_pendiente) {
            header('Location: ../vistas/admin/validar_juegos.php');
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Obtener la propuesta completa
            $stmt = $this->pdo->prepare("
                SELECT jp.*, ep.plataforma_id, ep.region, ep.bloqueo_regional, ep.edicion_nombre, ep.juego_id_real, ep.plataforma_nombre_nueva, ep.idioma_nombre_nueva
                FROM juegos_pendientes jp
                LEFT JOIN ediciones_pendientes ep ON ep.juego_pendiente_id = jp.id
                WHERE jp.id = ?
            ");
            $stmt->execute([$id_pendiente]);
            $propuesta = $stmt->fetch();

            if (!$propuesta) throw new Exception("Propuesta no encontrada.");

            // 2. Capturar correcciones del Admin (vienen por POST)
            $tituloCorregido = $_POST['corregir_titulo'] ?? $propuesta->titulo;
            $devCorregido    = $_POST['corregir_dev']    ?? $propuesta->desarrollador;
            $regionCorregida = trim($_POST['corregir_region'] ?? $propuesta->region ?? '');
            $platNombre      = $_POST['corregir_plataforma'] ?? null;
            $idiomaNombre    = trim($_POST['corregir_idioma'] ?? '');

            if (!empty($propuesta->bloqueo_regional) && $regionCorregida === '') {
                throw new Exception(__('admin_validate_region_required'));
            }

            // 2b. GESTIÓN DE IDIOMA (propuesta de nuevo idioma)
            if ($idiomaNombre === '' && !empty($propuesta->idioma_nombre_nueva)) {
                $idiomaNombre = trim($propuesta->idioma_nombre_nueva);
            }
            if ($idiomaNombre === '' && strpos($tituloCorregido, 'Idioma:') === 0) {
                $idiomaNombre = trim(str_replace('Idioma:', '', $tituloCorregido));
            }

            if ($idiomaNombre !== '' && (strpos($tituloCorregido, 'Idioma:') === 0 || !empty($propuesta->idioma_nombre_nueva))) {
                $stBuscaIdioma = $this->pdo->prepare("SELECT id FROM idiomas WHERE nombre = ?");
                $stBuscaIdioma->execute([$idiomaNombre]);
                if (!$stBuscaIdioma->fetch()) {
                    $stInsIdioma = $this->pdo->prepare("INSERT INTO idiomas (nombre) VALUES (?)");
                    $stInsIdioma->execute([$idiomaNombre]);
                }
            }

            // 3. GESTIÓN DE LA PLATAFORMA (Crucial para que aparezca en el catálogo)
            $plataforma_id_final = $propuesta->plataforma_id;

            if (!empty($platNombre)) {
                // Buscamos si ya existe por nombre (para evitar duplicados)
                $stBusca = $this->pdo->prepare("SELECT id FROM plataformas WHERE nombre = ?");
                $stBusca->execute([trim($platNombre)]);
                $existe = $stBusca->fetch();

                if ($existe) {
                    $plataforma_id_final = $existe->id;
                } else {
                    // SI NO EXISTE, LA CREAMOS AHORA MISMO
                    $stIns = $this->pdo->prepare("INSERT INTO plataformas (nombre) VALUES (?)");
                    $stIns->execute([trim($platNombre)]);
                    $plataforma_id_final = $this->pdo->lastInsertId();
                }
            }

            // 4. LÓGICA DE JUEGO / EDICIÓN
            if (strpos($tituloCorregido, 'Plataforma:') === 0 || strpos($tituloCorregido, 'Región:') === 0 || strpos($tituloCorregido, 'Idioma:') === 0) {
                // Propuesta solo de plataforma, región o idioma
            } else {
                $tituloLimpio = trim(str_replace('Juego: ', '', $tituloCorregido));
                $juego_id_final = $propuesta->juego_id_real;

                if (!$juego_id_final) {
                    // Crear juego nuevo si no existía
                    $sqlJ = "INSERT INTO juegos (titulo, desarrollador) VALUES (?, ?)";
                    $stmtJ = $this->pdo->prepare($sqlJ);
                    $stmtJ->execute([$tituloLimpio, $devCorregido]);
                    $juego_id_final = $this->pdo->lastInsertId();
                }

                // Crear la edición oficial vinculada
                if ($juego_id_final && $plataforma_id_final) {
                    $sqlE = "INSERT INTO ediciones (juego_id, plataforma_id, region, edicion_nombre) VALUES (?, ?, ?, ?)";
                    $this->pdo->prepare($sqlE)->execute([
                        $juego_id_final,
                        $plataforma_id_final,
                        $regionCorregida !== '' ? $regionCorregida : null,
                        $propuesta->edicion_nombre ?? 'Edición Estándar'
                    ]);
                }
            }

            // 5. Marcar como aprobado
            $stmtUpdate = $this->pdo->prepare("UPDATE juegos_pendientes SET estado = 'aprobado', revisado_por = ?, fecha_revision = NOW() WHERE id = ?");
            $stmtUpdate->execute([$_SESSION['usuario_id'], $id_pendiente]);

            $this->pdo->commit();
            header('Location: ../vistas/admin/validar_juegos.php?status=aprobado');
            exit();

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            die($lang['error_validation'] . $e->getMessage());
        }
    }

    public function rechazarPropuesta() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("UPDATE juegos_pendientes SET estado = 'rechazado', revisado_por = ?, fecha_revision = NOW() WHERE id = ?");
            $stmt->execute([$_SESSION['usuario_id'], $id]);
        }
        header('Location: ../vistas/admin/validar_juegos.php?status=rechazado');
        exit();
    }

    // --- ACCIONES DE REGISTRO DIRECTO ---

    public function registrarPlataformaDirecta() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = htmlspecialchars(trim($_POST['nombre']));
            try {
                $stmt = $this->pdo->prepare("INSERT INTO plataformas (nombre) VALUES (?)");
                $stmt->execute([$nombre]);
                header('Location: ../vistas/admin/registrar_directo.php?status=success');
            } catch (Exception $e) {
                header('Location: ../vistas/admin/registrar_directo.php?error=exists');
            }
            exit();
        }
    }

    public function registrarJuegoDirecto() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $titulo = htmlspecialchars(trim($_POST['titulo']));
            $dev = htmlspecialchars(trim($_POST['desarrollador']));
            $stmt = $this->pdo->prepare("INSERT INTO juegos (titulo, desarrollador) VALUES (?, ?)");
            $stmt->execute([$titulo, $dev]);
            header('Location: ../vistas/admin/registrar_directo.php?status=success');
            exit();
        }
    }

    public function registrarIdiomaDirecto() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = htmlspecialchars(trim($_POST['nombre']));
            try {
                $stmt = $this->pdo->prepare("INSERT INTO idiomas (nombre) VALUES (?)");
                $stmt->execute([$nombre]);
                header('Location: ../vistas/admin/registrar_directo.php?status=success');
            } catch (Exception $e) {
                header('Location: ../vistas/admin/registrar_directo.php?error=exists');
            }
            exit();
        }
    }

    public function subirPortadaJuego() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ../vistas/admin/registrar_directo.php');
            exit();
        }

        $juegoId = isset($_POST['juego_id']) ? (int)$_POST['juego_id'] : 0;

        if ($juegoId <= 0) {
            header('Location: ../vistas/admin/registrar_directo.php?cover_error=invalid_game');
            exit();
        }

        if (!isset($_FILES['portada']) || $_FILES['portada']['error'] !== UPLOAD_ERR_OK) {
            header('Location: ../vistas/admin/registrar_directo.php?cover_error=upload');
            exit();
        }

        $tmpPath = $_FILES['portada']['tmp_name'];
        $originalName = $_FILES['portada']['name'];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($tmpPath);

        $allowedMimes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp'
        ];

        if (!isset($allowedMimes[$mimeType])) {
            header('Location: ../vistas/admin/registrar_directo.php?cover_error=type');
            exit();
        }

        $ext = $allowedMimes[$mimeType];
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);
        $safeBase = trim($safeBase, '_');
        if ($safeBase === '') {
            $safeBase = 'cover';
        }

        $fileName = $safeBase . '_' . time() . '.' . $ext;
        $uploadDir = __DIR__ . '/../img/portadas';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            header('Location: ../vistas/admin/registrar_directo.php?cover_error=filesystem');
            exit();
        }

        $destPath = $uploadDir . '/' . $fileName;

        if (!move_uploaded_file($tmpPath, $destPath)) {
            header('Location: ../vistas/admin/registrar_directo.php?cover_error=filesystem');
            exit();
        }

        $stmt = $this->pdo->prepare("UPDATE ediciones SET imagen_portada = ? WHERE juego_id = ?");
        $stmt->execute([$fileName, $juegoId]);

        header('Location: ../vistas/admin/registrar_directo.php?cover_status=success');
        exit();
    }

    // --- NUEVAS ACCIONES DE BORRADO MAESTRO ---

    public function eliminarPlataforma() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            try {
                $this->pdo->beginTransaction();
                
                // Al ejecutar esto, la base de datos borrará automáticamente 
                // las ediciones asociadas debido al ON DELETE CASCADE.
                $stmt = $this->pdo->prepare("DELETE FROM plataformas WHERE id = ?");
                $stmt->execute([$id]);
                
                $this->pdo->commit();
                header('Location: ../vistas/admin/inventario_maestro.php?status=deleted');
            } catch (Exception $e) {
                $this->pdo->rollBack();
                die($lang['error_delete_platform'] . $e->getMessage());
            }
        } else {
            header('Location: ../vistas/admin/inventario_maestro.php');
        }
        exit();
    }

    public function eliminarEdicion() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM ediciones WHERE id = ?");
            $stmt->execute([$id]);
        }
        header('Location: ../vistas/admin/inventario_maestro.php?status=deleted');
        exit();
    }

    public function eliminarJuegoMaestro() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM juegos WHERE id = ?");
            $stmt->execute([$id]);
        }
        header('Location: ../vistas/admin/inventario_maestro.php?status=deleted');
        exit();
    }

    public function eliminarIdioma() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $this->pdo->prepare("DELETE FROM idiomas WHERE id = ?");
            $stmt->execute([$id]);
        }
        header('Location: ../vistas/admin/inventario_maestro.php?status=deleted');
        exit();
    }

    public function eliminarPorRegion() {
        $region = $_GET['nombre'] ?? null;
        if ($region) {
            try {
                $this->pdo->beginTransaction();
                
                // 1. Borrar ediciones oficiales de esa región (Limpieza de catálogo)
                // Esto dispara el CASCADE hacia coleccion_usuario y prestamos [cite: 779, 782, 785]
                $stmt1 = $this->pdo->prepare("DELETE FROM ediciones WHERE region = ?");
                $stmt1->execute([$region]);

                // 2. Borrar propuestas de ediciones de usuarios que tengan esa región
                $stmt2 = $this->pdo->prepare("DELETE FROM ediciones_pendientes WHERE region = ?");
                $stmt2->execute([$region]);

                // 3. OPCIONAL: Si quieres borrar también la mención en el título de la propuesta maestra
                $stmt3 = $this->pdo->prepare("DELETE FROM juegos_pendientes WHERE titulo = ?");
                $stmt3->execute(["Región: " . $region]);
                
                $this->pdo->commit();
                header('Location: ../vistas/admin/inventario_maestro.php?status=deleted');
            } catch (Exception $e) {
                if ($this->pdo->inTransaction()) $this->pdo->rollBack();
                die($lang['error_delete_region'] . $e->getMessage());
            }
        }
        exit();
    }

    public function eliminarUsuario() {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: ../vistas/admin/gestionar_usuarios.php');
            exit();
        }

        // Impedir eliminar admin o a sí mismo
        $stmt = $this->pdo->prepare("SELECT id, rol FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $usuario = $stmt->fetch();

        if (!$usuario || $usuario->rol === 'admin') {
            header('Location: ../vistas/admin/gestionar_usuarios.php?error=cannot_delete');
            exit();
        }

        if (isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === (int)$id) {
            header('Location: ../vistas/admin/gestionar_usuarios.php?error=cannot_delete_self');
            exit();
        }

        $stmtDel = $this->pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmtDel->execute([$id]);

        header('Location: ../vistas/admin/gestionar_usuarios.php?status=deleted');
        exit();
    }
}


// Router
if (isset($_GET['action'])) {
    $admin = new AdminController($pdo);
    if ($_GET['action'] == 'aprobar') $admin->aprobarPropuesta();
    if ($_GET['action'] == 'rechazar') $admin->rechazarPropuesta();
    if ($_GET['action'] == 'registrar_plataforma') $admin->registrarPlataformaDirecta();
    if ($_GET['action'] == 'registrar_juego') $admin->registrarJuegoDirecto();
    if ($_GET['action'] == 'registrar_idioma') $admin->registrarIdiomaDirecto();
    if ($_GET['action'] == 'subir_portada_juego') $admin->subirPortadaJuego();
    if ($_GET['action'] == 'eliminar_idioma') $admin->eliminarIdioma();
    if ($_GET['action'] == 'eliminar_region') $admin->eliminarPorRegion();  
    
    // Rutas para el Inventario Maestro
    if ($_GET['action'] == 'eliminar_plataforma') $admin->eliminarPlataforma();
    if ($_GET['action'] == 'eliminar_edicion') $admin->eliminarEdicion();
    if ($_GET['action'] == 'eliminar_juego') $admin->eliminarJuegoMaestro();
    if ($_GET['action'] == 'eliminar_usuario') $admin->eliminarUsuario();
}