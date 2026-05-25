/* CREACIÓN DE BASE DE DATOS */

CREATE DATABASE proyectofinal;

USE proyectofinal;


/* TABLA: USUARIOS
   - Gestiona usuarios normales y administradores
   - Email que no permite duplicados
   - Login único que luego separa en front y back */

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL,

    email VARCHAR(150) NOT NULL,

    password VARCHAR(255) NOT NULL,

    rol ENUM('usuario', 'admin', 'super_admin') NOT NULL DEFAULT 'usuario',

    activo BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT unique_email UNIQUE (email)
);

CREATE INDEX idx_usuario_rol ON usuarios(rol);


/* TABLA: PLATAFORMAS
   - Consolas o sistemas (PS2, Switch, etc.), que más adelante se podrán meter más */

CREATE TABLE plataformas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    fecha_lanzamiento DATE NULL
);


/* TABLA: IDIOMAS
   - Idiomas del cartucho/disco (gestionados por admin) */

CREATE TABLE idiomas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);


/* TABLA: REGIONES
   - Códigos de región del catálogo (PAL, NTSC-J, etc.) */

CREATE TABLE regiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);


/* TABLA: JUEGOS 
   - Juego como concepto general */

CREATE TABLE juegos (
    id INT AUTO_INCREMENT PRIMARY KEY,

    titulo VARCHAR(255) NOT NULL,

    descripcion TEXT,

    fecha_lanzamiento DATE,

    desarrollador VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_juego_titulo ON juegos(titulo);


/* TABLA: JUEGO_IDIOMAS
   - Idiomas del cartucho/disco del juego en catálogo (varios por juego) */

CREATE TABLE juego_idiomas (
    juego_id INT NOT NULL,
    idioma_id INT NOT NULL,
    PRIMARY KEY (juego_id, idioma_id),
    FOREIGN KEY (juego_id) REFERENCES juegos(id) ON DELETE CASCADE,
    FOREIGN KEY (idioma_id) REFERENCES idiomas(id) ON DELETE CASCADE
);


/* TABLA: EDICIONES
   - Versiones físicas concretas del juego
   - Diferencia región, año, edición especial, etc.  */

CREATE TABLE ediciones (
    id INT AUTO_INCREMENT PRIMARY KEY,

    juego_id INT NOT NULL,
    plataforma_id INT NOT NULL,

    region VARCHAR(50),
    anio INT,
    edicion_nombre VARCHAR(255),
    codigo_barras VARCHAR(100),
    imagen_portada VARCHAR(255),

    FOREIGN KEY (juego_id) 
        REFERENCES juegos(id) 
        ON DELETE CASCADE,

    FOREIGN KEY (plataforma_id) 
        REFERENCES plataformas(id) 
        ON DELETE CASCADE
);

CREATE INDEX idx_edicion_juego ON ediciones(juego_id);
CREATE INDEX idx_edicion_plataforma ON ediciones(plataforma_id);


/* TABLA: COLECCION_USUARIO
   - Juegos que un usuario tiene en su estantería
   - Un usuario puede tener varias copias de la misma edición */

CREATE TABLE coleccion_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT NOT NULL,
    edicion_id INT NOT NULL,
    idioma_id INT NULL,

    estado ENUM('pendiente', 'jugando', 'completado') 
        DEFAULT 'pendiente',

    estado_conservacion ENUM('nuevo', 'como_nuevo', 'bueno', 'usado', 'sin_caja') NULL DEFAULT NULL,

    valoracion_personal INT CHECK (valoracion_personal BETWEEN 1 AND 10),

    notas TEXT,

    fecha_adicion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) 
        ON DELETE CASCADE,

    FOREIGN KEY (edicion_id) 
        REFERENCES ediciones(id) 
        ON DELETE CASCADE,

    FOREIGN KEY (idioma_id)
        REFERENCES idiomas(id)
        ON DELETE SET NULL
);

CREATE INDEX idx_coleccion_usuario ON coleccion_usuario(usuario_id);


/* TABLA: VALORACIONES
   - Valoraciones globales de juegos
   - Un usuario solo puede valorar una vez cada juego */

CREATE TABLE valoraciones (
    id INT AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT NOT NULL,
    juego_id INT NOT NULL,

    puntuacion INT NOT NULL CHECK (puntuacion BETWEEN 1 AND 10),
    comentario TEXT,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) 
        ON DELETE CASCADE,

    FOREIGN KEY (juego_id) 
        REFERENCES juegos(id) 
        ON DELETE CASCADE,

    CONSTRAINT unique_usuario_juego UNIQUE (usuario_id, juego_id)
);

CREATE INDEX idx_valoraciones_juego ON valoraciones(juego_id);


/* TABLA: PRESTAMOS
   - Registro de préstamos de juegos
   - Se vincula a la copia concreta del usuario  */

CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,

    coleccion_id INT NOT NULL,

    nombre_persona VARCHAR(255) NOT NULL,

    fecha_prestamo DATE NOT NULL,
    fecha_devolucion DATE,

    devuelto BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (coleccion_id) 
        REFERENCES coleccion_usuario(id) 
        ON DELETE CASCADE
);

CREATE INDEX idx_prestamos_coleccion ON prestamos(coleccion_id);


/* =====================================================
   SISTEMA DE VALIDACIÓN POR ADMIN
===================================================== */


/* TABLA: JUEGOS_PENDIENTES
   - Juegos propuestos por usuarios
   - Deben ser aprobados, corregidos o rechazados por admin */

CREATE TABLE juegos_pendientes (
    id INT AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT NOT NULL,

    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    fecha_lanzamiento DATE,
    desarrollador VARCHAR(255),

    estado ENUM('pendiente', 'aprobado', 'rechazado') 
        DEFAULT 'pendiente',

    revisado_por INT,
    fecha_revision TIMESTAMP NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id)
        ON DELETE CASCADE,

    FOREIGN KEY (revisado_por) 
        REFERENCES usuarios(id)
        ON DELETE SET NULL
);

CREATE INDEX idx_juegos_pendientes_estado ON juegos_pendientes(estado);


/* TABLA: EDICIONES_PENDIENTES
   - Versiones físicas asociadas a juegos pendientes */

CREATE TABLE ediciones_pendientes (
    id INT AUTO_INCREMENT PRIMARY KEY,

    juego_pendiente_id INT NOT NULL,
    plataforma_id INT NOT NULL,

    region VARCHAR(50),
    bloqueo_regional TINYINT(1) NOT NULL DEFAULT 0,
    anio INT,
    edicion_nombre VARCHAR(255),

    FOREIGN KEY (juego_pendiente_id) 
        REFERENCES juegos_pendientes(id) 
        ON DELETE CASCADE,

    FOREIGN KEY (plataforma_id) 
        REFERENCES plataformas(id)
);

CREATE INDEX idx_ediciones_pendientes_juego 
ON ediciones_pendientes(juego_pendiente_id);


/* =====================================================
   USUARIO
===================================================== */

CREATE USER 'ana_sanchez'@'localhost' IDENTIFIED BY '3dleSLF$gl1FM';

GRANT ALL PRIVILEGES ON proyectofinal.* TO 'ana_sanchez'@'localhost';


/* =====================================================
   DATOS DE JUEGOS
===================================================== */

/* 1. INSERTAR PLATAFORMAS */
INSERT INTO plataformas (nombre) VALUES 
('Nintendo Switch'), 
('PlayStation 2'), 
('PlayStation 4'), 
('PC'),
('GameCube');

/* 1b. INSERTAR IDIOMAS */
INSERT INTO idiomas (nombre) VALUES
('Español'),
('Inglés'),
('Francés'),
('Alemán'),
('Italiano'),
('Japonés');

/* 1c. INSERTAR REGIONES */
INSERT INTO regiones (nombre) VALUES
('PAL'),
('NTSC-U'),
('NTSC-J'),
('Global');

/* 2. INSERTAR JUEGOS (MAESTROS) */
INSERT INTO juegos (titulo, desarrollador, fecha_lanzamiento, descripcion) VALUES 
('The Legend of Zelda: Breath of the Wild', 'Nintendo', '2017-03-03', 'Aventura de mundo abierto en el reino de Hyrule.'),
('Metal Gear Solid 2: Sons of Liberty', 'Konami', '2001-11-13', 'Acción y sigilo táctico en una plataforma marina.'),
('Elden Ring', 'FromSoftware', '2022-02-25', 'RPG de acción épico en las Tierras Intermedias.'),
('Final Fantasy VII', 'Square Enix', '1997-01-31', 'Clásico RPG que definió una generación.'),
('Silent Hill 2', 'Konami', '2001-09-24', 'Terror psicológico en la niebla de Silent Hill.'),
('The Last of Us Part II', 'Naughty Dog', '2020-06-19', 'Intensa historia de supervivencia y venganza.'),
('Super Mario Odyssey', 'Nintendo', '2017-10-27', 'Plataformas 3D recorriendo diversos reinos.'),
('God of War', 'Santa Monica Studio', '2018-04-20', 'Kratos regresa en una ambientación nórdica.'),
('Grand Theft Auto: San Andreas', 'Rockstar Games', '2004-10-26', 'Crimen y libertad en el estado de San Andreas.'),
('Shadow of the Colossus', 'Team Ico', '2005-10-18', 'Una gesta heroica para derrotar a gigantes colosos.'),
('Resident Evil 4', 'Capcom', '2005-01-11', 'Leon S. Kennedy rescata a la hija del presidente.'),
('The Witcher 3: Wild Hunt', 'CD Projekt Red', '2015-05-19', 'Geralt de Rivia busca a su protegida Ciri.'),
('Hollow Knight', 'Team Cherry', '2017-02-24', 'Metroidvania en un reino de insectos en ruinas.'),
('Red Dead Redemption 2', 'Rockstar Games', '2018-10-26', 'El fin de la era de los forajidos en el salvaje oeste.'),
('Metroid Prime', 'Retro Studios', '2002-11-18', 'Exploración en primera persona en el planeta Tallon IV.'),
('Kingdom Hearts II', 'Square Enix', '2005-12-22', 'Crossover de Disney y Square Enix.'),
('Dark Souls', 'FromSoftware', '2011-09-22', 'Difícil RPG de fantasía oscura y hogueras.'),
('Persona 5 Royal', 'Atlus', '2019-10-31', 'Vida escolar y combates por turnos en Tokio.'),
('Cyberpunk 2077', 'CD Projekt Red', '2020-12-10', 'Futuro distópico en la peligrosa Night City.'),
('Bloodborne', 'FromSoftware', '2015-03-24', 'Terror gótico y acción rápida en Yharnam.');

/* 3. INSERTAR EDICIONES (ESPECÍFICAS) */
INSERT INTO ediciones (juego_id, plataforma_id, region, anio, edicion_nombre, imagen_portada) VALUES 
(1, 1, 'PAL', 2017, 'Standard Edition', 'zelda_botw.jpg'),
(2, 2, 'PAL', 2001, 'Original Black Label', 'mgs2_ps2.jpg'),
(3, 3, 'PAL', 2022, 'Launch Edition', 'elden_ring_ps4.jpg'),
(4, 4, 'Global', 2020, 'Steam Version', 'ff7_pc.jpg'),
(5, 2, 'NTSC-U', 2001, 'Standard PS2', 'sh2_ps2.jpg'),
(6, 3, 'PAL', 2020, 'Steelbook Edition', 'tlou2_ps4.jpg'),
(7, 1, 'PAL', 2017, 'Standard Edition', 'mario_odyssey.jpg'),
(8, 3, 'PAL', 2018, 'PlayStation Hits', 'gow_ps4.jpg'),
(9, 2, 'PAL', 2004, 'Platinum Edition', 'gta_sa_ps2.jpg'),
(10, 2, 'NTSC-J', 2005, 'First Print', 'sotc_ps2.jpg'),
(11, 5, 'PAL', 2005, 'GameCube Original', 're4_gc.jpg'),
(12, 4, 'Global', 2015, 'Game of the Year Edition', 'witcher3_pc.jpg'),
(13, 1, 'PAL', 2019, 'Physical Edition', 'hollow_knight_switch.jpg'),
(14, 3, 'PAL', 2018, 'Special Edition', 'rdr2_ps4.jpg'),
(15, 5, 'PAL', 2002, 'Original GameCube', 'metroid_prime_gc.jpg'),
(16, 2, 'PAL', 2005, 'Standard PS2', 'kh2_ps2.jpg'),
(17, 3, 'PAL', 2018, 'Remastered', 'darksouls_ps4.jpg'),
(18, 3, 'PAL', 2020, 'Royal Steelbook', 'p5r_ps4.jpg'),
(19, 4, 'Global', 2020, 'Digital Code', 'cyberpunk_pc.jpg'),
(20, 3, 'PAL', 2015, 'Standard Edition', 'bloodborne_ps4.jpg');

-- Prueba de juego con dos plataformas
INSERT INTO ediciones (juego_id, plataforma_id, region, anio, edicion_nombre) 
VALUES (1, 4, 'Global', 2024, 'PC Digital Port');


-- Arreglo para permitir que ediciones pendientes puedan no tener un juego pendiente asociado (en caso de que el usuario proponga una edición de un juego ya existente) y para vincular la edición pendiente con el juego real una vez aprobado.

ALTER TABLE ediciones_pendientes 
MODIFY juego_pendiente_id INT NULL;

ALTER TABLE ediciones_pendientes 
ADD COLUMN juego_id_real INT NULL AFTER juego_pendiente_id,
ADD FOREIGN KEY (juego_id_real) REFERENCES juegos(id) ON DELETE CASCADE;

-- Permitir nuevas ediciones
ALTER TABLE ediciones_pendientes
ADD COLUMN plataforma_nombre_nueva VARCHAR(100);

ALTER TABLE ediciones_pendientes
ADD COLUMN idioma_nombre_nueva VARCHAR(100) NULL AFTER plataforma_nombre_nueva;

-- Permitir NULL en plataforma_id
ALTER TABLE ediciones_pendientes
MODIFY plataforma_id INT NULL;

-- 1. Averiguar el nombre exacto de la restricción (suele ser ediciones_pendientes_ibfk_2 según tu error)
ALTER TABLE ediciones_pendientes DROP FOREIGN KEY ediciones_pendientes_ibfk_2;

-- 2. Volver a crearla con el borrado en cascada habilitado
ALTER TABLE ediciones_pendientes 
ADD CONSTRAINT fk_ediciones_pendientes_plataforma 
FOREIGN KEY (plataforma_id) 
REFERENCES plataformas(id) 
ON DELETE CASCADE;

-- Eliminar la restricción actual
ALTER TABLE ediciones_pendientes DROP FOREIGN KEY fk_ediciones_pendientes_plataforma;

-- Crearla de nuevo con CASCADE
ALTER TABLE ediciones_pendientes
ADD CONSTRAINT fk_ediciones_pendientes_plataforma
FOREIGN KEY (plataforma_id)
REFERENCES plataformas(id)
ON DELETE CASCADE;

-- arreglar que si hay juegos sin edicion se borren
DELIMITER //
CREATE TRIGGER limpiar_juegos_huerfanos
AFTER DELETE ON ediciones
FOR EACH ROW
BEGIN
    -- Borra el juego si ya no existen ediciones para él
    DELETE FROM juegos 
    WHERE id = OLD.juego_id 
    AND NOT EXISTS (SELECT 1 FROM ediciones WHERE juego_id = OLD.juego_id);
END; //
DELIMITER ;