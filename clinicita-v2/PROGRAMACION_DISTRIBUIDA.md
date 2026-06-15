# ClíniCita — Aplicación de Programación Distribuida
## Universidad de Cartagena · Facultad de Ingeniería · Programación Distribuida

---

## Resumen ejecutivo

**ClíniCita** es un sistema web de agendamiento de citas médicas en línea que aplica de manera directa y práctica los principios fundamentales de la **Programación Distribuida**. El sistema distribuye la lógica de negocio en capas y servicios independientes que se comunican a través de una red mediante protocolos estándar, permitiendo que múltiples clientes (pacientes, médicos, administradores) interactúen simultáneamente con el servidor desde distintos dispositivos y ubicaciones geográficas.

---

## 1. ¿Por qué ClíniCita es un sistema distribuido?

Un sistema distribuido es aquel en el que **componentes ubicados en computadores interconectados en red se comunican y coordinan sus acciones mediante el intercambio de mensajes**. ClíniCita cumple cada uno de estos criterios:

### 1.1 Múltiples nodos con roles distintos

El sistema opera con al menos tres nodos con responsabilidades diferenciadas:

| Nodo | Rol | Tecnología |
|------|-----|------------|
| **Navegador del cliente** | Interfaz gráfica, lógica de presentación | HTML5, JavaScript, Tailwind CSS |
| **Servidor de aplicación** | Lógica de negocio, autenticación, enrutamiento | PHP 8, Apache HTTP Server |
| **Servidor de base de datos** | Persistencia y consulta de datos | MySQL / MariaDB |

Cada nodo es independiente: el cliente no accede directamente a la base de datos; toda comunicación pasa por el servidor de aplicación, que actúa como intermediario.

### 1.2 Comunicación mediante mensajes sobre red

Toda interacción entre el cliente y el servidor se realiza mediante **peticiones HTTP/HTTPS** con intercambio de mensajes en formato **JSON**. Esto es la esencia de la comunicación distribuida: los procesos no comparten memoria, sino que se envían mensajes estructurados a través de la red.

```
Cliente (Navegador)
       |
       |  POST /gateway.php?svc=auth&action=login
       |  Content-Type: application/x-www-form-urlencoded
       |  Body: email=...&password=...
       ↓
Servidor Apache (PHP)
       |
       |  SELECT * FROM usuarios WHERE email=? 
       ↓
Servidor MySQL
       |
       ↓
Servidor Apache → { "ok": true, "redirect": "/paciente" }
       ↓
Cliente recibe JSON y redirige
```

### 1.3 Procesamiento concurrente

El servidor Apache gestiona múltiples solicitudes simultáneas de distintos usuarios. Un paciente puede estar agendando una cita al mismo tiempo que un médico consulta su agenda y un administrador crea horarios, todo en el mismo servidor sin interferencia entre procesos.

### 1.4 Transparencia de ubicación

El cliente no sabe dónde físicamente están almacenados los datos ni cómo está estructurado el backend. Solo conoce la URL del gateway. Esto es **transparencia de acceso**, una propiedad clave de los sistemas distribuidos.

---

## 2. Arquitectura del sistema

### 2.1 Patrón API Gateway + Microservicios

ClíniCita implementa un **API Gateway** centralizado (`gateway.php`) que recibe todas las peticiones del cliente y las enruta dinámicamente a servicios especializados:

```
                    ┌─────────────────────────────┐
                    │         API GATEWAY          │
                    │       gateway.php            │
                    │                              │
                    │  - Valida JWT                │
                    │  - Autoriza roles             │
                    │  - Enruta a servicios        │
                    └──────────────┬───────────────┘
                                   │
              ┌────────────────────┼────────────────────┐
              │                    │                     │
     ┌────────▼───────┐  ┌────────▼───────┐  ┌────────▼───────┐
     │  auth/         │  │ appointments/  │  │  schedules/    │
     │  login.php     │  │  crear.php     │  │  listar.php    │
     │  registrar.php │  │  listar.php    │  │  bloquear.php  │
     │  logout.php    │  │  cancelar.php  │  │  crear.php     │
     └────────────────┘  └────────────────┘  └────────────────┘
```

Este patrón es estándar en arquitecturas distribuidas modernas porque:
- Cada servicio es **independiente** y puede modificarse sin afectar los demás.
- El gateway centraliza la **seguridad** (autenticación JWT).
- Permite **escalabilidad horizontal**: cada servicio podría desplegarse en un servidor diferente.

### 2.2 Arquitectura Cliente-Servidor de tres capas

```
┌─────────────────────────────────────────────────────┐
│              CAPA DE PRESENTACIÓN                    │
│   HTML5 + Tailwind CSS + JavaScript (Fetch API)     │
│   Navegadores: Chrome, Firefox, Safari, Edge        │
│   Dispositivos: PC, tablet, móvil                   │
└───────────────────────┬─────────────────────────────┘
                        │ HTTP/HTTPS (JSON)
                        │ Peticiones asíncronas (AJAX)
┌───────────────────────▼─────────────────────────────┐
│              CAPA DE NEGOCIO                         │
│   PHP 8 + Apache HTTP Server                         │
│   gateway.php → servicios/* → JWT → MySQL            │
└───────────────────────┬─────────────────────────────┘
                        │ MySQLi (TCP/IP)
┌───────────────────────▼─────────────────────────────┐
│              CAPA DE DATOS                           │
│   MySQL / MariaDB                                    │
│   Tablas: usuarios, medicos, horarios, citas         │
└─────────────────────────────────────────────────────┘
```

---

## 3. Conceptos de Programación Distribuida aplicados

### 3.1 Comunicación asíncrona (Fetch API / AJAX)

El cliente no espera bloqueado mientras el servidor procesa. Utiliza la **Fetch API** de JavaScript para enviar peticiones de forma **no bloqueante**, continuando la ejecución mientras espera la respuesta:

```javascript
// Comunicación distribuida asíncrona
const respuesta = await fetch('gateway.php?svc=auth&action=login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: `email=${email}&password=${password}`
});
const datos = await respuesta.json(); // Deserialización del mensaje
```

Esto es equivalente al modelo de **paso de mensajes** en sistemas distribuidos, donde los procesos se comunican enviando y recibiendo mensajes sin compartir espacio de memoria.

### 3.2 Autenticación distribuida con JWT (JSON Web Tokens)

Uno de los problemas clásicos de los sistemas distribuidos es **cómo mantener el estado de sesión** cuando el servidor no recuerda al cliente entre peticiones (protocolo HTTP es *stateless*).

ClíniCita resuelve esto con **JWT (JSON Web Tokens)**: el servidor genera un token firmado criptográficamente que el cliente almacena en una cookie. En cada petición, el cliente envía el token y el servidor lo verifica **sin consultar la base de datos**, lo que es ideal para sistemas distribuidos:

```
1. Cliente envía: email + password
2. Servidor verifica credenciales en BD
3. Servidor genera: JWT = { id, nombre, rol, exp } + FIRMA_HMAC_SHA256
4. Cliente guarda el JWT en cookie
5. En cada petición: Cliente envía JWT → Servidor verifica firma → Autoriza
```

**Ventaja distribuida**: si el sistema escalara a múltiples servidores, cualquiera podría verificar el JWT sin compartir estado de sesión.

### 3.3 Protocolo de comunicación estándar: HTTP REST

El sistema implementa una **API RESTful** sobre HTTP, el protocolo de comunicación distribuida más utilizado en la web:

| Método | Endpoint | Acción |
|--------|----------|--------|
| POST | `/gateway.php?svc=auth&action=login` | Autenticación |
| POST | `/gateway.php?svc=appointments&action=crear` | Crear cita |
| GET  | `/gateway.php?svc=schedules&action=listar` | Listar horarios |
| POST | `/gateway.php?svc=schedules&action=bloquear` | Bloquear cupo |

Todas las respuestas son **JSON**, el formato de serialización de datos más utilizado en comunicación distribuida moderna.

### 3.4 Concurrencia y condición de carrera

ClíniCita resuelve un problema clásico de sistemas distribuidos: **la condición de carrera en la reserva de citas**. Si dos pacientes intentan reservar el mismo horario simultáneamente, el sistema podría asignar la misma cita a ambos.

La solución implementada usa la restricción `UNIQUE KEY` en la columna `horario_id` de la tabla `citas`, lo que delega la gestión de concurrencia al motor de base de datos MySQL (que usa bloqueos a nivel de fila con InnoDB):

```sql
-- La restricción UNIQUE garantiza que solo una cita pueda tener el mismo horario_id
-- Si dos transacciones intentan insertar el mismo horario_id simultáneamente,
-- MySQL rechaza la segunda con un error de duplicado
UNIQUE KEY (horario_id)
```

El servidor captura este error y notifica al segundo paciente que el horario ya fue tomado, actualizando la interfaz en tiempo real.

### 3.5 Actualización en tiempo real (Polling)

Para simular comunicación en tiempo real entre nodos distribuidos, el sistema implementa **polling periódico**: el cliente consulta al servidor cada 3-5 segundos para detectar cambios de estado:

```javascript
// Polling distribuido: el cliente consulta el servidor periódicamente
setInterval(async () => {
    const respuesta = await fetch('gateway.php?svc=appointments&action=listar_admin');
    const datos = await respuesta.json();
    if (Array.isArray(datos)) { adminCitas = datos; renderCitas(); }
}, 5000); // cada 5 segundos
```

Esto es especialmente relevante en el panel del médico: si un paciente agenda una cita mientras el médico tiene el panel abierto, la nueva cita aparece automáticamente.

### 3.6 Despliegue en entorno de producción distribuido

El sistema fue desplegado en un servidor de hosting compartido real (`clinicita.pcore.app`), separado físicamente de las máquinas de desarrollo. Esto demuestra el concepto de **distribución geográfica**: el código fuente, el servidor de aplicación y la base de datos están en un datacenter externo, accesible desde cualquier punto de Internet.

---

## 4. Seguridad en sistemas distribuidos

Los sistemas distribuidos exponen servicios en red, lo que amplía la superficie de ataque. ClíniCita implementa las siguientes medidas de seguridad:

| Amenaza | Contramedida implementada |
|---------|--------------------------|
| Acceso no autorizado | Autenticación JWT en cada petición |
| Escalada de privilegios | Verificación de rol en cada servicio (`$u['rol'] !== 'admin'`) |
| Inyección SQL | Prepared statements con `bind_param()` en MySQLi |
| XSS | `htmlspecialchars()` en todo output dinámico |
| Sesiones robadas | Expiración del JWT + limpieza de cookie al logout |
| Acceso directo a servicios | Gateway centralizado como único punto de entrada |

---

## 5. Escalabilidad

La arquitectura de ClíniCita fue diseñada pensando en la escalabilidad:

- **Escalabilidad horizontal**: los servicios son stateless (sin estado en el servidor gracias a JWT), lo que permite desplegar múltiples instancias del servidor de aplicación detrás de un load balancer.
- **Escalabilidad vertical**: la base de datos puede migrarse a un servidor dedicado sin cambiar el código de la aplicación (solo se cambian las credenciales en `db.php`).
- **Separación de responsabilidades**: cada microservicio puede optimizarse o reemplazarse de forma independiente.

---

## 6. Distribución de temas por integrante

A continuación se propone la distribución de temas para la exposición del proyecto:

---

### Integrante 1 — Arquitectura del sistema y API Gateway

**Temas a exponer:**
- Qué es la arquitectura cliente-servidor de 3 capas y cómo se implementa en ClíniCita.
- El patrón API Gateway: cómo `gateway.php` centraliza todas las peticiones y las enruta a microservicios.
- Cómo se implementó el enrutamiento dinámico de servicios en PHP (`$svc` y `$action`).
- Ventajas de este patrón frente a un monolito tradicional.
- Demo: abrir las herramientas de desarrollo del navegador (pestaña Network) y mostrar las peticiones al gateway en tiempo real.

---

### Integrante 2 — Comunicación distribuida y protocolo HTTP/REST

**Temas a exponer:**
- El protocolo HTTP como base de la comunicación distribuida en la web.
- Qué es REST y cómo ClíniCita implementa una API RESTful.
- Cómo funciona la Fetch API de JavaScript para comunicación asíncrona (no bloqueante).
- El formato JSON como estándar de serialización de mensajes entre procesos.
- El concepto de *stateless*: por qué HTTP no recuerda al cliente y cómo se resuelve.
- Demo: mostrar en Postman o en el navegador una petición directa al gateway y la respuesta JSON.

---

### Integrante 3 — Autenticación distribuida con JWT

**Temas a exponer:**
- El problema de la sesión en sistemas distribuidos: ¿cómo sabe el servidor quién es el cliente?
- Qué es un JWT (JSON Web Token): estructura (header, payload, signature) y cómo se firma con HMAC-SHA256.
- Flujo completo de autenticación en ClíniCita: login → generación del token → almacenamiento en cookie → verificación en cada petición.
- Control de acceso basado en roles (RBAC): paciente, médico, administrador.
- Por qué JWT es ideal para sistemas distribuidos escalables (sin estado de sesión en el servidor).
- Demo: decodificar un JWT en `jwt.io` y mostrar su contenido.

---

### Integrante 4 — Concurrencia, consistencia y tiempo real

**Temas a exponer:**
- El problema clásico de la condición de carrera en sistemas distribuidos: dos usuarios compitiendo por el mismo recurso.
- Cómo ClíniCita previene la doble reserva de citas usando restricciones `UNIQUE KEY` en MySQL e InnoDB.
- El modelo de actualización en tiempo real mediante polling: cómo el panel del médico y del administrador se actualizan automáticamente cada 5 segundos.
- Alternativas más avanzadas: WebSockets y Server-Sent Events (qué serían y por qué polling es suficiente para este caso).
- Demo: abrir dos navegadores, intentar reservar el mismo horario simultáneamente y mostrar cómo el sistema rechaza la segunda reserva.

---

### Integrante 5 — Despliegue, seguridad y escalabilidad

**Temas a exponer:**
- Despliegue en producción: qué implica llevar un sistema distribuido de desarrollo local (XAMPP) a un servidor real en Internet.
- El rol del `.htaccess` y `mod_rewrite` de Apache en el enrutamiento de URLs amigables.
- Seguridad en sistemas distribuidos: superficie de ataque, inyección SQL, XSS, escalada de privilegios y cómo se mitigan.
- Escalabilidad horizontal vs. vertical: cómo la arquitectura stateless de ClíniCita facilita crecer.
- Variables de entorno y configuración: por qué las credenciales no deben estar hardcodeadas en el código.
- Demo: mostrar el sistema funcionando en `clinicita.pcore.app` desde el celular y desde el computador simultáneamente.

---

## 7. Conclusión

ClíniCita no es solo un sistema de agendamiento: es una demostración práctica y completa de los principios de la Programación Distribuida aplicados a un problema real del sector salud. Implementa comunicación cliente-servidor sobre HTTP, autenticación sin estado con JWT, manejo de concurrencia en base de datos, actualización en tiempo real mediante polling, arquitectura de microservicios con API Gateway, y despliegue en un entorno de producción distribuido geográficamente.

El sistema demuestra que los conceptos teóricos de la programación distribuida —transparencia, escalabilidad, tolerancia a fallos, comunicación asíncrona— tienen aplicación directa y tangible en el desarrollo de software moderno.

---

## Referencias

- Tanenbaum, A. S., & Van Steen, M. (2007). *Distributed Systems: Principles and Paradigms*. Prentice Hall.
- Fielding, R. T. (2000). *Architectural Styles and the Design of Network-based Software Architectures*. Doctoral dissertation, UC Irvine.
- Jones, M., Bradley, J., & Sakimura, N. (2015). *JSON Web Token (JWT)*. RFC 7519. IETF.
- PHP Group. (2024). *PHP Manual — MySQLi Extension*. https://www.php.net/manual/es/book.mysqli.php
- Mozilla Developer Network. (2024). *Fetch API*. https://developer.mozilla.org/es/docs/Web/API/Fetch_API

---

*Universidad de Cartagena · Facultad de Ingeniería · Programación Distribuida · 2026*
