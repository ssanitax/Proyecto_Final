<?php

function directorioPortadas() {
    return realpath(__DIR__ . '/../img/portadas') ?: (__DIR__ . '/../img/portadas');
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
