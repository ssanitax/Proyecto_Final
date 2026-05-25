<?php

function directorioPortadas() {
    $dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'img'
        . DIRECTORY_SEPARATOR . 'portadas';
    $resolved = realpath($dir);
    return $resolved !== false ? $resolved : $dir;
}

/**
 * Crea img/portadas si no existe y comprueba que el servidor pueda escribir.
 */
function asegurarDirectorioPortadas() {
    $dir = directorioPortadas();
    if (!is_dir($dir) && !@mkdir($dir, 0755, true) && !is_dir($dir)) {
        return false;
    }
    return is_dir($dir) && is_writable($dir);
}

/**
 * Nombre de archivo a partir del título: espacios → guiones bajos.
 */
function nombreArchivoPortadaDesdeTitulo($titulo, $extension) {
    $base = trim((string)$titulo);
    $base = preg_replace('/\s+/u', '_', $base);
    $base = preg_replace('/[\\\\\\/:\\*\\?\"<>\\|]/', '', $base);
    $base = preg_replace('/_+/', '_', $base);
    $base = trim($base, '._');
    if ($base === '') {
        $base = 'juego';
    }
    if (preg_match('/^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])$/i', $base)) {
        $base = 'juego_' . $base;
    }
    if (strlen($base) > 180) {
        $base = substr($base, 0, 180);
    }
    $ext = strtolower(preg_replace('/[^a-z0-9]/', '', $extension));
    return $base . '.' . ($ext !== '' ? $ext : 'jpg');
}

/**
 * Nombre de archivo por edición: título + plataforma (+ región).
 */
function nombreArchivoPortadaEdicion($titulo, $plataforma, $region, $extension, $edicionId = 0) {
    $partes = array_filter([trim((string)$titulo), trim((string)$plataforma), trim((string)$region)]);
    $base = implode(' ', $partes);
    $nombre = nombreArchivoPortadaDesdeTitulo($base, $extension);
    if ($edicionId > 0) {
        $ext = pathinfo($nombre, PATHINFO_EXTENSION);
        $stem = pathinfo($nombre, PATHINFO_FILENAME);
        $nombre = $stem . '_' . (int)$edicionId . '.' . $ext;
    }
    return $nombre;
}

/**
 * Subconsulta SQL: portada de la edición con lanzamiento más reciente del juego.
 */
function sqlSelectPortadaMasRecientePorJuego($columnaJuegoId = 'j.id') {
    return "(
        SELECT e.imagen_portada
        FROM ediciones e
        INNER JOIN juegos j2 ON j2.id = e.juego_id
        WHERE e.juego_id = {$columnaJuegoId}
          AND e.imagen_portada IS NOT NULL AND e.imagen_portada != ''
        ORDER BY COALESCE(e.anio, YEAR(j2.fecha_lanzamiento), 0) DESC, e.id DESC
        LIMIT 1
    )";
}

/**
 * Portada principal entre copias del usuario (edición con lanzamiento más reciente).
 */
function portadaMasRecienteEntreCopias(array $copias) {
    $mejor = null;
    $mejorClave = -1;
    foreach ($copias as $copia) {
        if (empty($copia->imagen_portada)) {
            continue;
        }
        $anio = isset($copia->anio) && $copia->anio !== null && $copia->anio !== ''
            ? (int)$copia->anio
            : 0;
        if ($anio <= 0 && !empty($copia->fecha_lanzamiento)) {
            $anio = (int)date('Y', strtotime($copia->fecha_lanzamiento));
        }
        $clave = $anio * 100000 + (int)($copia->edicion_id ?? $copia->id ?? 0);
        if ($clave > $mejorClave) {
            $mejorClave = $clave;
            $mejor = $copia->imagen_portada;
        }
    }
    return $mejor;
}

/**
 * Si el juego no tiene ninguna edición, crea una estándar para poder guardar la portada.
 */
function asegurarEdicionParaPortada($pdo, $juegoId) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ediciones WHERE juego_id = ?");
    $stmt->execute([(int)$juegoId]);
    if ((int)$stmt->fetchColumn() > 0) {
        return true;
    }

    $platId = $pdo->query("SELECT id FROM plataformas ORDER BY nombre ASC LIMIT 1")->fetchColumn();
    if (!$platId) {
        return false;
    }

    $ins = $pdo->prepare(
        "INSERT INTO ediciones (juego_id, plataforma_id, edicion_nombre) VALUES (?, ?, 'Edición Estándar')"
    );
    return $ins->execute([(int)$juegoId, (int)$platId]);
}

/**
 * Rutas de portada distintas usadas por ediciones que coinciden con el filtro.
 * $whereSql solo condiciones con alias e (ej: "e.juego_id = ?").
 */
function obtenerPortadasPorFiltroEdiciones($pdo, $whereSql, array $params) {
    $sql = "SELECT DISTINCT e.imagen_portada
            FROM ediciones e
            WHERE e.imagen_portada IS NOT NULL AND e.imagen_portada != ''
              AND ($whereSql)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

/**
 * Elimina el archivo en disco si ninguna edición del catálogo lo referencia.
 */
function eliminarArchivoPortadaSiHuerfano($pdo, $nombreArchivo) {
    if ($nombreArchivo === null || $nombreArchivo === '') {
        return;
    }

    $nombreArchivo = basename($nombreArchivo);
    $stmt = $pdo->prepare(
        "SELECT COUNT(*) FROM ediciones WHERE imagen_portada = ?"
    );
    $stmt->execute([$nombreArchivo]);
    if ((int)$stmt->fetchColumn() > 0) {
        return;
    }

    $ruta = directorioPortadas() . DIRECTORY_SEPARATOR . $nombreArchivo;
    if (is_file($ruta)) {
        @unlink($ruta);
    }
}

/**
 * Tras borrar ediciones en BD, intenta eliminar cada archivo de portada listado.
 */
function limpiarArchivosPortadaLista($pdo, array $nombresArchivo) {
    foreach (array_unique(array_filter($nombresArchivo)) as $nombre) {
        eliminarArchivoPortadaSiHuerfano($pdo, $nombre);
    }
}

/**
 * Borra en disco las imágenes de img/portadas que ya no están en ninguna edición.
 */
/**
 * Elimina filas de juegos que ya no tienen ninguna edición.
 */
function eliminarJuegosSinEdicionesEnBd($pdo) {
    $stmt = $pdo->prepare(
        "DELETE j FROM juegos j
         LEFT JOIN ediciones e ON e.juego_id = j.id
         WHERE e.id IS NULL"
    );
    $stmt->execute();
    return (int)$stmt->rowCount();
}

/**
 * Tras borrar ediciones, elimina el juego maestro si ha quedado vacío.
 */
function eliminarJuegoSiQuedoSinEdiciones($pdo, $juegoId) {
    $juegoId = (int)$juegoId;
    if ($juegoId <= 0) {
        return false;
    }
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM ediciones WHERE juego_id = ?");
    $stmt->execute([$juegoId]);
    if ((int)$stmt->fetchColumn() > 0) {
        return false;
    }
    $del = $pdo->prepare("DELETE FROM juegos WHERE id = ?");
    $del->execute([$juegoId]);
    return $del->rowCount() > 0;
}

function limpiarTodasPortadasHuerfanasEnDisco($pdo) {
    $dir = directorioPortadas();
    if (!is_dir($dir)) {
        return 0;
    }

    $stmt = $pdo->query(
        "SELECT DISTINCT imagen_portada FROM ediciones
         WHERE imagen_portada IS NOT NULL AND imagen_portada != ''"
    );
    $enUso = [];
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $nombre) {
        $enUso[basename($nombre)] = true;
    }

    $eliminados = 0;
    foreach (glob($dir . '/*') as $ruta) {
        if (!is_file($ruta)) {
            continue;
        }
        $base = basename($ruta);
        if (!isset($enUso[$base]) && @unlink($ruta)) {
            $eliminados++;
        }
    }
    return $eliminados;
}

/**
 * Guarda una imagen opcional de propuesta (usuario o admin). Devuelve el nombre de archivo o null.
 */
function guardarImagenPortadaOpcional(array $archivo, $prefijo = 'propuesta') {
    if (!isset($archivo['error']) || $archivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if (!asegurarDirectorioPortadas()) {
        return null;
    }

    $tmpPath = $archivo['tmp_name'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($tmpPath);
    $allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowedMimes[$mimeType])) {
        return null;
    }

    $ext = $allowedMimes[$mimeType];
    $nombre = nombreArchivoPortadaDesdeTitulo($prefijo . '_' . time(), $ext);
    $destPath = directorioPortadas() . DIRECTORY_SEPARATOR . $nombre;

    if (move_uploaded_file($tmpPath, $destPath) || @copy($tmpPath, $destPath)) {
        return $nombre;
    }
    return null;
}

/**
 * Asigna portada a una edición: prioridad subida admin, luego imagen sugerida por el usuario.
 */
function asignarPortadaAEdicion($pdo, $edicionId, $titulo, $plataforma, $region, $archivoAdmin, $portadaSugerida = null) {
    $edicionId = (int)$edicionId;
    if ($edicionId <= 0) {
        return;
    }

    $portadasAnteriores = obtenerPortadasPorFiltroEdiciones($pdo, 'e.id = ?', [$edicionId]);
    $fileName = null;

    if (isset($archivoAdmin['error']) && $archivoAdmin['error'] === UPLOAD_ERR_OK) {
        $fileName = guardarImagenPortadaOpcional($archivoAdmin, 'catalogo');
        if ($fileName) {
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $final = nombreArchivoPortadaEdicion($titulo, $plataforma, $region, $ext, $edicionId);
            $dir = directorioPortadas();
            $origen = $dir . DIRECTORY_SEPARATOR . $fileName;
            $destino = $dir . DIRECTORY_SEPARATOR . $final;
            if (is_file($origen) && @rename($origen, $destino)) {
                $fileName = $final;
            }
        }
    }

    if ($fileName === null && $portadaSugerida !== null && $portadaSugerida !== '') {
        $portadaSugerida = basename($portadaSugerida);
        $dir = directorioPortadas();
        $origen = $dir . DIRECTORY_SEPARATOR . $portadaSugerida;
        if (is_file($origen)) {
            $ext = pathinfo($portadaSugerida, PATHINFO_EXTENSION) ?: 'jpg';
            $fileName = nombreArchivoPortadaEdicion($titulo, $plataforma, $region, $ext, $edicionId);
            if (is_file($dir . DIRECTORY_SEPARATOR . $fileName)) {
                @unlink($dir . DIRECTORY_SEPARATOR . $fileName);
            }
            @copy($origen, $dir . DIRECTORY_SEPARATOR . $fileName);
        }
    }

    if ($fileName !== null) {
        $stmt = $pdo->prepare('UPDATE ediciones SET imagen_portada = ? WHERE id = ?');
        $stmt->execute([$fileName, $edicionId]);
        limpiarArchivosPortadaLista($pdo, $portadasAnteriores);
        if ($portadaSugerida && basename($portadaSugerida) !== $fileName) {
            eliminarArchivoPortadaSiHuerfano($pdo, $portadaSugerida);
        }
    }
}
