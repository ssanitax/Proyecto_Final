-- Rol super_admin: único que puede crear y eliminar otros administradores.
ALTER TABLE usuarios
    MODIFY rol ENUM('usuario', 'admin', 'super_admin') NOT NULL DEFAULT 'usuario';

-- El primer admin existente pasa a ser super administrador (ajusta el id si hace falta).
UPDATE usuarios SET rol = 'super_admin' WHERE rol = 'admin' ORDER BY id ASC LIMIT 1;
