<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Instalador · ClíniCita</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: system-ui, sans-serif; background: #f0f9ff; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
    .card { background: #fff; border-radius: 16px; box-shadow: 0 4px 32px rgba(13,148,136,.12); padding: 40px 48px; width: 100%; max-width: 640px; }
    .logo { display: flex; align-items: center; gap: 12px; margin-bottom: 32px; }
    .logo-icon { width: 40px; height: 40px; background: linear-gradient(135deg,#0d9488,#0891b2); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
    .logo-icon svg { width: 22px; height: 22px; fill: #fff; }
    .logo h1 { font-size: 22px; font-weight: 800; color: #0d9488; }
    .logo span { font-size: 13px; color: #64748b; display: block; margin-top: 2px; }
    h2 { font-size: 18px; font-weight: 700; color: #1f2937; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 2px solid #e2e8f0; }
    .step { display: flex; align-items: flex-start; gap: 14px; padding: 12px 16px; border-radius: 10px; margin-bottom: 8px; font-size: 14px; }
    .step.ok   { background: #f0fdf4; border: 1px solid #bbf7d0; }
    .step.fail { background: #fff1f2; border: 1px solid #fecdd3; }
    .step.warn { background: #fffbeb; border: 1px solid #fde68a; }
    .step.info { background: #f0f9ff; border: 1px solid #bae6fd; }
    .badge { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; margin-top: 1px; }
    .badge.ok   { background: #22c55e; color: #fff; }
    .badge.fail { background: #ef4444; color: #fff; }
    .badge.warn { background: #f59e0b; color: #fff; }
    .badge.info { background: #0ea5e9; color: #fff; }
    .step-text { flex: 1; }
    .step-text strong { display: block; color: #1f2937; margin-bottom: 2px; }
    .step-text span { color: #64748b; }
    .step-text code { background: #f1f5f9; padding: 1px 6px; border-radius: 4px; font-size: 12px; color: #0f172a; }
    .btn { display: inline-block; padding: 14px 28px; background: linear-gradient(135deg,#0d9488,#0891b2); color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; text-decoration: none; margin-top: 24px; width: 100%; text-align: center; transition: opacity .2s; }
    .btn:hover { opacity: .88; }
    .btn.gris { background: #64748b; }
    .btn.verde { background: linear-gradient(135deg,#16a34a,#15803d); }
    .credentials { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px 20px; margin-top: 20px; }
    .credentials h3 { font-size: 14px; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 12px; }
    .cred-row { display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
    .cred-row:last-child { border-bottom: none; }
    .cred-label { color: #64748b; }
    .cred-value { font-weight: 600; color: #1f2937; font-family: monospace; }
    .warning-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 14px 16px; margin-top: 16px; font-size: 13px; color: #78350f; }
    .warning-box strong { display: block; margin-bottom: 4px; }
  </style>
</head>
<body>
<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start();

// ─── Leer credenciales desde POST o usar valores por defecto ───────────────
$DB_HOST = trim($_POST['db_host'] ?? 'localhost');
$DB_USER = trim($_POST['db_user'] ?? 'root');
$DB_PASS = $_POST['db_pass'] ?? '';
$DB_NAME = trim($_POST['db_name'] ?? 'clinica');

// Si no se envió el formulario, mostrar pantalla de configuración
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { ?>

  <div class="card" style="max-width:520px;">
    <div class="logo">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6.5 3.5 5 5.5 5c1.54 0 2.99.99 3.57 2.36h1.87C12.51 5.99 13.96 5 15.5 5 17.5 5 19 6.5 19 8.5c0 3.78-3.4 6.86-8.65 11.54L12 21.35z"/></svg>
      </div>
      <div><h1>ClíniCita</h1><span>Instalador del sistema</span></div>
    </div>

    <h2>Configuración de base de datos</h2>
    <p style="font-size:14px;color:#64748b;margin-bottom:20px;">
      Ingresa las credenciales de tu base de datos MySQL. En hosting compartido encuéntralas en el panel de control (cPanel, Plesk, etc.).
    </p>

    <form method="POST" style="display:flex;flex-direction:column;gap:14px;">
      <div>
        <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">Host de la base de datos</label>
        <input name="db_host" value="localhost" required
          style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;outline:none;">
        <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">Normalmente <code style="background:#f1f5f9;padding:1px 5px;border-radius:4px;">localhost</code></p>
      </div>
      <div>
        <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">Usuario</label>
        <input name="db_user" value="root" required
          style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;outline:none;">
      </div>
      <div>
        <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">Contraseña</label>
        <input name="db_pass" type="password" placeholder="(vacío si es local)"
          style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;outline:none;">
      </div>
      <div>
        <label style="display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.06em;margin-bottom:5px;">Nombre de la base de datos</label>
        <input name="db_name" value="clinica" required
          style="width:100%;padding:10px 12px;border:1.5px solid #e2e8f0;border-radius:8px;font-size:14px;box-sizing:border-box;outline:none;">
        <p style="font-size:12px;color:#94a3b8;margin:4px 0 0;">En hosting puede ser <code style="background:#f1f5f9;padding:1px 5px;border-radius:4px;">usuario_clinica</code></p>
      </div>

      <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 14px;font-size:13px;color:#92400e;">
        <strong style="display:block;margin-bottom:4px;">⚠️ Importante en hosting</strong>
        La base de datos debe estar creada previamente en tu panel de control. El instalador solo creará las tablas dentro de ella.
      </div>

      <button type="submit" class="btn" style="margin-top:4px;">🚀 Instalar ClíniCita</button>
    </form>
  </div>

<?php
  ob_end_flush(); exit;
}

// ─── Usar las credenciales recibidas por POST ──────────────────────────────
define('DB_HOST', $DB_HOST);
define('DB_USER', $DB_USER);
define('DB_PASS', $DB_PASS);
define('DB_NAME', $DB_NAME);

// ─── SQL completo de la base de datos ──────────────────────────────────────
$SQL_SCHEMA = <<<SQL
DROP TABLE IF EXISTS citas;
DROP TABLE IF EXISTS horarios;
DROP TABLE IF EXISTS medicos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
  id             INT AUTO_INCREMENT PRIMARY KEY,
  nombre         VARCHAR(120) NOT NULL,
  cedula         VARCHAR(30)  NOT NULL UNIQUE,
  tipo_documento ENUM('cedula','tarjeta') NOT NULL DEFAULT 'cedula',
  email          VARCHAR(150) NOT NULL UNIQUE,
  password       VARCHAR(255) NOT NULL,
  rol            ENUM('paciente','medico','admin') NOT NULL DEFAULT 'paciente',
  telefono       VARCHAR(30)  DEFAULT NULL,
  session_token  VARCHAR(255) DEFAULT NULL,
  creado_en      DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE medicos (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id   INT NOT NULL UNIQUE,
  especialidad VARCHAR(120) NOT NULL,
  CONSTRAINT fk_medico_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE horarios (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  medico_id  INT NOT NULL,
  fecha      DATE NOT NULL,
  hora       TIME NOT NULL,
  disponible TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_cupo (medico_id, fecha, hora),
  CONSTRAINT fk_horario_medico FOREIGN KEY (medico_id)
    REFERENCES medicos(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE citas (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  paciente_id INT NOT NULL,
  horario_id  INT NOT NULL UNIQUE,
  motivo      VARCHAR(255),
  estado      ENUM('pendiente','confirmada','cancelada','realizada','no_realizada')
              NOT NULL DEFAULT 'confirmada',
  creada_en   DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_cita_paciente FOREIGN KEY (paciente_id)
    REFERENCES usuarios(id) ON DELETE CASCADE,
  CONSTRAINT fk_cita_horario FOREIGN KEY (horario_id)
    REFERENCES horarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO usuarios (nombre, cedula, tipo_documento, email, password, rol) VALUES
('Administrador General', '0000000000', 'cedula', 'admin@clinica.com',
 '\$2y\$10\$e16MwP7qTZ4X92pooOsx0u4CZa3sGkrcrdJwBtPY4JXKAhhHg7E4m', 'admin');
SQL;

// ─── Resultados ────────────────────────────────────────────────────────────
$pasos   = [];
$errores = 0;

function paso(string $titulo, string $detalle, string $estado): void {
    global $pasos, $errores;
    $pasos[] = compact('titulo', 'detalle', 'estado');
    if ($estado === 'fail') $errores++;
}

// 1. Verificar extensión MySQLi
if (extension_loaded('mysqli')) {
    paso('Extensión MySQLi', 'PHP tiene soporte para MySQL', 'ok');
} else {
    paso('Extensión MySQLi', 'PHP no tiene cargada la extensión mysqli. Habilítala en php.ini', 'fail');
}

// 2. Verificar versión de PHP
$phpVersion = phpversion();
if (version_compare($phpVersion, '7.4', '>=')) {
    paso('Versión de PHP', "PHP $phpVersion — compatible", 'ok');
} else {
    paso('Versión de PHP', "PHP $phpVersion — se requiere 7.4 o superior", 'fail');
}

// 3. Conectar a MySQL y seleccionar la BD
$conn = null;
if ($errores === 0) {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        paso('Conexión a MySQL',
             'No se pudo conectar: ' . $conn->connect_error .
             '. Verifica host, usuario, contraseña y que la base de datos <strong>' . htmlspecialchars(DB_NAME) . '</strong> exista.', 'fail');
        $conn = null;
    } else {
        $conn->set_charset('utf8mb4');
        paso('Conexión a MySQL',
             'Conectado a <code>' . htmlspecialchars(DB_HOST) . '</code> · BD: <code>' . htmlspecialchars(DB_NAME) . '</code>', 'ok');
    }
}

// 4. Crear tablas
if ($conn) {
    $conn->multi_query($SQL_SCHEMA);

    $queryOk  = true;
    $queryMsg = '';
    do {
        if ($result = $conn->store_result()) $result->free();
        if ($conn->error) { $queryOk = false; $queryMsg = $conn->error; break; }
    } while ($conn->more_results() && $conn->next_result());

    if ($queryOk) {
        paso('Tablas instaladas',
             '4 tablas creadas: <code>usuarios</code>, <code>medicos</code>, <code>horarios</code>, <code>citas</code>', 'ok');
    } else {
        paso('Error al crear tablas', $queryMsg, 'fail');
    }
}

// 5. Verificar tablas
if ($conn && $errores === 0) {
    $tablas = ['usuarios','medicos','horarios','citas'];
    $encontradas = [];
    $resT = $conn->query("SHOW TABLES");
    if ($resT) while ($row = $resT->fetch_array()) $encontradas[] = $row[0];
    $faltantes = array_diff($tablas, $encontradas);
    if (empty($faltantes)) {
        paso('Tablas verificadas', implode(', ', $tablas), 'ok');
    } else {
        paso('Tablas faltantes', 'No se encontraron: ' . implode(', ', $faltantes), 'fail');
    }
}

// 6. Verificar usuario admin
if ($conn && $errores === 0) {
    $r = $conn->query("SELECT id FROM usuarios WHERE email='admin@clinica.com' AND rol='admin'");
    if ($r && $r->num_rows > 0) {
        paso('Usuario administrador', 'admin@clinica.com creado correctamente', 'ok');
    } else {
        paso('Usuario administrador', 'No se encontró el usuario admin. Revisa el INSERT en el schema.', 'warn');
    }
}

// 7. Verificar que los archivos del gateway existen
$archivos = [
    'gateway.php'                            => 'API Gateway',
    'services/shared/jwt.php'                => 'Shared: JWT',
    'services/shared/db.php'                 => 'Shared: DB',
    'services/auth/login.php'                => 'Auth: login',
    'services/auth/registrar.php'            => 'Auth: registrar',
    'services/auth/logout.php'               => 'Auth: logout',
    'services/auth/actualizar_perfil.php'    => 'Auth: actualizar perfil',
    'services/auth/eliminar_perfil.php'      => 'Auth: eliminar perfil',
    'services/appointments/crear.php'        => 'Appointments: crear',
    'services/appointments/listar.php'       => 'Appointments: listar',
    'services/appointments/listar_admin.php' => 'Appointments: listar admin',
    'services/appointments/listar_medico.php'=> 'Appointments: listar médico',
    'services/appointments/cancelar.php'     => 'Appointments: cancelar',
    'services/appointments/marcar.php'       => 'Appointments: marcar',
    'services/doctors/listar.php'            => 'Doctors: listar',
    'services/doctors/crear.php'             => 'Doctors: crear',
    'services/doctors/editar.php'            => 'Doctors: editar',
    'services/doctors/eliminar.php'          => 'Doctors: eliminar',
    'services/schedules/listar.php'          => 'Schedules: listar',
    'services/schedules/listar_admin.php'    => 'Schedules: listar admin',
    'services/schedules/listar_medico.php'   => 'Schedules: listar médico',
    'services/schedules/crear.php'           => 'Schedules: crear',
    'services/schedules/bloquear.php'        => 'Schedules: bloquear',
    'services/schedules/eliminar.php'        => 'Schedules: eliminar',
    'services/schedules/fechas_citas.php'    => 'Schedules: fechas citas',
    'services/schedules/estado.php'          => 'Schedules: estado',
    'admin.php'                              => 'Panel Administrador',
    'medico.php'                             => 'Panel Médico',
    'paciente.php'                           => 'Panel Paciente',
    'js/paciente.js'                         => 'JS: paciente',
    'css/estilos.css'                        => 'CSS: estilos',
];
$faltanArchivos = [];
foreach ($archivos as $ruta => $nombre) {
    if (!file_exists(__DIR__ . '/' . $ruta)) $faltanArchivos[] = $nombre;
}
if (empty($faltanArchivos)) {
    paso('Microservicios', 'gateway.php y todos los servicios están presentes', 'ok');
} else {
    paso('Microservicios', 'Archivos faltantes: ' . implode(', ', $faltanArchivos), 'warn');
}

if ($conn) $conn->close();

// ─── Render ────────────────────────────────────────────────────────────────
$iconos = ['ok'=>'✓', 'fail'=>'✗', 'warn'=>'!', 'info'=>'i'];
?>

<div class="card">
  <div class="logo">
    <div class="logo-icon">
      <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6.5 3.5 5 5.5 5c1.54 0 2.99.99 3.57 2.36h1.87C12.51 5.99 13.96 5 15.5 5 17.5 5 19 6.5 19 8.5c0 3.78-3.4 6.86-8.65 11.54L12 21.35z"/>
      </svg>
    </div>
    <div>
      <h1>ClíniCita</h1>
      <span>Instalador del sistema · Universidad de Cartagena</span>
    </div>
  </div>

  <h2>Verificación del entorno</h2>

  <?php foreach ($pasos as $p): ?>
    <div class="step <?= $p['estado'] ?>">
      <div class="badge <?= $p['estado'] ?>"><?= $iconos[$p['estado']] ?></div>
      <div class="step-text">
        <strong><?= $p['titulo'] ?></strong>
        <span><?= $p['detalle'] ?></span>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if ($errores === 0): ?>
    <div class="credentials">
      <h3>Credenciales de acceso</h3>
      <div class="cred-row"><span class="cred-label">URL del sistema</span><span class="cred-value">http://<?= $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) ?>/../</span></div>
      <div class="cred-row"><span class="cred-label">Rol</span><span class="cred-value">Administrador</span></div>
      <div class="cred-row"><span class="cred-label">Correo</span><span class="cred-value">admin@clinica.com</span></div>
      <div class="cred-row"><span class="cred-label">Contraseña</span><span class="cred-value">admin123</span></div>
    </div>

    <div class="warning-box">
      <strong>⚠️ Seguridad</strong>
      Elimina o restringe el acceso a <code>install.php</code> después de la instalación.
      Este archivo reinicia toda la base de datos cada vez que se ejecuta.
    </div>

    <?php
    // Si las credenciales son distintas al default local, mostrar aviso de db.php
    $esLocal = (DB_HOST === 'localhost' && DB_USER === 'root' && DB_PASS === '' && DB_NAME === 'clinica');
    if (!$esLocal): ?>
    <div class="warning-box" style="background:#fff7ed;border-color:#fed7aa;color:#9a3412;">
      <strong>⚙️ Paso adicional requerido</strong>
      Edita el archivo <code>services/shared/db.php</code> y actualiza las credenciales:<br>
      <code style="display:block;margin-top:8px;background:#fef3c7;padding:8px;border-radius:6px;font-size:12px;">
        new mysqli('<?= htmlspecialchars(DB_HOST) ?>', '<?= htmlspecialchars(DB_USER) ?>', '***', '<?= htmlspecialchars(DB_NAME) ?>');
      </code>
    </div>
    <?php endif; ?>

    <div class="warning-box">
      <strong>⚠️ Seguridad</strong>
      Elimina o restringe el acceso a <code>install.php</code> después de la instalación.
      Este archivo reinicia toda la base de datos cada vez que se ejecuta.
    </div>

    <a href="/inicio" class="btn verde">🚀 Ir a la aplicación</a>

  <?php else: ?>
    <div class="warning-box" style="background:#fff1f2;border-color:#fecdd3;color:#9f1239;">
      <strong>❌ Instalación incompleta</strong>
      Corrige los errores marcados en rojo y vuelve a intentarlo.
    </div>
    <form method="POST" style="margin-top:12px;">
      <input type="hidden" name="db_host" value="<?= htmlspecialchars(DB_HOST) ?>">
      <input type="hidden" name="db_user" value="<?= htmlspecialchars(DB_USER) ?>">
      <input type="hidden" name="db_pass" value="<?= htmlspecialchars(DB_PASS) ?>">
      <input type="hidden" name="db_name" value="<?= htmlspecialchars(DB_NAME) ?>">
      <button type="submit" class="btn" style="background:#64748b;">🔄 Reintentar con las mismas credenciales</button>
    </form>
    <a href="install.php" class="btn" style="margin-top:8px;">⬅ Cambiar credenciales</a>
  <?php endif; ?>
</div>

</body>
</html>
