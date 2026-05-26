<?php

/**
 * Todos los idiomas del catálogo (el usuario elige uno al guardar su copia en la estantería).
 */
function todosLosIdiomas(PDO $pdo): array {
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
