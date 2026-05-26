-- Ejecutar una vez en la base de datos existente (propuestas)
ALTER TABLE ediciones_pendientes
    ADD COLUMN bloqueo_regional TINYINT(1) NOT NULL DEFAULT 0
    AFTER region;

-- Catálogo maestro: ver también 013_ediciones_bloqueo_regional.sql
