-- Idioma y portada opcional en propuestas de juego/edición
ALTER TABLE ediciones_pendientes
    ADD COLUMN idioma_id INT NULL AFTER plataforma_id,
    ADD COLUMN imagen_portada_sugerida VARCHAR(255) NULL AFTER edicion_nombre;

ALTER TABLE ediciones_pendientes
    ADD CONSTRAINT fk_ediciones_pendientes_idioma
    FOREIGN KEY (idioma_id) REFERENCES idiomas(id) ON DELETE SET NULL;
