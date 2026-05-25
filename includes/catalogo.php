<?php

/**
 * Idiomas que el usuario puede elegir al añadir una copia.
 * Si el juego no tiene filas en juego_idiomas, se ofrecen todos los del catálogo.
 */
function idiomasDisponiblesParaJuego(PDO $pdo, int $juegoId): array {
    try {
        $stmt = $pdo->prepare("
            SELECT i.id, i.nombre
            FROM idiomas i
            INNER JOIN juego_idiomas ji ON ji.idioma_id = i.id
            WHERE ji.juego_id = ?
            ORDER BY i.nombre ASC
        ");
        $stmt->execute([$juegoId]);
        $rows = $stmt->fetchAll();
        if (!empty($rows)) {
            return $rows;
        }
    } catch (PDOException $e) {
        // juego_idiomas puede no existir aún
    }

    try {
        return $pdo->query("SELECT id, nombre FROM idiomas ORDER BY nombre ASC")->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

function regionesParaSelector(PDO $pdo): array {
    try {
        return $pdo->query("SELECT nombre FROM regiones ORDER BY nombre ASC")->fetchAll();
    } catch (PDOException $e) {
        $legacy = $pdo->query(
            "SELECT DISTINCT region AS nombre FROM ediciones
             WHERE region IS NOT NULL AND region != ''
             ORDER BY region ASC"
        )->fetchAll();
        return $legacy;
    }
}

function guardarIdiomasPropuestaPendiente(PDO $pdo, int $juegoPendienteId, array $idiomasIds): void {
    try {
        $pdo->prepare("DELETE FROM juegos_pendientes_idiomas WHERE juego_pendiente_id = ?")
            ->execute([$juegoPendienteId]);
        if (empty($idiomasIds)) {
            return;
        }
        $stmt = $pdo->prepare(
            "INSERT IGNORE INTO juegos_pendientes_idiomas (juego_pendiente_id, idioma_id) VALUES (?, ?)"
        );
        foreach ($idiomasIds as $idiomaId) {
            $idiomaId = (int)$idiomaId;
            if ($idiomaId > 0) {
                $stmt->execute([$juegoPendienteId, $idiomaId]);
            }
        }
    } catch (PDOException $e) {
        // Tabla aún no migrada
    }
}

function idiomasPropuestaPendiente(PDO $pdo, int $juegoPendienteId): array {
    try {
        $stmt = $pdo->prepare("
            SELECT idioma_id FROM juegos_pendientes_idiomas WHERE juego_pendiente_id = ?
        ");
        $stmt->execute([$juegoPendienteId]);
        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    } catch (PDOException $e) {
        return [];
    }
}

function sincronizarIdiomasJuego(PDO $pdo, int $juegoId, array $idiomasIds): void {
    if ($juegoId <= 0) {
        return;
    }
    try {
        $idiomasIds = array_unique(array_filter(array_map('intval', $idiomasIds)));
        if (empty($idiomasIds)) {
            $todos = $pdo->query("SELECT id FROM idiomas")->fetchAll(PDO::FETCH_COLUMN);
            $idiomasIds = array_map('intval', $todos);
        }
        $pdo->prepare("DELETE FROM juego_idiomas WHERE juego_id = ?")->execute([$juegoId]);
        $stmt = $pdo->prepare("INSERT IGNORE INTO juego_idiomas (juego_id, idioma_id) VALUES (?, ?)");
        foreach ($idiomasIds as $idiomaId) {
            if ($idiomaId > 0) {
                $stmt->execute([$juegoId, $idiomaId]);
            }
        }
    } catch (PDOException $e) {
        // Tabla juego_idiomas puede no existir
    }
}
