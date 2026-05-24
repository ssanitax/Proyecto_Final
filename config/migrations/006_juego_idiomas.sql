-- Idiomas del cartucho/disco asociados al juego en catálogo (varios por juego)
CREATE TABLE IF NOT EXISTS juego_idiomas (
    juego_id INT NOT NULL,
    idioma_id INT NOT NULL,
    PRIMARY KEY (juego_id, idioma_id),
    FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE,
    FOREIGN KEY (idioma_id) REFERENCES idiomas(id) ON DELETE CASCADE
);
