CREATE DATABASE IF NOT EXISTS clinica
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE clinica;

-- USUARIOS(pacientes, médicos y administradores)--
DROP TABLE IF EXISTS citas;
DROP TABLE IF EXISTS horarios;
DROP TABLE IF EXISTS medicos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(120) NOT NULL,
  cedula        VARCHAR(30)  NOT NULL UNIQUE,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  rol           ENUM('paciente','medico','admin') NOT NULL DEFAULT 'paciente',
  telefono      VARCHAR(30) DEFAULT NULL,
  creado_en     DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

--Datos adicionales del médico--
CREATE TABLE medicos (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id    INT NOT NULL UNIQUE,
  especialidad  VARCHAR(120) NOT NULL,
  CONSTRAINT fk_medico_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

--HORARIOS (cupos creados por el administrador)--
CREATE TABLE horarios (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  medico_id   INT NOT NULL,
  fecha       DATE NOT NULL,
  hora        TIME NOT NULL,
  disponible  TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_cupo (medico_id, fecha, hora),
  CONSTRAINT fk_horario_medico FOREIGN KEY (medico_id)
    REFERENCES medicos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

--CITAS--
CREATE TABLE citas (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  paciente_id  INT NOT NULL,
  horario_id   INT NOT NULL UNIQUE,
  motivo       VARCHAR(255),
  estado       ENUM('pendiente','confirmada','cancelada','realizada','no_realizada') NOT NULL DEFAULT 'confirmada',
  creada_en    DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cita_paciente FOREIGN KEY (paciente_id)
    REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_cita_horario FOREIGN KEY (horario_id)
    REFERENCES horarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

--Para recordar: Tengo un solo admin--
--Usuario por defecto admin--
--email: admin@clinica.com
--password: admin123

INSERT INTO usuarios (nombre, cedula, email, password, rol) VALUES
('Administrador General', '0000000000', 'admin@clinica.com',
 '$2y$10$e16MwP7qTZ4X92pooOsx0u4CZa3sGkrcrdJwBtPY4JXKAhhHg7E4m', 'admin');
