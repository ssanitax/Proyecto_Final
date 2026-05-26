-- Bloqueo regional en ediciones del catálogo (no solo en propuestas pendientes)
ALTER TABLE ediciones
    ADD COLUMN bloqueo_regional TINYINT(1) NOT NULL DEFAULT 0 AFTER region;
