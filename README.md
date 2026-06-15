ClíniCita es una aplicación web para el agendamiento de citas médicas, desarrollada como proyecto de Programación Distribuida.

## Descripción

La aplicación permite que pacientes, médicos y administradores gestionen citas, horarios y usuarios desde una interfaz web. Está construida con PHP, JavaScript y MySQL, y utiliza un gateway central para enrutar todas las solicitudes del frontend hacia servicios backend especializados.

## Características principales

- Registro e inicio de sesión de usuarios
- Roles: paciente, médico y administrador
- Creación y gestión de horarios médicos
- Reserva de citas en línea
- Consulta de citas y estado de atención
- Autenticación con JWT
- Comunicación cliente-servidor vía HTTP/JSON

## Tecnologías

- PHP 8
- MySQL / MariaDB
- HTML5
- CSS
- JavaScript
- Apache (XAMPP)

## Estructura del proyecto

- `admin.php`, `medico.php`, `paciente.php`: interfaces para cada tipo de usuario
- `gateway.php`: punto de entrada principal para todas las peticiones
- `php/`: scripts del backend para operaciones clásicas
- `services/`: servicios organizados por dominio (auth, appointments, doctors, schedules)
- `sql/`: scripts de base de datos y migraciones
- `css/`, `img/`, `js/`: recursos del frontend

## Instalación local

1. Instala XAMPP o un entorno similar con PHP y MySQL.
2. Copia el proyecto a la carpeta `htdocs` de XAMPP.
3. Inicia Apache y MySQL.
4. Importa la base de datos con `sql/clinica.sql` y, si es necesario, `sql/migration_v2.sql`.
5. Configura los datos de conexión en `php/conexion.php` o `services/shared/db.php`.
6. Abre `http://localhost/clinicita-v2/` en tu navegador.

## Uso

- Inicia sesión como paciente para ver y reservar citas.
- Inicia sesión como médico para revisar el horario y las citas asignadas.
- Inicia sesión como administrador para crear médicos, horarios y controlar citas.

## Notas importantes

- `gateway.php` centraliza y enruta las peticiones hacia los servicios PHP.
- Las respuestas se manejan en JSON.
- JWT se utiliza para validar el usuario en cada petición sin depender de sesiones de servidor.

## Recomendaciones

- Verifica que la base de datos se haya importado correctamente.
- Si cambias la configuración del servidor o la base de datos, actualiza los archivos de conexión.
- Usa `phpMyAdmin` o MySQL Workbench para revisar las tablas y datos.

## Israel Prada

Proyecto desarrollado como parte de la materia de Programación Distribuida.
'@; Set-Content -Path .\README.md -Value $content -Encoding UTF8
