-- Ejecutar una vez en la base de datos existente
ALTER TABLE ediciones_pendientes
    ADD COLUMN idioma_nombre_nueva VARCHAR(100) NULL AFTER plataforma_nombre_nueva;
