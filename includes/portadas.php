<?php

function directorioPortadas() {
    return realpath(__DIR__ . '/../img/portadas') ?: (__DIR__ . '/../img/portadas');
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
    $ext = strtolower(preg_replace('/[^a-z0-9]/', '', $extension));
    return $base . '.' . ($ext !== '' ? $ext : 'jpg');
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
