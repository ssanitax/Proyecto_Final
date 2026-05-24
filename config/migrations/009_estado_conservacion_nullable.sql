-- Estado de conservación: reservado para ventas futuras; no obligatorio en biblioteca.
ALTER TABLE coleccion_usuario
    MODIFY estado_conservacion ENUM('nuevo', 'como_nuevo', 'bueno', 'usado', 'sin_caja') NULL DEFAULT NULL;

UPDATE coleccion_usuario SET estado_conservacion = NULL WHERE estado_conservacion IS NOT NULL;
