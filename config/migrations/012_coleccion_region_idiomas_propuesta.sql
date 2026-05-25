-- Región de la copia del usuario (cuando la edición tiene bloqueo regional)
ALTER TABLE coleccion_usuario
    ADD COLUMN region VARCHAR(50) NULL AFTER idioma_id;

-- Idiomas disponibles indicados en una propuesta de juego
CREATE TABLE IF NOT EXISTS juegos_pendientes_idiomas (
    juego_pendiente_id INT NOT NULL,
    idioma_id INT NOT NULL,
    PRIMARY KEY (juego_pendiente_id, idioma_id),
    FOREIGN KEY (juego_pendiente_id) REFERENCES juegos_pendientes(id) ON DELETE CASCADE,
    FOREIGN KEY (idioma_id) REFERENCES idiomas(id) ON DELETE CASCADE
);
