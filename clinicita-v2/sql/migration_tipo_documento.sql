-- Agrega el tipo de documento a la tabla usuarios
-- Ejecutar este archivo en phpMyAdmin o MySQL si ya tienes la base de datos creada
ALTER TABLE usuarios
  ADD COLUMN tipo_documento ENUM('cedula','tarjeta') NOT NULL DEFAULT 'cedula' AFTER cedula;
