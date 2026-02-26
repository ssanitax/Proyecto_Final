/* =====================================================
   CREACIÓN DE BASE DE DATOS
===================================================== */

CREATE DATABASE proyectofinal;

USE proyectofinal;


/* =====================================================
   TABLA: USUARIOS
   - Gestiona usuarios normales y administradores
   - Email único (no se permiten duplicados)
   - Login único para front y back
===================================================== */

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,

    nombre VARCHAR(100) NOT NULL,

    email VARCHAR(150) NOT NULL,

    password VARCHAR(255) NOT NULL,

    rol ENUM('usuario', 'admin') NOT NULL DEFAULT 'usuario',

    activo BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT unique_email UNIQUE (email)
);

CREATE INDEX idx_usuario_rol ON usuarios(rol);


/* =====================================================
   TABLA: PLATAFORMAS
   - Consolas o sistemas (PS2, Switch, etc.)
===================================================== */

CREATE TABLE plataformas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);


/* =====================================================
   TABLA: JUEGOS (MAESTRO)
   - Juego como concepto general
===================================================== */

CREATE TABLE juegos (
    id INT AUTO_INCREMENT PRIMARY KEY,

    titulo VARCHAR(255) NOT NULL,

    descripcion TEXT,

    fecha_lanzamiento DATE,

    desarrollador VARCHAR(255),

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_juego_titulo ON juegos(titulo);


/* =====================================================
   TABLA: EDICIONES
   - Versiones físicas concretas del juego
   - Diferencia región, año, edición especial, etc.
===================================================== */

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


/* =====================================================
   TABLA: COLECCION_USUARIO
   - Juegos que un usuario tiene en su estantería
   - No puede repetir la misma edición
===================================================== */

CREATE TABLE coleccion_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,

    usuario_id INT NOT NULL,
    edicion_id INT NOT NULL,

    estado ENUM('pendiente', 'jugando', 'completado') 
        DEFAULT 'pendiente',

    estado_conservacion ENUM('nuevo', 'como_nuevo', 'bueno', 'usado', 'sin_caja'),

    valoracion_personal INT CHECK (valoracion_personal BETWEEN 1 AND 10),

    notas TEXT,

    fecha_adicion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (usuario_id) 
        REFERENCES usuarios(id) 
        ON DELETE CASCADE,

    FOREIGN KEY (edicion_id) 
        REFERENCES ediciones(id) 
        ON DELETE CASCADE,

    CONSTRAINT unique_usuario_edicion UNIQUE (usuario_id, edicion_id)
);

CREATE INDEX idx_coleccion_usuario ON coleccion_usuario(usuario_id);


/* =====================================================
   TABLA: VALORACIONES
   - Valoraciones globales de juegos
   - Un usuario solo puede valorar una vez cada juego
===================================================== */

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


/* =====================================================
   TABLA: PRESTAMOS
   - Registro de préstamos de juegos
   - Se vincula a la copia concreta del usuario
===================================================== */

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


/* -----------------------------------------------------
   TABLA: JUEGOS_PENDIENTES
   - Juegos propuestos por usuarios
   - Deben ser aprobados o rechazados por admin
----------------------------------------------------- */

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
        REFERENCES usuarios(id),

    FOREIGN KEY (revisado_por) 
        REFERENCES usuarios(id)
);

CREATE INDEX idx_juegos_pendientes_estado ON juegos_pendientes(estado);


/* -----------------------------------------------------
   TABLA: EDICIONES_PENDIENTES
   - Versiones físicas asociadas a juegos pendientes
----------------------------------------------------- */

CREATE TABLE ediciones_pendientes (
    id INT AUTO_INCREMENT PRIMARY KEY,

    juego_pendiente_id INT NOT NULL,
    plataforma_id INT NOT NULL,

    region VARCHAR(50),
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
