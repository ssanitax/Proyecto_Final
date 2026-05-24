-- Ejecutar una vez en la base de datos existente

CREATE TABLE IF NOT EXISTS idiomas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    CONSTRAINT unique_idioma_nombre UNIQUE (nombre)
);

INSERT IGNORE INTO idiomas (nombre) VALUES
('Español'),
('Inglés'),
('Francés'),
('Alemán'),
('Italiano'),
('Japonés');

ALTER TABLE coleccion_usuario
    ADD COLUMN idioma_id INT NULL AFTER edicion_id,
    ADD CONSTRAINT fk_coleccion_idioma
        FOREIGN KEY (idioma_id) REFERENCES idiomas(id) ON DELETE SET NULL;
