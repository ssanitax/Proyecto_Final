-- Catálogo maestro de regiones (admin + propuestas de usuarios)
CREATE TABLE IF NOT EXISTS regiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    CONSTRAINT unique_region_nombre UNIQUE (nombre)
);

INSERT IGNORE INTO regiones (nombre)
SELECT DISTINCT region FROM ediciones
WHERE region IS NOT NULL AND region != '';
