-- Ejecutar una vez en la base de datos existente
ALTER TABLE ediciones_pendientes
    ADD COLUMN bloqueo_regional TINYINT(1) NOT NULL DEFAULT 0
    AFTER region;
