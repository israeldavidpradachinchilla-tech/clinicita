<?php
require_once __DIR__ . '/services/shared/jwt.php';

$token = $_COOKIE['clinicita_token'] ?? '';
$u     = $token ? JWT::decode($token) : null;
if (!$u || $u['rol'] !== 'admin') {
    header('Location: /login'); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Administrador · ClíniCita</title>
  <link rel="stylesheet" href="css/estilos.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: { DEFAULT: '#0d9488', dark: '#0f766e', light: '#14b8a6' },
          }
        }
      },
      corePlugins: { preflight: false }
    }
  </script>
  <style>
    /* Layout */
    html, body { height: 100%; }
    body { display: flex; background: #f1f5f9; }

    /* Sidebar */
    #sidebar { width: 260px; min-width: 260px; }

    /* Secciones de contenido */
    .seccion { display: none; }
    .seccion.activa { display: block; }
    .seccion-titulo { font-size: 22px; font-weight: 700; color: #0d9488; margin-bottom: 24px; padding-bottom: 10px; border-bottom: 2px solid #e2e8f0; }
    .admin-content .formulario { margin: 0 0 32px; max-width: 560px; }

    /* Modales */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.45); z-index: 999; align-items: center; justify-content: center; }
    .modal-box { background: #fff; border-radius: 12px; padding: 32px; width: 100%; max-width: 440px; box-shadow: 0 8px 32px rgba(0,0,0,.15); }
    .modal-box h3 { color: #0d9488; margin-bottom: 20px; }
    .modal-box input { width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; margin-bottom: 12px; font-size: 14px; }
    .modal-box label { display: block; font-weight: 500; margin-bottom: 4px; }
    .modal-hint { color: #94a3b8; font-weight: 400; }
    .modal-actions { display: flex; gap: 10px; margin-top: 8px; }
    .modal-actions .btn { flex: 1; padding: 12px; }

    /* Nav active state */
    .nav-link.activo { background: rgba(255,255,255,0.15) !important; border-left-color: #fff !important; color: #fff !important; font-weight: 700; }
    .nav-link.activo .nav-icon-wrap { background: rgba(255,255,255,0.25) !important; }

    /* Inputs limpios para formularios con Tailwind */
    .tw-field {
      display: block; width: 100%; padding: 10px 14px;
      border: 1.5px solid #e2e8f0; border-radius: 10px;
      font-size: 14px; color: #1f2937; background: #fff;
      outline: none; transition: border-color .15s, box-shadow .15s;
      box-sizing: border-box;
    }
    .tw-field::placeholder { color: #cbd5e1; }
    .tw-field:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13,148,136,.1); }
    .tw-label {
      display: block; font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: .06em;
      color: #64748b; margin-bottom: 6px;
    }
    @keyframes pulse-dot {
      0%, 100% { opacity: 1; transform: scale(1); }
      50%       { opacity: .5; transform: scale(.75); }
    }
    /* Tablas dentro de tarjetas — anula el margin-top global */
    .card-table table { margin-top: 0; border-radius: 0; box-shadow: none; }
    .card-table th    { background: transparent; color: #94a3b8; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; padding: 12px 20px; border-bottom: 1.5px solid #f1f5f9; }
  </style>
</head>
<body class="font-sans text-gray-800">

  <!-- ── SIDEBAR ─────────────────────────────────────────────────── -->
  <aside id="sidebar" class="flex flex-col h-screen sticky top-0 overflow-y-auto"
    style="background: linear-gradient(175deg, #0d9488 0%, #0a7a6e 50%, #0891b2 100%); box-shadow: 4px 0 20px rgba(0,0,0,0.12);">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
      <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/20 shadow-inner">
        <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6.5 3.5 5 5.5 5c1.54 0 2.99.99 3.57 2.36h1.87C12.51 5.99 13.96 5 15.5 5 17.5 5 19 6.5 19 8.5c0 3.78-3.4 6.86-8.65 11.54L12 21.35z"/></svg>
      </div>
      <div>
        <p class="text-white font-bold text-lg leading-tight">ClíniCita</p>
        <p class="text-white/60 text-xs font-medium uppercase tracking-widest">Admin</p>
      </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 flex flex-col gap-1">
      <p class="text-white/40 text-xs font-bold uppercase tracking-widest px-3 mb-2">Panel</p>

      <a href="#" class="nav-link activo group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 border-l-4 border-transparent text-sm font-medium no-underline" data-seccion="inicio">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 transition-all shrink-0">
          <!-- Home -->
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><path d="M9 21V12h6v9"/>
          </svg>
        </span>
        Inicio
      </a>

      <p class="text-white/40 text-xs font-bold uppercase tracking-widest px-3 mt-5 mb-2">Gestión</p>

      <a href="#" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 border-l-4 border-transparent text-sm font-medium no-underline" data-seccion="crear-medico">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <!-- User plus -->
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
          </svg>
        </span>
        Crear Médico
      </a>

      <a href="#" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 border-l-4 border-transparent text-sm font-medium no-underline" data-seccion="crear-horarios">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <!-- Calendar -->
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
          </svg>
        </span>
        Crear Horarios
      </a>

      <a href="#" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 border-l-4 border-transparent text-sm font-medium no-underline" data-seccion="ver-citas">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <!-- Clipboard list -->
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
          </svg>
        </span>
        Ver Citas
      </a>

      <a href="#" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 border-l-4 border-transparent text-sm font-medium no-underline" data-seccion="cupos">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <!-- Clock / schedule -->
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 12"/>
          </svg>
        </span>
        Gestión de Cupos
      </a>
    </nav>

    <!-- Usuario + Logout -->
    <div class="px-4 py-4 border-t border-white/10">
      <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-white/10 mb-3">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-white/20 shrink-0">
          <svg class="w-5 h-5 fill-white" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
        </div>
        <div class="overflow-hidden">
          <p class="text-white text-sm font-semibold truncate"><?= htmlspecialchars($u['nombre']) ?></p>
          <p class="text-white/50 text-xs">Administrador</p>
        </div>
      </div>
      <a href="#" id="btn-logout"
        class="flex items-center justify-center gap-2 w-full py-2 px-4 rounded-xl bg-white/10 hover:bg-red-500/80 text-white/80 hover:text-white text-sm font-medium transition-all duration-150 no-underline">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
        Cerrar sesión
      </a>
    </div>
  </aside>

  <!-- ── ÁREA PRINCIPAL ──────────────────────────────────────────── -->
  <div class="flex flex-col flex-1 min-h-screen overflow-hidden">

    <!-- Topbar -->
    <header class="flex items-center justify-between bg-white px-8 py-0 h-16 shrink-0"
      style="box-shadow: 0 1px 3px rgba(0,0,0,0.08); border-bottom: 1px solid #f1f5f9;">
      <div class="flex items-center gap-3">
        <div class="w-1.5 h-7 rounded-full" style="background: linear-gradient(180deg,#0d9488,#0891b2);"></div>
        <span id="topbar-titulo" class="font-semibold text-gray-700 text-base">Resumen general</span>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-400 hidden sm:block">
          <?= date('d \d\e F, Y') ?>
        </span>
        <div class="w-px h-5 bg-gray-200"></div>
        <div class="flex items-center gap-2 text-sm text-gray-600 font-medium">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
            style="background: linear-gradient(135deg,#0d9488,#0891b2);">
            <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
          </div>
          <?= htmlspecialchars(explode(' ', $u['nombre'])[0]) ?>
        </div>
      </div>
    </header>

    <!-- Contenido -->
    <main class="admin-content flex-1 overflow-y-auto p-8" style="background:#f1f5f9;">

      <!-- INICIO -->
      <section class="seccion activa" id="seccion-inicio">

        <!-- Saludo -->
        <div class="mb-7">
          <h1 class="text-2xl font-bold text-gray-800">Bienvenido, <?= htmlspecialchars(explode(' ', $u['nombre'])[0]) ?> 👋</h1>
          <p class="text-sm text-gray-400 mt-1">Aquí tienes el resumen de actividad de la clínica.</p>
        </div>

        <!-- Tarjetas de stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">

          <!-- Pacientes -->
          <div class="bg-white rounded-2xl p-6 flex items-center gap-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl shrink-0"
              style="background: linear-gradient(135deg,#0d9488,#14b8a6);">
              <svg class="w-7 h-7 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
              </svg>
            </div>
            <div>
              <p class="text-3xl font-extrabold text-gray-800 leading-none" id="stat-pacientes">—</p>
              <p class="text-sm text-gray-400 mt-1 font-medium">Pacientes registrados</p>
            </div>
          </div>

          <!-- Médicos -->
          <div class="bg-white rounded-2xl p-6 flex items-center gap-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl shrink-0"
              style="background: linear-gradient(135deg,#0891b2,#38bdf8);">
              <svg class="w-7 h-7 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                <circle cx="12" cy="13" r="3"/>
              </svg>
            </div>
            <div>
              <p class="text-3xl font-extrabold text-gray-800 leading-none" id="stat-medicos">—</p>
              <p class="text-sm text-gray-400 mt-1 font-medium">Médicos activos</p>
            </div>
          </div>

          <!-- Citas -->
          <div class="bg-white rounded-2xl p-6 flex items-center gap-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl shrink-0"
              style="background: linear-gradient(135deg,#6366f1,#8b5cf6);">
              <svg class="w-7 h-7 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01"/>
              </svg>
            </div>
            <div>
              <p class="text-3xl font-extrabold text-gray-800 leading-none" id="stat-citas">—</p>
              <p class="text-sm text-gray-400 mt-1 font-medium">Citas confirmadas</p>
            </div>
          </div>

        </div>

        <!-- Tabla médicos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

          <!-- Header -->
          <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 flex-wrap">
            <div>
              <h2 class="font-bold text-gray-800 text-base">Médicos registrados</h2>
              <p class="text-xs text-gray-400 mt-0.5">Equipo médico activo en el sistema</p>
            </div>
            <div class="flex items-center gap-3">
              <!-- Búsqueda -->
              <div style="display:flex;align-items:center;gap:8px;border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;background:#fff;min-width:200px;">
                <svg style="width:14px;height:14px;stroke:#94a3b8;fill:none;flex-shrink:0;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input id="buscar-medico" type="text" placeholder="Buscar médico…"
                  style="border:none;outline:none;font-size:13px;color:#374151;background:transparent;width:100%;"
                  oninput="renderMedicos()">
              </div>
              <!-- Botón nuevo -->
              <a href="#" class="nav-link flex items-center gap-2 text-sm font-semibold px-4 py-2 rounded-xl text-white no-underline"
                style="background:linear-gradient(135deg,#0d9488,#0891b2);white-space:nowrap;" data-seccion="crear-medico">
                <svg style="width:14px;height:14px;stroke:#fff;fill:none;" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nuevo médico
              </a>
            </div>
          </div>

          <!-- Tabla -->
          <div class="card-table" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;margin:0;">
              <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #f1f5f9;">
                  <th style="padding:11px 24px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;background:transparent;">Médico</th>
                  <th style="padding:11px 24px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;background:transparent;">Especialidad</th>
                  <th style="padding:11px 24px;text-align:left;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;background:transparent;">Email</th>
                  <th style="padding:11px 24px;text-align:right;font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;background:transparent;">Acciones</th>
                </tr>
              </thead>
              <tbody id="tbody-medicos"></tbody>
            </table>
          </div>

        </div>

      </section>

      <!-- MODAL EDITAR MÉDICO -->
      <div id="modal-editar" class="modal-overlay">
        <div class="modal-box" style="max-width:480px;">
          <div class="flex items-center gap-3 mb-5">
            <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0" style="background:linear-gradient(135deg,#0d9488,#0891b2);">
              <svg class="w-5 h-5 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            </div>
            <div>
              <h3 style="margin:0;color:#1f2937;font-size:16px;">Editar médico</h3>
              <p style="margin:0;font-size:12px;color:#94a3b8;">Modifica los datos del perfil</p>
            </div>
          </div>
          <form id="form-editar-medico" class="flex flex-col gap-3">
            <input type="hidden" name="id" id="edit-id">
            <div>
              <label class="tw-label">Nombre</label>
              <input type="text" name="nombre" id="edit-nombre" required class="tw-field">
            </div>
            <div>
              <label class="tw-label">Especialidad</label>
              <input type="text" name="especialidad" id="edit-especialidad" required class="tw-field">
            </div>
            <div>
              <label class="tw-label">Email</label>
              <input type="email" name="email" id="edit-email" required class="tw-field">
            </div>
            <div>
              <label class="tw-label">Nueva contraseña <span style="color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0;">(dejar en blanco para no cambiar)</span></label>
              <input type="password" name="password" id="edit-password" minlength="6" placeholder="••••••" class="tw-field">
            </div>
            <div class="flex gap-3 mt-2">
              <button type="submit" class="flex-1 py-2.5 rounded-xl text-white font-semibold text-sm" style="background:linear-gradient(135deg,#0d9488,#0891b2);border:none;cursor:pointer;">Guardar cambios</button>
              <button type="button" onclick="cerrarEditar()" class="flex-1 py-2.5 rounded-xl font-semibold text-sm" style="background:#f1f5f9;color:#64748b;border:none;cursor:pointer;">Cancelar</button>
            </div>
          </form>
        </div>
      </div>

      <!-- MODAL EDITAR DÍA -->
      <div id="modal-dia" class="modal-overlay">
        <div class="modal-box" style="max-width:520px; max-height:80vh; overflow-y:auto;">
          <h3 id="modal-dia-titulo">Cupos del día</h3>
          <div id="modal-dia-cupos"></div>
          <hr style="margin:16px 0; border-color:#e2e8f0;">
          <p style="font-weight:600; color:#1f2937; margin-bottom:10px;">Agregar cupos a este día</p>
          <form id="form-agregar-cupo" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <input type="hidden" id="modal-medico-id">
            <input type="hidden" id="modal-fecha">
            <div>
              <label style="font-size:13px; font-weight:500;">Hora inicio</label>
              <input type="time" id="modal-hora-inicio" style="padding:8px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;" required>
            </div>
            <div>
              <label style="font-size:13px; font-weight:500;">Hora fin</label>
              <input type="time" id="modal-hora-fin" style="padding:8px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px;" required>
            </div>
            <div>
              <label style="font-size:13px; font-weight:500;">Intervalo (min)</label>
              <input type="number" id="modal-intervalo" value="30" min="1" max="1440" style="padding:8px; border:1px solid #cbd5e1; border-radius:6px; font-size:14px; width:80px;" required>
            </div>
            <button type="button" class="btn" onclick="agregarCuposDia()">➕ Agregar</button>
          </form>
          <div style="display:flex; gap:10px; margin-top:20px;">
            <button class="btn rojo" onclick="eliminarTodoDia()">🗑️ Eliminar todos los cupos del día</button>
            <button class="btn gris" onclick="cerrarModalDia()">Cerrar</button>
          </div>
        </div>
      </div>

      <!-- CREAR MÉDICO -->
      <section class="seccion" id="seccion-crear-medico">

        <div class="mb-7">
          <h1 class="text-2xl font-bold text-gray-800">Crear perfil de médico</h1>
          <p class="text-sm text-gray-400 mt-1">Registra un nuevo médico en el sistema.</p>
        </div>

        <div class="flex gap-8 items-start flex-wrap">

          <!-- Formulario -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex-1" style="min-width:340px;max-width:520px;">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
              <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0"
                style="background:linear-gradient(135deg,#0d9488,#0891b2);">
                <svg class="w-5 h-5 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                  <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                </svg>
              </div>
              <div>
                <p class="font-bold text-gray-800 text-sm">Datos del médico</p>
                <p class="text-xs text-gray-400">Completa todos los campos requeridos</p>
              </div>
            </div>

            <form id="form-crear-medico" class="px-6 py-5 flex flex-col gap-4">

              <div class="grid grid-cols-2 gap-4">
                <!-- Nombre -->
                <div class="col-span-2">
                  <label class="tw-label">Nombre completo <span style="color:#f87171;">*</span></label>
                  <input type="text" name="nombre" required placeholder="Dr. Juan Pérez" class="tw-field">
                </div>

                <!-- Cédula -->
                <div>
                  <label class="tw-label">Cédula <span style="color:#f87171;">*</span></label>
                  <input type="text" name="cedula" required placeholder="123456789" class="tw-field">
                </div>

                <!-- Email -->
                <div>
                  <label class="tw-label">Correo electrónico <span style="color:#f87171;">*</span></label>
                  <input type="email" name="email" required placeholder="doctor@clinica.com" class="tw-field">
                </div>

                <!-- Especialidad existente -->
                <div class="col-span-2">
                  <label class="tw-label">Especialidad existente</label>
                  <select name="especialidad" id="select-especialidad-crear" class="tw-field">
                    <option value="">— Selecciona una especialidad —</option>
                  </select>
                  <p style="font-size:12px;color:#94a3b8;margin-top:5px;">O escribe una nueva abajo si no aparece en la lista.</p>
                </div>

                <!-- Nueva especialidad -->
                <div class="col-span-2">
                  <label class="tw-label">Nueva especialidad</label>
                  <input type="text" name="especialidad_nueva" placeholder="Ej: Cardiología" class="tw-field">
                </div>

                <!-- Contraseña -->
                <div class="col-span-2">
                  <label class="tw-label">Contraseña inicial <span style="color:#f87171;">*</span></label>
                  <input type="password" name="password" required minlength="6" placeholder="Mínimo 6 caracteres" class="tw-field">
                </div>
              </div>

              <!-- Mensaje feedback -->
              <div id="msg-crear-medico" class="hidden text-sm px-4 py-3 rounded-xl font-medium"></div>

              <!-- Botón submit -->
              <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white font-semibold text-sm transition-all hover:opacity-90"
                style="background:linear-gradient(135deg,#0d9488,#0891b2); border:none; cursor:pointer; margin-top:4px;">
                <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Crear médico
              </button>

            </form>
          </div>

          <!-- Panel informativo -->
          <div class="flex flex-col gap-4" style="min-width:240px;max-width:300px;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Tips</p>
              <ul class="flex flex-col gap-3 text-sm text-gray-500">
                <li class="flex items-start gap-2">
                  <span class="mt-0.5 flex items-center justify-center w-5 h-5 rounded-full shrink-0 text-white text-xs font-bold" style="background:#0d9488;">1</span>
                  El médico recibirá sus credenciales para ingresar al sistema.
                </li>
                <li class="flex items-start gap-2">
                  <span class="mt-0.5 flex items-center justify-center w-5 h-5 rounded-full shrink-0 text-white text-xs font-bold" style="background:#0d9488;">2</span>
                  Si la especialidad ya existe, selecciónala del listado para mantener consistencia.
                </li>
                <li class="flex items-start gap-2">
                  <span class="mt-0.5 flex items-center justify-center w-5 h-5 rounded-full shrink-0 text-white text-xs font-bold" style="background:#0d9488;">3</span>
                  Tras crear el médico, podrás asignarle horarios desde la sección <strong>Crear Horarios</strong>.
                </li>
              </ul>
            </div>
            <div class="rounded-2xl p-5 border border-teal-100" style="background:#f0fdfa;">
              <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 fill-none stroke-current text-teal-600" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <p class="text-xs font-bold text-teal-700 uppercase tracking-wide">Nota</p>
              </div>
              <p class="text-xs text-teal-600 leading-relaxed">La contraseña puede cambiarse después desde la edición del médico.</p>
            </div>
          </div>

        </div>
      </section>

      <!-- CREAR HORARIOS -->
      <section class="seccion" id="seccion-crear-horarios">

        <div class="mb-7">
          <h1 class="text-2xl font-bold text-gray-800">Crear horarios de trabajo</h1>
          <p class="text-sm text-gray-400 mt-1">Asigna un rango de fechas, días y horas de atención a un médico.</p>
        </div>

        <div class="flex gap-6 items-start flex-wrap">

          <!-- Formulario -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex-1" style="min-width:320px;max-width:480px;">

            <!-- Header tarjeta -->
            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
              <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0"
                style="background:linear-gradient(135deg,#0d9488,#0891b2);">
                <svg class="w-5 h-5 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
              </div>
              <div>
                <p class="font-bold text-gray-800 text-sm">Configurar horario</p>
                <p class="text-xs text-gray-400">Médico, fechas, días y franjas horarias</p>
              </div>
            </div>

            <form id="form-crear-cupos" class="px-6 py-5 flex flex-col gap-5">

              <!-- Médico -->
              <div>
                <label class="tw-label">Médico <span style="color:#f87171;">*</span></label>
                <input type="hidden" name="medico_id" id="horario-medico-select">
                <div style="position:relative;" id="hor-drop-wrap">
                  <!-- Trigger -->
                  <div id="hor-drop-trigger" onclick="toggleHorDrop()"
                    style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;cursor:pointer;transition:border-color .15s;">
                    <div id="hor-drop-avatar" style="width:32px;height:32px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                      <svg style="width:14px;height:14px;stroke:#94a3b8;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div style="flex:1;min-width:0;">
                      <p id="hor-drop-label" style="font-size:13px;font-weight:600;color:#94a3b8;margin:0;">— Selecciona un médico —</p>
                      <p id="hor-drop-sub" style="font-size:11px;color:#cbd5e1;margin:0;display:none;"></p>
                    </div>
                    <svg id="hor-drop-chevron" style="width:14px;height:14px;stroke:#94a3b8;fill:none;flex-shrink:0;transition:transform .2s;" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                  </div>
                  <!-- Panel -->
                  <div id="hor-drop-panel"
                    style="display:none;position:absolute;top:calc(100% + 6px);left:0;right:0;background:#fff;border-radius:14px;border:1.5px solid #e2e8f0;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:50;overflow:hidden;">
                    <div style="padding:8px 10px;border-bottom:1px solid #f1f5f9;">
                      <div style="display:flex;align-items:center;gap:8px;background:#f8fafc;border-radius:8px;padding:6px 10px;">
                        <svg style="width:12px;height:12px;stroke:#94a3b8;fill:none;flex-shrink:0;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input id="hor-drop-search" type="text" placeholder="Buscar…" oninput="buildHorDropList()"
                          style="border:none;outline:none;background:transparent;font-size:12px;color:#374151;width:100%;">
                      </div>
                    </div>
                    <div id="hor-drop-list" style="max-height:220px;overflow-y:auto;padding:6px;"></div>
                  </div>
                </div>
              </div>

              <!-- Fechas -->
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="tw-label">Fecha inicio <span style="color:#f87171;">*</span></label>
                  <input id="fecha_inicio" type="date" name="fecha" required class="tw-field">
                </div>
                <div>
                  <label class="tw-label">Fecha fin <span style="color:#94a3b8;font-weight:400;text-transform:none;letter-spacing:0;">(opcional)</span></label>
                  <input id="fecha_fin" type="date" name="fecha_fin" class="tw-field">
                </div>
              </div>

              <!-- Días de la semana -->
              <div>
                <label class="tw-label">Días de atención</label>
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:6px;margin-top:4px;">
                  <?php foreach (['1'=>'L','2'=>'M','3'=>'X','4'=>'J','5'=>'V','6'=>'S','7'=>'D'] as $val => $letra): ?>
                    <button type="button" class="dia-btn" data-value="<?= $val ?>" onclick="toggleDia(this)"
                      style="padding:10px 4px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;color:#64748b;font-size:13px;font-weight:700;cursor:pointer;transition:all .15s;text-align:center;">
                      <?= $letra ?>
                    </button>
                    <input type="checkbox" name="dias[]" value="<?= $val ?>" id="dia-check-<?= $val ?>" style="display:none;">
                  <?php endforeach; ?>
                </div>
                <p style="font-size:11px;color:#94a3b8;margin-top:6px;">L=Lun · M=Mar · X=Mié · J=Jue · V=Vie · S=Sáb · D=Dom</p>
              </div>

              <!-- Horas -->
              <div>
                <label class="tw-label">Franja horaria <span style="color:#f87171;">*</span></label>
                <div style="display:grid;grid-template-columns:1fr auto 1fr;gap:8px;align-items:center;margin-top:4px;">
                  <!-- Hora inicio -->
                  <div style="display:flex;border:1.5px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#fff;">
                    <select id="hora_inicio_h" onchange="syncHora()"
                      style="flex:1;padding:10px 6px 10px 12px;border:none;outline:none;font-size:14px;color:#1f2937;background:transparent;cursor:pointer;appearance:none;-webkit-appearance:none;">
                      <?php for($h=0;$h<24;$h++) printf('<option value="%02d"%s>%02d</option>',$h,$h===7?' selected':'',$h); ?>
                    </select>
                    <span style="padding:0 4px;display:flex;align-items:center;color:#94a3b8;font-weight:700;font-size:15px;pointer-events:none;">:</span>
                    <select id="hora_inicio_m" onchange="syncHora()"
                      style="flex:1;padding:10px 12px 10px 6px;border:none;outline:none;font-size:14px;color:#1f2937;background:transparent;cursor:pointer;appearance:none;-webkit-appearance:none;">
                      <option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option>
                    </select>
                  </div>
                  <!-- Separador -->
                  <span style="text-align:center;font-size:13px;font-weight:700;color:#94a3b8;">hasta</span>
                  <!-- Hora fin -->
                  <div style="display:flex;border:1.5px solid #e2e8f0;border-radius:10px;overflow:hidden;background:#fff;">
                    <select id="hora_fin_h" onchange="syncHora()"
                      style="flex:1;padding:10px 6px 10px 12px;border:none;outline:none;font-size:14px;color:#1f2937;background:transparent;cursor:pointer;appearance:none;-webkit-appearance:none;">
                      <?php for($h=0;$h<24;$h++) printf('<option value="%02d"%s>%02d</option>',$h,$h===17?' selected':'',$h); ?>
                    </select>
                    <span style="padding:0 4px;display:flex;align-items:center;color:#94a3b8;font-weight:700;font-size:15px;pointer-events:none;">:</span>
                    <select id="hora_fin_m" onchange="syncHora()"
                      style="flex:1;padding:10px 12px 10px 6px;border:none;outline:none;font-size:14px;color:#1f2937;background:transparent;cursor:pointer;appearance:none;-webkit-appearance:none;">
                      <option value="00">00</option><option value="15">15</option><option value="30">30</option><option value="45">45</option>
                    </select>
                  </div>
                </div>
                <!-- Inputs ocultos que recibe el form -->
                <input type="hidden" name="hora_inicio" id="hora_inicio_val" value="07:00">
                <input type="hidden" name="hora_fin"    id="hora_fin_val"    value="17:00">
              </div>

              <!-- Intervalo -->
              <div>
                <label class="tw-label">Duración de cada turno <span style="color:#f87171;">*</span></label>
                <input type="hidden" name="intervalo" id="intervalo_val" value="30">
                <div id="preset-intervalo" style="display:grid;grid-template-columns:repeat(5,1fr);gap:6px;margin-top:4px;">
                  <?php foreach ([15=>'15m',20=>'20m',30=>'30m',45=>'45m',60=>'1h'] as $mins => $lbl): ?>
                    <button type="button" data-mins="<?= $mins ?>" onclick="setIntervalo(this)"
                      class="preset-int-btn"
                      style="padding:10px 4px;border:1.5px solid <?= $mins===30?'#0d9488':'#e2e8f0' ?>;border-radius:10px;
                             background:<?= $mins===30?'#0d9488':'#fff' ?>;
                             color:<?= $mins===30?'#fff':'#64748b' ?>;
                             font-size:13px;font-weight:700;cursor:pointer;transition:all .15s;text-align:center;
                             <?= $mins===30?'box-shadow:0 2px 8px rgba(13,148,136,.25);':'' ?>">
                      <?= $lbl ?>
                    </button>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Mensaje feedback -->
              <div id="msg-crear-cupos" class="hidden text-sm px-4 py-3 rounded-xl font-medium"></div>

              <!-- Submit -->
              <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white font-semibold text-sm hover:opacity-90 transition-all"
                style="background:linear-gradient(135deg,#0d9488,#0891b2);border:none;cursor:pointer;">
                <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 5v14M5 12h14"/>
                </svg>
                Generar cupos
              </button>

            </form>
          </div>

          <!-- Calendario -->
          <div class="flex-1" style="min-width:280px;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

              <!-- Header calendario -->
              <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <div>
                  <p class="font-bold text-gray-800 text-sm">Vista de horarios</p>
                  <p class="text-xs text-gray-400">Días con cupos generados</p>
                </div>
                <div class="flex items-center gap-1">
                  <button type="button" onclick="cambiarMes(-1)"
                    class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-all"
                    style="cursor:pointer;">
                    <svg class="w-4 h-4 stroke-gray-500 fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                  </button>
                  <span id="cal-titulo" class="text-sm font-bold px-3" style="color:#0d9488;min-width:130px;text-align:center;"></span>
                  <button type="button" onclick="cambiarMes(1)"
                    class="flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 transition-all"
                    style="cursor:pointer;">
                    <svg class="w-4 h-4 stroke-gray-500 fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                  </button>
                </div>
              </div>

              <div class="px-5 py-4">
                <!-- Cabecera días -->
                <div style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;text-align:center;margin-bottom:8px;">
                  <?php foreach (['Lu','Ma','Mi','Ju','Vi','Sa','Do'] as $d): ?>
                    <span style="font-size:11px;font-weight:700;color:#94a3b8;"><?= $d ?></span>
                  <?php endforeach; ?>
                </div>
                <!-- Grid días -->
                <div id="cal-grid" style="display:grid;grid-template-columns:repeat(7,1fr);gap:4px;"></div>

                <!-- Leyenda -->
                <div class="flex items-center gap-4 mt-4 pt-4 border-t border-gray-100">
                  <div class="flex items-center gap-1.5">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:4px;background:#0d9488;"></span>
                    <span style="font-size:11px;color:#64748b;">Con cupos · clic para editar</span>
                  </div>
                  <div class="flex items-center gap-1.5">
                    <span style="display:inline-block;width:12px;height:12px;border-radius:4px;background:#f1f5f9;border:1px solid #e2e8f0;"></span>
                    <span style="font-size:11px;color:#64748b;">Sin cupos</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

      <!-- VER CITAS -->
      <section class="seccion" id="seccion-ver-citas">

        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Citas</h1>
          <p class="text-sm text-gray-400 mt-1">Listado de todas las citas agendadas.</p>
        </div>

        <!-- Stats rápidas -->
        <div class="grid grid-cols-3 gap-4 mb-6" id="citas-stats">
          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-11 h-11 rounded-xl shrink-0" style="background:#f0fdfa;">
              <svg class="w-5 h-5 fill-none" style="stroke:#0d9488;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="stat-citas-total">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Total citas</p>
            </div>
          </div>
          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-11 h-11 rounded-xl shrink-0" style="background:#f0fdf4;">
              <svg class="w-5 h-5 fill-none" style="stroke:#16a34a;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="stat-citas-confirmadas">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Confirmadas</p>
            </div>
          </div>
          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-11 h-11 rounded-xl shrink-0" style="background:#fff7ed;">
              <svg class="w-5 h-5 fill-none" style="stroke:#f97316;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 12"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="stat-citas-pendientes">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Pendientes</p>
            </div>
          </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100" style="overflow:visible;">

          <!-- Toolbar -->
          <div class="flex items-center justify-between gap-4 px-6 py-4 border-b border-gray-100 flex-wrap">
            <div>
              <p class="font-bold text-gray-800 text-sm">Todas las citas</p>
              <p class="text-xs text-gray-400 mt-0.5">Se actualiza automáticamente cada 5 segundos</p>
            </div>
            <div class="flex items-center gap-3">
              <!-- Indicador live -->
              <div class="flex items-center gap-1.5">
                <span id="live-dot" style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e;animation:pulse-dot 2s infinite;"></span>
                <span style="font-size:12px;color:#64748b;font-weight:500;">En vivo</span>
              </div>
              <!-- Filtro médico custom -->
              <div style="position:relative;" id="citas-drop-wrap">
                <div id="citas-drop-trigger" onclick="toggleCitasDrop()"
                  style="display:flex;align-items:center;gap:8px;padding:8px 12px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;cursor:pointer;min-width:220px;transition:border-color .15s;">
                  <div id="citas-drop-avatar" style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:12px;height:12px;stroke:#fff;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                  </div>
                  <div style="flex:1;min-width:0;">
                    <p id="citas-drop-label" style="font-size:12px;font-weight:600;color:#374151;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Todos los médicos</p>
                    <p id="citas-drop-sub" style="font-size:10px;color:#94a3b8;margin:0;display:none;"></p>
                  </div>
                  <svg id="citas-drop-chevron" style="width:13px;height:13px;stroke:#94a3b8;fill:none;flex-shrink:0;transition:transform .2s;" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div id="citas-drop-panel"
                  style="display:none;position:absolute;top:calc(100% + 6px);right:0;min-width:260px;background:#fff;border-radius:14px;border:1.5px solid #e2e8f0;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:50;overflow:hidden;">
                  <div style="padding:8px 10px;border-bottom:1px solid #f1f5f9;">
                    <div style="display:flex;align-items:center;gap:8px;background:#f8fafc;border-radius:8px;padding:6px 10px;">
                      <svg style="width:12px;height:12px;stroke:#94a3b8;fill:none;flex-shrink:0;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                      <input id="citas-drop-search" type="text" placeholder="Buscar médico…" oninput="buildCitasDropList()"
                        style="border:none;outline:none;background:transparent;font-size:12px;color:#374151;width:100%;">
                    </div>
                  </div>
                  <div id="citas-drop-list" style="max-height:220px;overflow-y:auto;padding:6px;"></div>
                </div>
                <input type="hidden" id="admin-filtro-medico">
              </div>
            </div>
          </div>

          <!-- Tabla -->
          <div class="overflow-x-auto card-table" style="border-radius:0 0 16px 16px;overflow:hidden;">
            <table class="w-full text-sm">
              <thead>
                <tr>
                  <th class="px-5 py-3 text-left">Fecha / Hora</th>
                  <th class="px-5 py-3 text-left">Médico</th>
                  <th class="px-5 py-3 text-left">Paciente</th>
                  <th class="px-5 py-3 text-left">Estado</th>
                </tr>
              </thead>
              <tbody id="admin-citas-body"></tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- CUPOS -->
      <section class="seccion" id="seccion-cupos">

        <!-- Header -->
        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Gestión de Cupos</h1>
          <p class="text-sm text-gray-400 mt-1">Administra la disponibilidad de horarios por médico y día.</p>
        </div>

        <!-- Stats + filtro -->
        <div class="flex items-stretch gap-4 mb-6 flex-wrap">

          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100 flex-1" style="min-width:140px;">
            <div style="width:40px;height:40px;border-radius:12px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg style="width:20px;height:20px;stroke:#0d9488;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 12"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="cupo-stat-total">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Total cupos</p>
            </div>
          </div>

          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100 flex-1" style="min-width:140px;">
            <div style="width:40px;height:40px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg style="width:20px;height:20px;stroke:#16a34a;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="cupo-stat-disponibles">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Disponibles</p>
            </div>
          </div>

          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100 flex-1" style="min-width:140px;">
            <div style="width:40px;height:40px;border-radius:12px;background:#fef2f2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg style="width:20px;height:20px;stroke:#dc2626;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="cupo-stat-bloqueados">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Bloqueados</p>
            </div>
          </div>

          <!-- Filtro médico custom -->
          <div style="position:relative;min-width:260px;" id="cupo-drop-wrap">
            <!-- Trigger -->
            <div id="cupo-drop-trigger" onclick="toggleCupoDrop()"
              style="background:#fff;border-radius:16px;padding:12px 16px;border:1.5px solid #e2e8f0;display:flex;align-items:center;gap:10px;cursor:pointer;box-shadow:0 1px 4px rgba(0,0,0,.05);">
              <div id="cupo-drop-avatar" style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">
                <svg style="width:14px;height:14px;stroke:#fff;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
              </div>
              <div style="flex:1;min-width:0;">
                <p id="cupo-drop-label" style="font-weight:600;color:#1f2937;font-size:13px;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Todos los médicos</p>
                <p id="cupo-drop-sub" style="font-size:11px;color:#94a3b8;margin:0;">Mostrando todos</p>
              </div>
              <svg id="cupo-drop-chevron" style="width:14px;height:14px;stroke:#94a3b8;fill:none;flex-shrink:0;transition:transform .2s;" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <!-- Panel -->
            <div id="cupo-drop-panel"
              style="display:none;position:absolute;top:calc(100% + 8px);left:0;right:0;background:#fff;border-radius:16px;border:1.5px solid #e2e8f0;box-shadow:0 8px 24px rgba(0,0,0,.1);z-index:50;overflow:hidden;">
              <!-- Buscador -->
              <div style="padding:10px 12px;border-bottom:1px solid #f1f5f9;">
                <div style="display:flex;align-items:center;gap:8px;background:#f8fafc;border-radius:10px;padding:7px 10px;">
                  <svg style="width:13px;height:13px;stroke:#94a3b8;fill:none;flex-shrink:0;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                  <input id="cupo-drop-search" type="text" placeholder="Buscar médico…"
                    oninput="filterCupoDrop()"
                    style="border:none;outline:none;background:transparent;font-size:12px;color:#374151;width:100%;">
                </div>
              </div>
              <!-- Lista -->
              <div id="cupo-drop-list" style="max-height:240px;overflow-y:auto;padding:6px;"></div>
            </div>
            <!-- input hidden para compatibilidad -->
            <input type="hidden" id="admin-filtro-cupo">
          </div>

        </div>

        <!-- Cuerpo colapsible -->
        <div id="admin-cupos-body"></div>

      </section>

      <!-- MODAL AVISO: selecciona médico -->
      <div id="modal-aviso" class="modal-overlay">
        <div style="background:#fff;border-radius:20px;padding:32px;width:100%;max-width:360px;box-shadow:0 16px 48px rgba(0,0,0,0.15);text-align:center;">
          <div style="display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:16px;background:#fff7ed;margin:0 auto 16px;">
            <svg style="width:28px;height:28px;stroke:#f97316;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
          </div>
          <h3 style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 8px;">Selecciona un médico</h3>
          <p style="font-size:14px;color:#64748b;margin:0 0 24px;line-height:1.5;">Debes elegir un médico en el selector antes de ver o editar sus cupos del día.</p>
          <button onclick="document.getElementById('modal-aviso').style.display='none'"
            style="width:100%;padding:11px;border-radius:12px;border:none;cursor:pointer;font-size:14px;font-weight:600;color:#fff;background:linear-gradient(135deg,#0d9488,#0891b2);">
            Entendido
          </button>
        </div>
      </div>

    </main>
  </div><!-- fin área principal -->

  <!-- ── MODAL SYSTEM ──────────────────────────────────────────── -->
  <div id="modal-sys-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;padding:16px;">
    <div id="modal-sys-box" style="background:#fff;border-radius:20px;padding:28px 28px 24px;width:100%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,.18);text-align:center;position:relative;">
      <div id="modal-sys-icon" style="width:52px;height:52px;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;"></div>
      <h3 id="modal-sys-title" style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 8px;"></h3>
      <p id="modal-sys-msg"   style="font-size:14px;color:#64748b;margin:0 0 22px;line-height:1.55;"></p>
      <div id="modal-sys-btns" style="display:flex;gap:10px;"></div>
    </div>
  </div>
  <style>
    #modal-sys-overlay { display:none; }
    #modal-sys-overlay.open { display:flex; }
  </style>

  <script>
    // ── Modal system ──────────────────────────────────────────────────────────
    const Modal = (() => {
      const overlay = document.getElementById('modal-sys-overlay');
      const icon    = document.getElementById('modal-sys-icon');
      const title   = document.getElementById('modal-sys-title');
      const msg     = document.getElementById('modal-sys-msg');
      const btns    = document.getElementById('modal-sys-btns');

      const cfg = {
        success: { bg:'#f0fdf4', color:'#16a34a', svg:'<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>' },
        error:   { bg:'#fef2f2', color:'#dc2626', svg:'<circle cx="12" cy="12" r="9"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>' },
        warning: { bg:'#fffbeb', color:'#d97706', svg:'<path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>' },
        danger:  { bg:'#fef2f2', color:'#dc2626', svg:'<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>' },
        info:    { bg:'#f0fdfa', color:'#0d9488', svg:'<circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>' },
      };

      function btnStyle(primary, color) {
        return primary
          ? `flex:1;padding:11px;border-radius:12px;border:none;cursor:pointer;font-size:14px;font-weight:600;color:#fff;background:${color};`
          : `flex:1;padding:11px;border-radius:12px;border:1.5px solid #e2e8f0;cursor:pointer;font-size:14px;font-weight:600;color:#64748b;background:#fff;`;
      }

      function show({ t='', m='', type='info', isConfirm=false, okTxt='Entendido', confirmTxt='Confirmar', cancelTxt='Cancelar' }) {
        const c = cfg[type] || cfg.info;
        icon.style.background = c.bg;
        icon.innerHTML = `<svg style="width:24px;height:24px;stroke:${c.color};fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${c.svg}</svg>`;
        title.textContent = t || { success:'Éxito', error:'Error', warning:'Advertencia', danger:'¡Cuidado!', info:'Información' }[type];
        msg.textContent = m;

        return new Promise(resolve => {
          btns.innerHTML = '';
          if (isConfirm) {
            const btnCancel  = document.createElement('button');
            btnCancel.textContent = cancelTxt;
            btnCancel.style.cssText = btnStyle(false, c.color);
            btnCancel.onclick = () => { close(); resolve(false); };

            const btnOk = document.createElement('button');
            btnOk.textContent = confirmTxt;
            btnOk.style.cssText = btnStyle(true, c.color);
            btnOk.onclick = () => { close(); resolve(true); };

            btns.appendChild(btnCancel);
            btns.appendChild(btnOk);
          } else {
            const btnOk = document.createElement('button');
            btnOk.textContent = okTxt;
            btnOk.style.cssText = `width:100%;padding:11px;border-radius:12px;border:none;cursor:pointer;font-size:14px;font-weight:600;color:#fff;background:${c.color};`;
            btnOk.onclick = () => { close(); resolve(true); };
            btns.appendChild(btnOk);
          }
          overlay.classList.add('open');
        });
      }

      function close() { overlay.classList.remove('open'); }
      overlay.addEventListener('click', e => { if (e.target === overlay) close(); });

      return {
        alert:   (m, t='', type='info')    => show({ t, m, type }),
        success: (m, t='')                 => show({ t, m, type:'success' }),
        error:   (m, t='Error')            => show({ t, m, type:'error' }),
        warning: (m, t='Advertencia')      => show({ t, m, type:'warning' }),
        confirm: (m, t='', type='warning') => show({ t, m, type, isConfirm:true }),
        danger:  (m, t='¡Cuidado!')        => show({ t, m, type:'danger', isConfirm:true }),
      };
    })();
  </script>

  <script>
    // ── Estado global ──────────────────────────────────────────────────────
    let medicosData  = [];
    let cuposData    = [];
    let adminCitas   = [];

    // ── Logout ─────────────────────────────────────────────────────────────
    document.getElementById('btn-logout').addEventListener('click', async function(e) {
      e.preventDefault();
      await fetch('gateway.php?svc=auth&action=logout', { method:'POST' });
      location.href = '/login';
    });

    // ── Sidebar ─────────────────────────────────────────────────────────────
    const titulosPorSeccion = {
      'inicio':          'Resumen general',
      'crear-medico':    'Crear perfil de médico',
      'crear-horarios':  'Crear horarios de trabajo',
      'ver-citas':       'Ver citas',
      'cupos':           'Gestión de Cupos',
    };
    function activarSeccion(seccion) {
      const secciones = Object.keys(titulosPorSeccion);
      const sec = secciones.includes(seccion) ? seccion : 'inicio';
      document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('activo'));
      document.querySelectorAll('.seccion').forEach(s => s.classList.remove('activa'));
      const link = document.querySelector(`.nav-link[data-seccion="${sec}"]`);
      if (link) link.classList.add('activo');
      document.getElementById('seccion-' + sec).classList.add('activa');
      document.getElementById('topbar-titulo').textContent = titulosPorSeccion[sec] || '';
      location.hash = sec;
    }

    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', e => {
        e.preventDefault();
        activarSeccion(link.dataset.seccion);
      });
    });

    // Restaurar sección desde el hash al cargar
    const hashInicial = location.hash.replace('#', '');
    if (hashInicial) activarSeccion(hashInicial);

    // ── Helpers ─────────────────────────────────────────────────────────────
    function convertTo12HourJS(t) {
      const [h24, min] = t.split(':');
      let h = parseInt(h24);
      const ap = h >= 12 ? 'p.m' : 'a.m';
      if (h > 12) h -= 12; if (h === 0) h = 12;
      return String(h).padStart(2,'0') + ':' + min + ' ' + ap;
    }

    function poblarSelect(selectId, items, valorFn, textoFn, vacio = 'Todos los médicos') {
      const sel = document.getElementById(selectId);
      const prev = sel.value;
      sel.innerHTML = `<option value="">${vacio}</option>` +
        items.map(i => `<option value="${valorFn(i)}">${textoFn(i)}</option>`).join('');
      if (prev) sel.value = prev;
    }

    // ── Carga de datos del gateway ──────────────────────────────────────────
    async function cargarTodo() {
      const [medicos, citas, cupos, stats] = await Promise.all([
        fetch('gateway.php?svc=doctors&action=listar').then(r=>r.json()),
        fetch('gateway.php?svc=appointments&action=listar_admin').then(r=>r.json()),
        fetch('gateway.php?svc=schedules&action=listar_admin').then(r=>r.json()),
        fetch('gateway.php?svc=appointments&action=stats').then(r=>r.json()),
      ]);

      medicosData = Array.isArray(medicos) ? medicos : [];
      adminCitas  = Array.isArray(citas)   ? citas   : [];
      cuposData   = Array.isArray(cupos)   ? cupos   : [];

      // Stats
      if (stats.ok) {
        document.getElementById('stat-pacientes').textContent = stats.total_pacientes;
        document.getElementById('stat-medicos').textContent   = stats.total_medicos;
        document.getElementById('stat-citas').textContent     = stats.total_citas;
      }

      // Tabla de médicos — llamar vía función para soportar búsqueda
      renderMedicos();

      // Especialidades en crear médico
      const esps = [...new Set(medicosData.map(m => m.especialidad))].sort();
      document.getElementById('select-especialidad-crear').innerHTML =
        '<option value="">Selecciona una especialidad</option>' +
        esps.map(e => `<option value="${e}">${e}</option>`).join('');

      // Selects de médico en filtros y formularios
      buildCitasDropList();
      buildCupoDropList(medicosData);
      buildHorDropList();

      // Min de fechas
      const hoy = new Date().toISOString().split('T')[0];
      document.getElementById('fecha_inicio').min = hoy;
      document.getElementById('fecha_fin').min    = hoy;

      renderCitas();
      renderCupos();
      renderCalendario();
    }

    // ── Ver Citas ───────────────────────────────────────────────────────────
    const estadoConfig = {
      confirmada: { bg:'#f0fdf4', color:'#16a34a', label:'Confirmada' },
      pendiente:  { bg:'#fff7ed', color:'#ea580c', label:'Pendiente'  },
      cancelada:  { bg:'#fef2f2', color:'#dc2626', label:'Cancelada'  },
    };

    function renderCitas() {
      const medicoId = document.getElementById('admin-filtro-medico').value;
      const lista = adminCitas.filter(c => !medicoId || c.medico_id == medicoId);

      // Stats
      const total       = lista.length;
      const confirmadas = lista.filter(c => c.estado === 'confirmada').length;
      const pendientes  = lista.filter(c => c.estado === 'pendiente').length;
      document.getElementById('stat-citas-total').textContent       = total;
      document.getElementById('stat-citas-confirmadas').textContent = confirmadas;
      document.getElementById('stat-citas-pendientes').textContent  = pendientes;

      if (!lista.length) {
        document.getElementById('admin-citas-body').innerHTML =
          '<tr><td colspan="4" style="text-align:center;padding:40px;color:#94a3b8;font-size:14px;">No hay citas registradas.</td></tr>';
        return;
      }

      const [anioH, mesH, diaH] = new Date().toISOString().split('T')[0].split('-');
      const hoy = `${anioH}-${mesH}-${diaH}`;

      document.getElementById('admin-citas-body').innerHTML = lista.map((c, i) => {
        const cfg = estadoConfig[c.estado] || { bg:'#f1f5f9', color:'#64748b', label: c.estado };
        const [anio, mes, dia] = c.fecha.split('-');
        const fechaFmt = `${dia}/${mes}/${anio}`;
        const esHoy = c.fecha === hoy;

        return `<tr style="${i % 2 !== 0 ? 'background:#f9fafb;' : ''}">
          <td class="px-5 py-3">
            <div class="flex items-center gap-2">
              ${esHoy ? `<span style="background:#0d9488;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">Hoy</span>` : ''}
              <div>
                <p style="font-weight:600;color:#1f2937;font-size:13px;">${fechaFmt}</p>
                <p style="color:#94a3b8;font-size:12px;">${convertTo12HourJS(c.hora)}</p>
              </div>
            </div>
          </td>
          <td class="px-5 py-3">
            <div class="flex items-center gap-2.5">
              <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0;">
                ${c.medico.charAt(0).toUpperCase()}
              </div>
              <div>
                <p style="font-weight:600;color:#1f2937;font-size:13px;">${c.medico}</p>
                <p style="color:#94a3b8;font-size:12px;">${c.especialidad}</p>
              </div>
            </div>
          </td>
          <td class="px-5 py-3">
            <p style="font-weight:600;color:#1f2937;font-size:13px;">${c.paciente}</p>
            <p style="color:#94a3b8;font-size:12px;">${c.paciente_email}</p>
          </td>
          <td class="px-5 py-3">
            <span style="background:${cfg.bg};color:${cfg.color};padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
              ${cfg.label}
            </span>
          </td>
        </tr>`;
      }).join('');
    }
    // filtro citas manejado por dropdown custom (selectCitasMedico)

    // Polling citas cada 5 segundos
    setInterval(async () => {
      const r = await fetch('gateway.php?svc=appointments&action=listar_admin').catch(()=>null);
      if (!r) return;
      const data = await r.json().catch(()=>null);
      if (Array.isArray(data)) { adminCitas = data; renderCitas(); }
    }, 5000);

    // ── Dropdown filtro cupos ────────────────────────────────────────────────
    const coloresDrop = ['#0d9488','#0891b2','#6366f1','#f59e0b','#ec4899','#14b8a6','#8b5cf6','#f97316'];
    let cupoDropOpen = false;

    function toggleCupoDrop() {
      cupoDropOpen = !cupoDropOpen;
      const panel   = document.getElementById('cupo-drop-panel');
      const chevron = document.getElementById('cupo-drop-chevron');
      panel.style.display   = cupoDropOpen ? 'block' : 'none';
      chevron.style.transform = cupoDropOpen ? 'rotate(180deg)' : 'rotate(0deg)';
      if (cupoDropOpen) {
        document.getElementById('cupo-drop-search').focus();
        setTimeout(() => document.addEventListener('click', closeCupoDropOutside), 0);
      }
    }

    function closeCupoDropOutside(e) {
      if (!document.getElementById('cupo-drop-wrap').contains(e.target)) {
        cupoDropOpen = false;
        document.getElementById('cupo-drop-panel').style.display = 'none';
        document.getElementById('cupo-drop-chevron').style.transform = 'rotate(0deg)';
        document.removeEventListener('click', closeCupoDropOutside);
      }
    }

    function selectCupoMedico(id, nombre, especialidad, colorIdx) {
      const color = coloresDrop[colorIdx % coloresDrop.length];
      document.getElementById('admin-filtro-cupo').value = id;

      const avatar = document.getElementById('cupo-drop-avatar');
      const label  = document.getElementById('cupo-drop-label');
      const sub    = document.getElementById('cupo-drop-sub');

      if (id === '') {
        avatar.style.background = 'linear-gradient(135deg,#0d9488,#0891b2)';
        avatar.innerHTML = `<svg style="width:14px;height:14px;stroke:#fff;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`;
        label.textContent = 'Todos los médicos';
        sub.textContent   = 'Mostrando todos';
      } else {
        avatar.style.background = color;
        avatar.innerHTML = `<span style="font-size:13px;font-weight:700;">${nombre.charAt(0).toUpperCase()}</span>`;
        label.textContent = nombre;
        sub.textContent   = especialidad;
      }

      toggleCupoDrop();
      renderCupos();
    }

    function buildCupoDropList(medicos) {
      const list = document.getElementById('cupo-drop-list');
      const q    = (document.getElementById('cupo-drop-search')?.value || '').toLowerCase();

      const items = medicos.filter(m =>
        !q || m.nombre.toLowerCase().includes(q) || m.especialidad.toLowerCase().includes(q)
      );

      const allItem = !q ? `
        <div onclick="selectCupoMedico('','','',-1)"
          style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;cursor:pointer;margin-bottom:2px;"
          onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
          <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:14px;height:14px;stroke:#fff;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
          </div>
          <div>
            <p style="font-weight:600;color:#1f2937;font-size:13px;margin:0;">Todos los médicos</p>
            <p style="font-size:11px;color:#94a3b8;margin:0;">Ver cupos de todos</p>
          </div>
        </div>` : '';

      const rows = items.map((m, i) => {
        const color = coloresDrop[i % coloresDrop.length];
        const cuposM = cuposData.filter(c => c.medico_id == m.id).length;
        return `
        <div onclick="selectCupoMedico(${m.id},'${m.nombre.replace(/'/g,"\\'")}','${m.especialidad.replace(/'/g,"\\'")}',${i})"
          style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;cursor:pointer;margin-bottom:2px;"
          onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
          <div style="width:34px;height:34px;border-radius:50%;background:${color};display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;flex-shrink:0;">
            ${m.nombre.charAt(0).toUpperCase()}
          </div>
          <div style="flex:1;min-width:0;">
            <p style="font-weight:600;color:#1f2937;font-size:13px;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.nombre}</p>
            <p style="font-size:11px;color:#94a3b8;margin:0;">${m.especialidad}</p>
          </div>
          <span style="font-size:11px;font-weight:700;color:#94a3b8;flex-shrink:0;">${cuposM} cupos</span>
        </div>`;
      }).join('');

      list.innerHTML = allItem + rows || '<p style="text-align:center;color:#94a3b8;font-size:12px;padding:16px;">Sin resultados</p>';
    }

    function filterCupoDrop() { buildCupoDropList(medicosData); }

    // ── Dropdown filtro citas ─────────────────────────────────────────────────
    let citasDropOpen = false;

    function toggleCitasDrop() {
      citasDropOpen = !citasDropOpen;
      document.getElementById('citas-drop-panel').style.display     = citasDropOpen ? 'block' : 'none';
      document.getElementById('citas-drop-chevron').style.transform  = citasDropOpen ? 'rotate(180deg)' : 'rotate(0deg)';
      document.getElementById('citas-drop-trigger').style.borderColor = citasDropOpen ? '#0d9488' : '#e2e8f0';
      if (citasDropOpen) {
        document.getElementById('citas-drop-search').focus();
        setTimeout(() => document.addEventListener('click', closeCitasDropOutside), 0);
      }
    }

    function closeCitasDropOutside(e) {
      if (!document.getElementById('citas-drop-wrap').contains(e.target)) {
        citasDropOpen = false;
        document.getElementById('citas-drop-panel').style.display     = 'none';
        document.getElementById('citas-drop-chevron').style.transform  = 'rotate(0deg)';
        document.getElementById('citas-drop-trigger').style.borderColor = '#e2e8f0';
        document.removeEventListener('click', closeCitasDropOutside);
      }
    }

    function selectCitasMedico(id, nombre, especialidad, colorIdx) {
      const color = coloresDrop[colorIdx % coloresDrop.length];
      document.getElementById('admin-filtro-medico').value = id;

      const avatar = document.getElementById('citas-drop-avatar');
      const label  = document.getElementById('citas-drop-label');
      const sub    = document.getElementById('citas-drop-sub');

      if (id === '') {
        avatar.style.background = 'linear-gradient(135deg,#0d9488,#0891b2)';
        avatar.innerHTML = `<svg style="width:12px;height:12px;stroke:#fff;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>`;
        label.textContent = 'Todos los médicos';
        label.style.color = '#374151';
        sub.style.display = 'none';
      } else {
        avatar.style.background = color;
        avatar.innerHTML = `<span style="font-size:12px;font-weight:700;color:#fff;">${nombre.charAt(0).toUpperCase()}</span>`;
        label.textContent = nombre;
        label.style.color = '#1f2937';
        sub.textContent   = especialidad;
        sub.style.display = 'block';
      }

      toggleCitasDrop();
      renderCitas();
    }

    function buildCitasDropList() {
      const list = document.getElementById('citas-drop-list');
      const q    = (document.getElementById('citas-drop-search')?.value || '').toLowerCase();
      const items = medicosData.filter(m =>
        !q || m.nombre.toLowerCase().includes(q) || m.especialidad.toLowerCase().includes(q)
      );

      const allItem = !q ? `
        <div onclick="selectCitasMedico('','','',-1)"
          style="display:flex;align-items:center;gap:10px;padding:7px 10px;border-radius:10px;cursor:pointer;margin-bottom:2px;"
          onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
          <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <svg style="width:13px;height:13px;stroke:#fff;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
          </div>
          <div>
            <p style="font-weight:600;color:#1f2937;font-size:12px;margin:0;">Todos los médicos</p>
            <p style="font-size:11px;color:#94a3b8;margin:0;">Ver todas las citas</p>
          </div>
        </div>` : '';

      const rows = items.map((m, i) => {
        const color   = coloresDrop[i % coloresDrop.length];
        const citasM  = adminCitas.filter(c => c.medico_id == m.id).length;
        return `
        <div onclick="selectCitasMedico(${m.id},'${m.nombre.replace(/'/g,"\\'")}','${m.especialidad.replace(/'/g,"\\'")}',${i})"
          style="display:flex;align-items:center;gap:10px;padding:7px 10px;border-radius:10px;cursor:pointer;margin-bottom:2px;"
          onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
          <div style="width:32px;height:32px;border-radius:50%;background:${color};display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;font-weight:700;flex-shrink:0;">
            ${m.nombre.charAt(0).toUpperCase()}
          </div>
          <div style="flex:1;min-width:0;">
            <p style="font-weight:600;color:#1f2937;font-size:12px;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.nombre}</p>
            <p style="font-size:11px;color:#94a3b8;margin:0;">${m.especialidad}</p>
          </div>
          ${citasM > 0 ? `<span style="font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:#f0fdfa;color:#0d9488;flex-shrink:0;">${citasM}</span>` : ''}
        </div>`;
      }).join('');

      list.innerHTML = allItem + (rows || '<p style="text-align:center;color:#94a3b8;font-size:12px;padding:14px;">Sin resultados</p>');
    }

    // ── Dropdown médico en Crear Horarios ────────────────────────────────────
    let horDropOpen = false;

    function toggleHorDrop() {
      horDropOpen = !horDropOpen;
      document.getElementById('hor-drop-panel').style.display   = horDropOpen ? 'block' : 'none';
      document.getElementById('hor-drop-chevron').style.transform = horDropOpen ? 'rotate(180deg)' : 'rotate(0deg)';
      document.getElementById('hor-drop-trigger').style.borderColor = horDropOpen ? '#0d9488' : '#e2e8f0';
      if (horDropOpen) {
        document.getElementById('hor-drop-search').focus();
        setTimeout(() => document.addEventListener('click', closeHorDropOutside), 0);
      }
    }

    function closeHorDropOutside(e) {
      if (!document.getElementById('hor-drop-wrap').contains(e.target)) {
        horDropOpen = false;
        document.getElementById('hor-drop-panel').style.display    = 'none';
        document.getElementById('hor-drop-chevron').style.transform = 'rotate(0deg)';
        document.getElementById('hor-drop-trigger').style.borderColor = '#e2e8f0';
        document.removeEventListener('click', closeHorDropOutside);
      }
    }

    function selectHorMedico(id, nombre, especialidad, colorIdx) {
      const color = coloresDrop[colorIdx % coloresDrop.length];
      document.getElementById('horario-medico-select').value = id;

      const avatar = document.getElementById('hor-drop-avatar');
      const label  = document.getElementById('hor-drop-label');
      const sub    = document.getElementById('hor-drop-sub');

      avatar.style.background = color;
      avatar.innerHTML = `<span style="font-size:13px;font-weight:700;color:#fff;">${nombre.charAt(0).toUpperCase()}</span>`;
      label.style.color = '#1f2937';
      label.textContent = nombre;
      sub.textContent   = especialidad;
      sub.style.display = 'block';

      toggleHorDrop();
      renderCalendario();
    }

    function buildHorDropList() {
      const list = document.getElementById('hor-drop-list');
      const q    = (document.getElementById('hor-drop-search')?.value || '').toLowerCase();
      const items = medicosData.filter(m =>
        !q || m.nombre.toLowerCase().includes(q) || m.especialidad.toLowerCase().includes(q)
      );

      if (!items.length) {
        list.innerHTML = '<p style="text-align:center;color:#94a3b8;font-size:12px;padding:14px;">Sin resultados</p>';
        return;
      }

      list.innerHTML = items.map((m, i) => {
        const color = coloresDrop[i % coloresDrop.length];
        const cuposM = cuposData.filter(c => c.medico_id == m.id).length;
        return `
        <div onclick="selectHorMedico(${m.id},'${m.nombre.replace(/'/g,"\\'")}','${m.especialidad.replace(/'/g,"\\'")}',${i})"
          style="display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;cursor:pointer;margin-bottom:2px;"
          onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
          <div style="width:34px;height:34px;border-radius:50%;background:${color};display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;flex-shrink:0;">
            ${m.nombre.charAt(0).toUpperCase()}
          </div>
          <div style="flex:1;min-width:0;">
            <p style="font-weight:600;color:#1f2937;font-size:13px;margin:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${m.nombre}</p>
            <p style="font-size:11px;color:#94a3b8;margin:0;">${m.especialidad}</p>
          </div>
          ${cuposM > 0 ? `<span style="font-size:11px;font-weight:700;color:#94a3b8;flex-shrink:0;">${cuposM} cupos</span>` : ''}
        </div>`;
      }).join('');
    }

    // ── Tabla médicos ────────────────────────────────────────────────────────
    function renderMedicos() {
      const q = (document.getElementById('buscar-medico')?.value || '').toLowerCase();
      const lista = medicosData.filter(m =>
        !q || m.nombre.toLowerCase().includes(q) || m.especialidad.toLowerCase().includes(q) || m.email.toLowerCase().includes(q)
      );

      const colores = ['#0d9488','#0891b2','#6366f1','#f59e0b','#ec4899','#14b8a6'];

      document.getElementById('tbody-medicos').innerHTML = lista.length
        ? lista.map((m, i) => {
            const color = colores[i % colores.length];
            return `<tr style="border-bottom:1px solid #f1f5f9;${i % 2 !== 0 ? 'background:#fafafa;' : ''}">
              <td style="padding:14px 24px;">
                <div style="display:flex;align-items:center;gap:12px;">
                  <div style="width:38px;height:38px;border-radius:50%;background:${color};display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:700;flex-shrink:0;">
                    ${m.nombre.charAt(0).toUpperCase()}
                  </div>
                  <div>
                    <p style="font-weight:600;color:#1f2937;font-size:14px;margin:0;">${m.nombre}</p>
                    <p style="color:#94a3b8;font-size:12px;margin:0;">ID #${m.id}</p>
                  </div>
                </div>
              </td>
              <td style="padding:14px 24px;">
                <span style="background:${color}18;color:${color};padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">
                  ${m.especialidad}
                </span>
              </td>
              <td style="padding:14px 24px;">
                <span style="color:#64748b;font-size:13px;">${m.email}</span>
              </td>
              <td style="padding:14px 24px;text-align:right;white-space:nowrap;">
                <button onclick="abrirEditar(${m.id},'${m.nombre.replace(/'/g,"\\'")}','${m.especialidad.replace(/'/g,"\\'")}','${m.email.replace(/'/g,"\\'")}');"
                  style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border:1.5px solid #e2e8f0;border-radius:8px;background:#fff;color:#374151;font-size:12px;font-weight:600;cursor:pointer;margin-right:6px;transition:border-color .15s,color .15s;"
                  onmouseover="this.style.borderColor='#0d9488';this.style.color='#0d9488';"
                  onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#374151';">
                  <svg style="width:13px;height:13px;stroke:currentColor;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Editar
                </button>
                <button onclick="eliminarMedico(${m.id},'${m.nombre.replace(/'/g,"\\'")}');"
                  style="display:inline-flex;align-items:center;gap:6px;padding:6px 14px;border:1.5px solid #fecaca;border-radius:8px;background:#fef2f2;color:#ef4444;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s,color .15s,border-color .15s;"
                  onmouseover="this.style.background='#ef4444';this.style.color='#fff';this.style.borderColor='#ef4444';"
                  onmouseout="this.style.background='#fef2f2';this.style.color='#ef4444';this.style.borderColor='#fecaca';">
                  <svg style="width:13px;height:13px;stroke:currentColor;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                  Eliminar
                </button>
              </td>
            </tr>`;
          }).join('')
        : `<tr><td colspan="4" style="padding:48px 24px;text-align:center;">
            <div style="display:flex;flex-direction:column;align-items:center;gap:8px;">
              <svg style="width:40px;height:40px;stroke:#cbd5e1;fill:none;" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <p style="color:#94a3b8;font-size:14px;font-weight:500;margin:0;">${q ? 'Sin resultados para "'+q+'"' : 'No hay médicos registrados aún.'}</p>
              ${q ? '<p style="color:#cbd5e1;font-size:12px;margin:0;">Intenta con otro nombre o especialidad.</p>' : ''}
            </div>
          </td></tr>`;
    }

    // ── Cupos ───────────────────────────────────────────────────────────────
    const coloresMedicos = ['#0d9488','#0891b2','#6366f1','#f59e0b','#ec4899','#14b8a6','#8b5cf6','#f97316'];
    let cuposAbiertos = {}; // estado de colapsibles { "medicoId|fecha": true/false }

    function safeId(key) { return key.replace(/[^a-zA-Z0-9]/g, '_'); }

    function toggleCollapse(key) {
      cuposAbiertos[key] = !cuposAbiertos[key];
      const sid  = safeId(key);
      const body  = document.getElementById('collapse-' + sid);
      const arrow = document.getElementById('arrow-'    + sid);
      if (body)  body.style.display       = cuposAbiertos[key] ? 'block' : 'none';
      if (arrow) arrow.style.transform    = cuposAbiertos[key] ? 'rotate(180deg)' : 'rotate(0deg)';
    }

    function renderCupos() {
      const medicoFiltro = document.getElementById('admin-filtro-cupo').value;
      const filtered = cuposData.filter(c => !medicoFiltro || c.medico_id == medicoFiltro);
      const cont = document.getElementById('admin-cupos-body');

      // Stats globales
      const totalC      = filtered.length;
      const dispC       = filtered.filter(c => c.disponible).length;
      const bloqC       = filtered.filter(c => !c.disponible).length;
      document.getElementById('cupo-stat-total').textContent       = totalC;
      document.getElementById('cupo-stat-disponibles').textContent = dispC;
      document.getElementById('cupo-stat-bloqueados').textContent  = bloqC;

      if (!totalC) {
        cont.innerHTML = `
          <div style="background:#fff;border-radius:20px;padding:48px 24px;text-align:center;border:1px solid #f1f5f9;">
            <svg style="width:48px;height:48px;stroke:#cbd5e1;fill:none;margin:0 auto 12px;" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 12"/></svg>
            <p style="color:#94a3b8;font-size:15px;font-weight:500;">No hay cupos próximos.</p>
          </div>`;
        return;
      }

      // Agrupar por médico → fecha
      const grupos = {};
      filtered.forEach(c => {
        const key = c.medico_id + '|' + c.medico;
        if (!grupos[key]) grupos[key] = {};
        if (!grupos[key][c.fecha]) grupos[key][c.fecha] = [];
        grupos[key][c.fecha].push(c);
      });

      const diasSemana = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
      const mesesCortos = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];

      cont.innerHTML = Object.entries(grupos).map(([key, fechas], gi) => {
        const mId = key.split('|')[0];
        const nombre = key.split('|')[1];
        const color = coloresMedicos[gi % coloresMedicos.length];
        const totalMedico = Object.values(fechas).flat().length;
        const dispMedico  = Object.values(fechas).flat().filter(c => c.disponible).length;
        const bloqMedico  = totalMedico - dispMedico;
        const diasCount   = Object.keys(fechas).length;

        const diasHTML = Object.entries(fechas).sort(([a],[b]) => a.localeCompare(b)).map(([fecha, cupos]) => {
          const colKey = mId + '|' + fecha;
          if (!(colKey in cuposAbiertos)) cuposAbiertos[colKey] = false;

          const total      = cupos.length;
          const bloq       = cupos.filter(c => !c.disponible).length;
          const disp       = total - bloq;
          const todoBloq   = bloq === total;
          const parcial    = bloq > 0 && !todoBloq;

          const [y, m, d]  = fecha.split('-');
          const dateObj    = new Date(+y, +m-1, +d);
          const diaSemana  = diasSemana[dateObj.getDay()];
          const mesCorto   = mesesCortos[+m-1];

          const badgeColor = todoBloq ? '#dc2626' : parcial ? '#d97706' : '#16a34a';
          const badgeBg    = todoBloq ? '#fef2f2' : parcial ? '#fffbeb' : '#f0fdf4';
          const badgeTxt   = todoBloq ? 'Bloqueado' : parcial ? `Parcial ${disp}/${total}` : `Disponible ${total}`;

          // Barra de progreso
          const pct = total > 0 ? Math.round((disp / total) * 100) : 0;
          const barColor = todoBloq ? '#ef4444' : parcial ? '#f59e0b' : '#22c55e';

          const isOpen = cuposAbiertos[colKey];

          const sid = safeId(colKey);

          const filasHTML = cupos.sort((a,b) => a.hora.localeCompare(b.hora)).map((c, ci) => `
            <div style="display:flex;align-items:center;gap:10px;padding:7px 14px;${ci%2!==0?'background:#fafafa;':''}${ci<cupos.length-1?'border-bottom:1px solid #f8fafc;':''}">
              <span style="font-weight:600;color:#374151;font-size:12px;min-width:80px;">${convertTo12HourJS(c.hora.substring(0,5))}</span>
              <span style="flex:1;font-size:11px;font-weight:700;padding:2px 8px;border-radius:20px;background:${c.disponible?'#f0fdf4':'#fef2f2'};color:${c.disponible?'#16a34a':'#dc2626'};display:inline-block;max-width:fit-content;">
                ${c.disponible ? 'Disponible' : 'Bloqueado'}
              </span>
              <button onclick="bloquearCupo(${c.id},${c.disponible},this)"
                style="padding:4px 10px;border-radius:7px;border:1.5px solid ${c.disponible?'#fecaca':'#d1fae5'};background:${c.disponible?'#fef2f2':'#f0fdfa'};color:${c.disponible?'#dc2626':'#0d9488'};font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;">
                ${c.disponible ? '🔒 Bloquear' : '🔓 Desbloquear'}
              </button>
            </div>`).join('');

          return `
            <div style="border:1px solid #f1f5f9;border-radius:12px;margin-bottom:6px;overflow:hidden;background:#fff;">
              <div onclick="toggleCollapse('${colKey}')"
                style="display:flex;align-items:center;gap:10px;padding:9px 14px;cursor:pointer;user-select:none;background:${isOpen?'#f8fafc':'#fff'};"
                onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='${isOpen?'#f8fafc':'#fff'}'">

                <div style="width:36px;height:36px;flex-shrink:0;text-align:center;background:${color}15;border-radius:8px;display:flex;flex-direction:column;align-items:center;justify-content:center;line-height:1;">
                  <span style="font-size:9px;font-weight:700;color:${color};text-transform:uppercase;">${mesCorto}</span>
                  <span style="font-size:15px;font-weight:800;color:${color};">${d}</span>
                </div>

                <div style="flex:1;min-width:0;">
                  <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:13px;font-weight:700;color:#1f2937;">${diaSemana} ${d}/${+m}/${y.slice(2)}</span>
                    <span style="padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;background:${badgeBg};color:${badgeColor};">${badgeTxt}</span>
                  </div>
                  <div style="margin-top:4px;height:3px;border-radius:99px;background:#f1f5f9;overflow:hidden;max-width:160px;">
                    <div style="height:100%;width:${pct}%;background:${barColor};border-radius:99px;"></div>
                  </div>
                </div>

                <button onclick="event.stopPropagation();bloquearDia('${mId}','${fecha}',${todoBloq?1:0})"
                  style="padding:5px 10px;border-radius:8px;border:1.5px solid ${todoBloq?'#d1fae5':'#fecaca'};background:${todoBloq?'#f0fdfa':'#fef2f2'};color:${todoBloq?'#0d9488':'#dc2626'};font-size:11px;font-weight:700;cursor:pointer;white-space:nowrap;flex-shrink:0;">
                  ${todoBloq ? '🔓 Desbloquear día' : '🔒 Bloquear día'}
                </button>

                <svg id="arrow-${sid}" style="width:14px;height:14px;stroke:#94a3b8;fill:none;flex-shrink:0;transition:transform .2s;transform:${isOpen?'rotate(180deg)':'rotate(0deg)'};" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
              </div>

              <div id="collapse-${sid}" style="display:${isOpen?'block':'none'};border-top:1px solid #f1f5f9;">
                ${filasHTML}
              </div>
            </div>`;
        }).join('');

        return `
          <div style="margin-bottom:16px;">
            <div style="background:#fff;border-radius:12px;padding:11px 16px;margin-bottom:6px;border:1px solid #f1f5f9;display:flex;align-items:center;gap:10px;">
              <div style="width:34px;height:34px;border-radius:50%;background:${color};display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;font-weight:800;flex-shrink:0;">
                ${nombre.charAt(0).toUpperCase()}
              </div>
              <div style="flex:1;">
                <p style="font-weight:700;color:#1f2937;font-size:13px;margin:0;">${nombre}</p>
                <p style="color:#94a3b8;font-size:11px;margin:0;">${diasCount} día${diasCount!==1?'s':''} · ${totalMedico} cupos</p>
              </div>
              <div style="display:flex;gap:6px;">
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#f0fdf4;color:#16a34a;">${dispMedico} libres</span>
                ${bloqMedico > 0 ? `<span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:#fef2f2;color:#dc2626;">${bloqMedico} bloq.</span>` : ''}
              </div>
            </div>
            ${diasHTML}
          </div>`;
      }).join('');
    }
    // filtro cupos manejado por dropdown custom (selectCupoMedico)

    // ── Bloquear cupo ────────────────────────────────────────────────────────
    async function bloquearCupo(id, disponible, btn) {
      const estaDisponible = parseInt(disponible) === 1;
      const accion = estaDisponible ? 'bloquear' : 'desbloquear';
      if (!await Modal.confirm(`¿Deseas ${accion} este cupo?`, `${accion.charAt(0).toUpperCase()+accion.slice(1)} cupo`, 'warning')) return;
      const nuevoValor = estaDisponible ? 0 : 1;
      const body = `id=${id}&disponible=${nuevoValor}`;
      try {
        const r = await fetch('gateway.php?svc=schedules&action=bloquear', {
          method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body
        });
        const d = await r.json();
        if (d.ok) {
          const c = cuposData.find(x => x.id == id);
          if (c) c.disponible = nuevoValor;
          renderCupos(); renderCalendario();
        } else {
          Modal.error(d.msg || 'No se pudo actualizar el cupo.');
        }
      } catch(e) {
        Modal.error('Error de conexión al servidor.');
      }
    }

    async function bloquearDia(medicoId, fecha, nuevoDisponible) {
      const nd     = parseInt(nuevoDisponible);
      const accion = nd ? 'desbloquear' : 'bloquear';
      const [y,m,d2] = fecha.split('-');
      if (!await Modal.confirm(`¿Deseas ${accion} todos los cupos del ${d2}/${m}/${y}?`, `${accion.charAt(0).toUpperCase()+accion.slice(1)} día completo`, 'warning')) return;
      const body = `medico_id=${medicoId}&fecha=${fecha}&disponible=${nd}`;
      try {
        const r = await fetch('gateway.php?svc=schedules&action=bloquear', {
          method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body
        });
        const d = await r.json();
        if (d.ok) {
          cuposData.forEach(c => {
            if (String(c.medico_id) === String(medicoId) && c.fecha === fecha) c.disponible = nd;
          });
          renderCupos(); renderCalendario();
        } else {
          Modal.error(d.msg || 'No se pudo ' + accion + ' el día.');
        }
      } catch(e) {
        Modal.error('Error de conexión al servidor.');
      }
    }

    // ── Editar médico ────────────────────────────────────────────────────────
    const modalEditar = document.getElementById('modal-editar');
    function abrirEditar(id, nombre, especialidad, email) {
      document.getElementById('edit-id').value           = id;
      document.getElementById('edit-nombre').value       = nombre;
      document.getElementById('edit-especialidad').value = especialidad;
      document.getElementById('edit-email').value        = email;
      document.getElementById('edit-password').value     = '';
      modalEditar.style.display = 'flex';
    }
    function cerrarEditar() { modalEditar.style.display = 'none'; }
    modalEditar.addEventListener('click', e => { if (e.target === modalEditar) cerrarEditar(); });

    document.getElementById('form-editar-medico').addEventListener('submit', async function(e) {
      e.preventDefault();
      const body = new URLSearchParams(new FormData(this)).toString();
      const r = await fetch('gateway.php?svc=doctors&action=editar', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body
      });
      const d = await r.json();
      if (d.ok) { cerrarEditar(); cargarTodo(); }
      else Modal.error(d.msg || 'Error al editar el médico.');
    });

    async function eliminarMedico(id, nombre) {
      if (!await Modal.danger(`¿Eliminar al médico <strong>${nombre}</strong>? Esta acción no se puede deshacer.`, '¿Eliminar médico?')) return;
      const r = await fetch('gateway.php?svc=doctors&action=eliminar', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`id=${id}`
      });
      const d = await r.json();
      if (d.ok) cargarTodo(); else Modal.error(d.msg || 'No se pudo eliminar el médico.');
    }

    // ── Crear médico ─────────────────────────────────────────────────────────
    document.getElementById('form-crear-medico').addEventListener('submit', async function(e) {
      e.preventDefault();
      const msg = document.getElementById('msg-crear-medico');
      const body = new URLSearchParams(new FormData(this)).toString();
      const r = await fetch('gateway.php?svc=doctors&action=crear', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body
      });
      const d = await r.json();
      msg.className = d.ok
        ? 'text-sm px-4 py-3 rounded-xl font-medium bg-emerald-50 text-emerald-700 border border-emerald-200'
        : 'text-sm px-4 py-3 rounded-xl font-medium bg-red-50 text-red-600 border border-red-200';
      msg.textContent = d.msg || (d.ok ? '✓ Médico creado correctamente.' : 'Error al crear el médico.');
      if (d.ok) { this.reset(); cargarTodo(); }
    });

    // ── Crear cupos ──────────────────────────────────────────────────────────
    document.getElementById('form-crear-cupos').addEventListener('submit', async function(e) {
      e.preventDefault();
      const msg  = document.getElementById('msg-crear-cupos');
      const body = new URLSearchParams(new FormData(this)).toString();
      const r = await fetch('gateway.php?svc=schedules&action=crear', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body
      });
      const d = await r.json();
      msg.className = d.ok
        ? 'text-sm px-4 py-3 rounded-xl font-medium bg-emerald-50 text-emerald-700 border border-emerald-200'
        : 'text-sm px-4 py-3 rounded-xl font-medium bg-red-50 text-red-600 border border-red-200';
      msg.textContent = d.msg || (d.ok ? '✓ Cupos generados correctamente.' : 'Error al crear los cupos.');
      if (d.ok) cargarTodo();
    });

    // Fecha fin >= fecha inicio
    document.getElementById('fecha_inicio').addEventListener('change', function() {
      document.getElementById('fecha_fin').min = this.value;
      const fin = document.getElementById('fecha_fin');
      if (fin.value && fin.value < this.value) fin.value = this.value;
    });

    // ── Sincronizar selects de hora con inputs ocultos ────────────────────────
    function syncHora() {
      const hI = document.getElementById('hora_inicio_h').value;
      const mI = document.getElementById('hora_inicio_m').value;
      const hF = document.getElementById('hora_fin_h').value;
      const mF = document.getElementById('hora_fin_m').value;
      document.getElementById('hora_inicio_val').value = hI + ':' + mI;
      document.getElementById('hora_fin_val').value    = hF + ':' + mF;
    }

    // ── Presets intervalo ─────────────────────────────────────────────────────
    function setIntervalo(btn) {
      document.getElementById('intervalo_val').value = btn.dataset.mins;
      document.querySelectorAll('.preset-int-btn').forEach(b => {
        const active = b === btn;
        b.style.background  = active ? '#0d9488' : '#fff';
        b.style.color       = active ? '#fff'    : '#64748b';
        b.style.borderColor = active ? '#0d9488' : '#e2e8f0';
        b.style.boxShadow   = active ? '0 2px 8px rgba(13,148,136,.25)' : 'none';
      });
    }

    // ── Días de semana ────────────────────────────────────────────────────────
    function toggleDia(btn) {
      const val    = btn.dataset.value;
      const check  = document.getElementById('dia-check-' + val);
      const activo = btn.classList.toggle('dia-activo');
      check.checked         = activo;
      btn.style.background  = activo ? '#0d9488' : '#fff';
      btn.style.color       = activo ? '#fff'    : '#64748b';
      btn.style.borderColor = activo ? '#0d9488' : '#e2e8f0';
      btn.style.boxShadow   = activo ? '0 2px 8px rgba(13,148,136,.3)' : 'none';
    }

    // ── Calendario ───────────────────────────────────────────────────────────
    let calAnio = new Date().getFullYear();
    let calMes  = new Date().getMonth();
    const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

    function renderCalendario() {
      const medicoId = document.getElementById('horario-medico-select').value;
      const fechasConCupos = new Set(
        cuposData.filter(c => !medicoId || c.medico_id == medicoId).map(c => c.fecha)
      );
      document.getElementById('cal-titulo').textContent = meses[calMes] + ' ' + calAnio;
      const primerDia = new Date(calAnio, calMes, 1).getDay();
      const diasMes   = new Date(calAnio, calMes + 1, 0).getDate();
      const offset    = primerDia === 0 ? 6 : primerDia - 1;
      let html = '';
      for (let i = 0; i < offset; i++) html += '<div></div>';
      for (let d = 1; d <= diasMes; d++) {
        const fecha = calAnio + '-' + String(calMes+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
        const tiene = fechasConCupos.has(fecha);
        html += `<div onclick="${tiene ? `abrirModalDia('${fecha}')` : ''}"
          style="text-align:center;padding:6px 2px;border-radius:6px;font-size:13px;font-weight:600;
            background:${tiene ? '#0d9488' : '#f1f5f9'};color:${tiene ? '#fff' : '#94a3b8'};
            border:1px solid ${tiene ? '#0d9488' : '#e2e8f0'};cursor:${tiene ? 'pointer' : 'default'};"
          ${tiene ? 'onmouseover="this.style.opacity=\'0.8\'" onmouseout="this.style.opacity=\'1\'"' : ''}>${d}</div>`;
      }
      document.getElementById('cal-grid').innerHTML = html;
    }

    // renderCalendario se llama desde selectHorMedico al elegir médico
    function cambiarMes(dir) {
      calMes += dir;
      if (calMes > 11) { calMes = 0; calAnio++; }
      if (calMes < 0)  { calMes = 11; calAnio--; }
      renderCalendario();
    }

    // ── Modal editar día ──────────────────────────────────────────────────────
    const modalDia = document.getElementById('modal-dia');
    function abrirModalDia(fecha) {
      const medicoId = document.getElementById('horario-medico-select').value;
      if (!medicoId) { document.getElementById('modal-aviso').style.display = 'flex'; return; }
      document.getElementById('modal-medico-id').value = medicoId;
      document.getElementById('modal-fecha').value     = fecha;
      document.getElementById('modal-dia-titulo').textContent = 'Cupos del ' + fecha;
      renderCuposDia(medicoId, fecha);
      modalDia.style.display = 'flex';
    }
    function cerrarModalDia() { modalDia.style.display = 'none'; }
    modalDia.addEventListener('click', e => { if (e.target === modalDia) cerrarModalDia(); });

    function renderCuposDia(medicoId, fecha) {
      const cuposDia = cuposData.filter(c => String(c.medico_id) === String(medicoId) && c.fecha === fecha)
                                .sort((a,b) => a.hora.localeCompare(b.hora));
      const cont = document.getElementById('modal-dia-cupos');
      if (!cuposDia.length) { cont.innerHTML = '<p style="color:#94a3b8;">No hay cupos para este día.</p>'; return; }
      cont.innerHTML = cuposDia.map(c => `
        <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #f1f5f9;">
          <span style="font-weight:600;">${convertTo12HourJS(c.hora.substring(0,5))}</span>
          <span style="padding:2px 10px;border-radius:20px;font-size:12px;
            background:${c.disponible ? '#d1fae5' : '#fee2e2'};color:${c.disponible ? '#065f46' : '#991b1b'}">
            ${c.disponible ? 'Disponible' : 'Bloqueado'}
          </span>
          <button class="btn rojo" style="font-size:12px;padding:4px 10px;" onclick="eliminarCupo(${c.id},this)">🗑️</button>
        </div>`).join('');
    }

    async function eliminarCupo(id, btn) {
      if (!await Modal.danger('¿Eliminar este cupo? No se puede deshacer.', 'Eliminar cupo')) return;
      const r = await fetch('gateway.php?svc=schedules&action=eliminar', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+id
      });
      const d = await r.json();
      if (d.ok) {
        cuposData = cuposData.filter(c => c.id != id);
        btn.closest('div').remove();
        renderCalendario();
      }
    }

    async function eliminarTodoDia() {
      const medicoId = document.getElementById('modal-medico-id').value;
      const fecha    = document.getElementById('modal-fecha').value;
      if (!await Modal.danger(`¿Eliminar TODOS los cupos del ${fecha}? Esta acción no se puede deshacer.`, 'Eliminar día completo')) return;
      const r = await fetch('gateway.php?svc=schedules&action=eliminar', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`medico_id=${medicoId}&fecha=${fecha}`
      });
      const d = await r.json();
      if (d.ok) {
        cuposData = cuposData.filter(c => !(String(c.medico_id)===String(medicoId) && c.fecha===fecha));
        cerrarModalDia(); renderCalendario(); renderCupos();
      }
    }

    async function agregarCuposDia() {
      const medicoId  = document.getElementById('modal-medico-id').value;
      const fecha     = document.getElementById('modal-fecha').value;
      const horaIni   = document.getElementById('modal-hora-inicio').value;
      const horaFin   = document.getElementById('modal-hora-fin').value;
      const intervalo = document.getElementById('modal-intervalo').value;
      if (!horaIni || !horaFin) { await Modal.warning('Debes completar la hora de inicio y fin.', 'Horas requeridas'); return; }
      await fetch('gateway.php?svc=schedules&action=crear', {
        method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:`medico_id=${medicoId}&fecha=${fecha}&hora_inicio=${horaIni}&hora_fin=${horaFin}&intervalo=${intervalo}`
      });
      await cargarTodo();
      renderCuposDia(medicoId, fecha);
    }

    // ── Inicio ────────────────────────────────────────────────────────────────
    cargarTodo();
  </script>
</body>
</html>
