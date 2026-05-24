-- Opcional: alinear FK de propuestas con el borrado de cuenta.
-- Si algún ALTER falla, revisa nombres con: SHOW CREATE TABLE juegos_pendientes;
-- El borrado desde Perfil ya limpia biblioteca, préstamos y propuestas en código (Usuario::eliminarCuenta).

-- Sustituye juegos_pendientes_ibfk_1 / ibfk_2 por los nombres que te muestre SHOW CREATE TABLE.

ALTER TABLE juegos_pendientes DROP FOREIGN KEY juegos_pendientes_ibfk_1;
ALTER TABLE juegos_pendientes
    ADD CONSTRAINT fk_jp_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE;

ALTER TABLE juegos_pendientes DROP FOREIGN KEY juegos_pendientes_ibfk_2;
ALTER TABLE juegos_pendientes
    ADD CONSTRAINT fk_jp_revisado FOREIGN KEY (revisado_por) REFERENCES usuarios(id) ON DELETE SET NULL;
