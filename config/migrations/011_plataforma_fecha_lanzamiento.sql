-- Fecha de lanzamiento de la consola (para elegir portada principal más reciente)
ALTER TABLE plataformas
    ADD COLUMN fecha_lanzamiento DATE NULL AFTER nombre;

ALTER TABLE ediciones_pendientes
    ADD COLUMN fecha_plataforma_sugerida DATE NULL AFTER plataforma_nombre_nueva;
