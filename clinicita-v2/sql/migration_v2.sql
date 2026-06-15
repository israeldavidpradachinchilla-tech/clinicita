--Migración para nuevas opciones de estado de citas--
USE clinica;

ALTER TABLE citas
  MODIFY estado ENUM('pendiente','confirmada','cancelada','realizada','no_realizada') NOT NULL DEFAULT 'confirmada';