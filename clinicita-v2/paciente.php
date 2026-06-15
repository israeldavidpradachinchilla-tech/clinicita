<?php
require_once __DIR__ . '/services/shared/jwt.php';

$token = $_COOKIE['clinicita_token'] ?? '';
$u     = $token ? JWT::decode($token) : null;
if (!$u || $u['rol'] !== 'paciente') {
    header('Location: /login'); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Paciente · ClíniCita</title>
  <link rel="stylesheet" href="css/estilos.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {} },
      corePlugins: { preflight: false }
    }
  </script>
  <style>
    html, body { height: 100%; }
    body { display: flex; background: #f1f5f9; }
    #sidebar { width: 260px; min-width: 260px; }
    .seccion { display: none; }
    .seccion.activa { display: block; }
    .nav-link.activo { background: rgba(255,255,255,0.15) !important; border-left-color: #fff !important; color: #fff !important; font-weight: 700; }
    .nav-link.activo .nav-icon-wrap { background: rgba(255,255,255,0.25) !important; }
    .tw-field { display:block;width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1f2937;background:#fff;outline:none;transition:border-color .15s,box-shadow .15s;box-sizing:border-box; }
    .tw-field:focus { border-color:#0d9488;box-shadow:0 0 0 3px rgba(13,148,136,.1); }
    .tw-label { display:block;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:#64748b;margin-bottom:6px; }
    .card-table table { margin-top:0;border-radius:0;box-shadow:none; }
    .card-table th { background:transparent;color:#94a3b8;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.06em;padding:11px 20px;border-bottom:1.5px solid #f1f5f9; }
    /* Slots rediseñados */
    .slot {
      padding: 10px 6px; border-radius: 10px; font-size: 13px; font-weight: 700;
      border: 1.5px solid #e2e8f0; background: #fff; color: #1f2937;
      cursor: pointer; text-align: center; transition: all .15s;
    }
    .slot-disponible { background:#fff; border-color:#e2e8f0; color:#1f2937; }
    .slot-disponible:hover { background:#f0fdfa; border-color:#0d9488; color:#0d9488; transform:translateY(-1px); box-shadow:0 4px 12px rgba(13,148,136,.15); }
    .slot-selected { background:linear-gradient(135deg,#0d9488,#0891b2) !important; border-color:#0d9488 !important; color:#fff !important; box-shadow:0 4px 14px rgba(13,148,136,.35); }
    .slot-ocupado { background:#fef2f2; border-color:#fecaca; color:#ef4444; cursor:not-allowed; opacity:.7; }
    .slot-pendiente { background:#f8fafc; border-color:#e2e8f0; color:#94a3b8; cursor:not-allowed; border-style:dashed; }
  </style>
</head>
<body class="font-sans text-gray-800">

  <!-- ── SIDEBAR ──────────────────────────────────────────────────── -->
  <aside id="sidebar" class="flex flex-col h-screen sticky top-0 overflow-y-auto"
    style="background:linear-gradient(175deg,#0d9488 0%,#0a7a6e 50%,#0891b2 100%);box-shadow:4px 0 20px rgba(0,0,0,0.12);">

    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
      <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/20 shadow-inner">
        <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6.5 3.5 5 5.5 5c1.54 0 2.99.99 3.57 2.36h1.87C12.51 5.99 13.96 5 15.5 5 17.5 5 19 6.5 19 8.5c0 3.78-3.4 6.86-8.65 11.54L12 21.35z"/></svg>
      </div>
      <div>
        <p class="text-white font-bold text-lg leading-tight">ClíniCita</p>
        <p class="text-white/60 text-xs font-medium uppercase tracking-widest">Paciente</p>
      </div>
    </div>

    <nav class="flex-1 px-3 py-4 flex flex-col gap-1">
      <p class="text-white/40 text-xs font-bold uppercase tracking-widest px-3 mb-2">Menú</p>

      <a href="#" class="nav-link activo group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all border-l-4 border-transparent text-sm font-medium no-underline" data-sec="agendar">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="12" y1="15" x2="12" y2="15.01"/></svg>
        </span>
        Agendar cita
      </a>

      <a href="#" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all border-l-4 border-transparent text-sm font-medium no-underline" data-sec="mis-citas">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/></svg>
        </span>
        Mis citas
      </a>

      <a href="#" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all border-l-4 border-transparent text-sm font-medium no-underline" data-sec="perfil">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </span>
        Mi perfil
      </a>
    </nav>

    <div class="px-4 py-4 border-t border-white/10">
      <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-white/10 mb-3">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-white/20 shrink-0 text-white font-bold text-sm">
          <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
        </div>
        <div class="overflow-hidden">
          <p class="text-white text-sm font-semibold truncate"><?= htmlspecialchars($u['nombre']) ?></p>
          <p class="text-white/50 text-xs">Paciente</p>
        </div>
      </div>
      <a href="#" id="btn-logout"
        class="flex items-center justify-center gap-2 w-full py-2 px-4 rounded-xl bg-white/10 hover:bg-red-500/80 text-white/80 hover:text-white text-sm font-medium transition-all no-underline">
        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5-5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
        Cerrar sesión
      </a>
    </div>
  </aside>

  <!-- ── ÁREA PRINCIPAL ──────────────────────────────────────────── -->
  <div class="flex flex-col flex-1 min-h-screen overflow-hidden">

    <!-- Topbar -->
    <header class="flex items-center justify-between bg-white px-8 h-16 shrink-0"
      style="box-shadow:0 1px 3px rgba(0,0,0,0.08);border-bottom:1px solid #f1f5f9;">
      <div class="flex items-center gap-3">
        <div class="w-1.5 h-7 rounded-full" style="background:linear-gradient(180deg,#0d9488,#0891b2);"></div>
        <span id="topbar-titulo" class="font-semibold text-gray-700 text-base">Agendar cita</span>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-400 hidden sm:block"><?= date('d \d\e F, Y') ?></span>
        <div class="w-px h-5 bg-gray-200"></div>
        <div class="flex items-center gap-2 text-sm text-gray-600 font-medium">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
            style="background:linear-gradient(135deg,#0d9488,#0891b2);">
            <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
          </div>
          <?= htmlspecialchars(explode(' ', $u['nombre'])[0]) ?>
        </div>
      </div>
    </header>

    <main class="flex-1 overflow-y-auto p-8" style="background:#f1f5f9;">

      <!-- ── AGENDAR CITA ───────────────────────────────────────── -->
      <section class="seccion activa" id="sec-agendar">

        <div class="mb-7">
          <h1 class="text-2xl font-bold text-gray-800">Agendar nueva cita</h1>
          <p class="text-sm text-gray-400 mt-1">Selecciona especialidad, médico, fecha y horario.</p>
        </div>

        <div class="flex gap-6 items-start flex-wrap">

          <!-- Formulario -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex-1" style="min-width:320px;max-width:500px;">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
              <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0" style="background:linear-gradient(135deg,#0d9488,#0891b2);">
                <svg class="w-5 h-5 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              </div>
              <div>
                <p class="font-bold text-gray-800 text-sm">Nueva cita</p>
                <p class="text-xs text-gray-400">Completa los pasos para reservar</p>
              </div>
            </div>

            <form id="formCita" class="px-6 py-5 flex flex-col gap-4">

              <!-- Paso 1: Especialidad -->
              <div>
                <label class="tw-label">1 · Especialidad</label>
                <select id="filtro-especialidad" class="tw-field">
                  <option value="">Todas las especialidades</option>
                </select>
              </div>

              <!-- Paso 2: Médico -->
              <div>
                <label class="tw-label">2 · Médico <span style="color:#f87171;">*</span></label>
                <select id="filtro-medico" name="medico_id" required class="tw-field"></select>
              </div>

              <!-- Paso 3: Fecha -->
              <div>
                <label class="tw-label">3 · Fecha <span style="color:#f87171;">*</span></label>
                <select id="filtro-fecha" name="fecha" required class="tw-field"></select>
              </div>

              <!-- Paso 4: Horario -->
              <div>
                <label class="tw-label">4 · Selecciona un horario</label>
                <div id="horarios-grid" class="horarios-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px;margin-top:4px;"></div>
                <input type="hidden" id="horario_id" name="horario_id">
              </div>

              <!-- Motivo -->
              <div>
                <label class="tw-label">Motivo de la consulta</label>
                <textarea name="motivo" rows="3" placeholder="Describe brevemente el motivo de tu cita…"
                  class="tw-field" style="resize:vertical;"></textarea>
              </div>

              <!-- Mensaje feedback -->
              <div id="msg-agendar" class="hidden text-sm px-4 py-3 rounded-xl font-medium"></div>

              <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white font-semibold text-sm hover:opacity-90 transition-all"
                style="background:linear-gradient(135deg,#0d9488,#0891b2);border:none;cursor:pointer;">
                <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Confirmar cita
              </button>
            </form>
          </div>

          <!-- Panel info -->
          <div class="flex flex-col gap-4" style="min-width:220px;max-width:280px;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Leyenda de horarios</p>
              <div class="flex flex-col gap-2">
                <div class="flex items-center gap-3">
                  <span style="display:inline-block;width:36px;height:32px;border-radius:8px;background:#fff;border:1.5px solid #e2e8f0;"></span>
                  <span style="font-size:12px;color:#64748b;">Disponible</span>
                </div>
                <div class="flex items-center gap-3">
                  <span style="display:inline-block;width:36px;height:32px;border-radius:8px;background:linear-gradient(135deg,#0d9488,#0891b2);"></span>
                  <span style="font-size:12px;color:#64748b;">Seleccionado</span>
                </div>
                <div class="flex items-center gap-3">
                  <span style="display:inline-block;width:36px;height:32px;border-radius:8px;background:#fef2f2;border:1.5px solid #fecaca;"></span>
                  <span style="font-size:12px;color:#64748b;">Ocupado</span>
                </div>
                <div class="flex items-center gap-3">
                  <span style="display:inline-block;width:36px;height:32px;border-radius:8px;background:#f8fafc;border:1.5px dashed #e2e8f0;"></span>
                  <span style="font-size:12px;color:#64748b;">Pendiente</span>
                </div>
              </div>
            </div>
            <div class="rounded-2xl p-5 border border-teal-100" style="background:#f0fdfa;">
              <div class="flex items-center gap-2 mb-2">
                <svg class="w-4 h-4 fill-none stroke-current text-teal-600" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <p class="text-xs font-bold text-teal-700 uppercase tracking-wide">Info</p>
              </div>
              <p class="text-xs text-teal-600 leading-relaxed">Los horarios se actualizan en tiempo real. Si un horario desaparece, otro paciente lo acaba de reservar.</p>
            </div>
          </div>

        </div>
      </section>

      <!-- ── MIS CITAS ─────────────────────────────────────────── -->
      <section class="seccion" id="sec-mis-citas">

        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Mis citas</h1>
          <p class="text-sm text-gray-400 mt-1">Historial de todas tus citas agendadas.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100" style="overflow:visible;">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
              <p class="font-bold text-gray-800 text-sm">Historial de citas</p>
              <p class="text-xs text-gray-400 mt-0.5">Se actualiza automáticamente</p>
            </div>
            <div class="flex items-center gap-2">
              <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e;animation:pulse-dot 2s infinite;"></span>
              <span style="font-size:12px;color:#64748b;font-weight:500;">En vivo</span>
            </div>
          </div>
          <div class="card-table" style="overflow-x:auto;border-radius:0 0 16px 16px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;margin:0;">
              <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #f1f5f9;">
                  <th style="padding:11px 20px;text-align:left;background:transparent;">Fecha / Hora</th>
                  <th style="padding:11px 20px;text-align:left;background:transparent;">Médico</th>
                  <th style="padding:11px 20px;text-align:left;background:transparent;">Motivo</th>
                  <th style="padding:11px 20px;text-align:left;background:transparent;">Estado</th>
                  <th style="padding:11px 20px;text-align:left;background:transparent;"></th>
                </tr>
              </thead>
              <tbody id="tbody-mis-citas"></tbody>
            </table>
          </div>
        </div>

        <style>@keyframes pulse-dot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(.75)}}</style>
      </section>

      <!-- ── MI PERFIL ─────────────────────────────────────────── -->
      <section class="seccion" id="sec-perfil">

        <div class="mb-7">
          <h1 class="text-2xl font-bold text-gray-800">Mi perfil</h1>
          <p class="text-sm text-gray-400 mt-1">Gestiona tu información personal.</p>
        </div>

        <div class="flex gap-6 items-start flex-wrap">

          <!-- Formulario perfil -->
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex-1" style="min-width:300px;max-width:480px;">
            <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-100">
              <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
                <svg class="w-5 h-5 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <div>
                <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($u['nombre']) ?></p>
                <p class="text-xs text-gray-400">Actualiza tus datos personales</p>
              </div>
            </div>

            <form id="formPerfil" class="px-6 py-5 flex flex-col gap-4">
              <div>
                <label class="tw-label">Correo electrónico</label>
                <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required class="tw-field">
              </div>
              <div>
                <label class="tw-label">Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($u['telefono'] ?? '') ?>" class="tw-field" placeholder="Ej: 3001234567">
              </div>

              <?php if (($u['tipo_documento'] ?? '') === 'tarjeta'): ?>
              <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:12px 14px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                  <svg style="width:14px;height:14px;stroke:#d97706;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                  <span style="font-size:12px;font-weight:700;color:#d97706;">Tarjeta de identidad</span>
                </div>
                <p style="font-size:12px;color:#92400e;margin:0;">Fuiste registrado con tarjeta de identidad. Cuando obtengas tu cédula puedes actualizar tu documento.</p>
              </div>
              <div>
                <label class="tw-label">Tipo de documento</label>
                <select name="tipo_documento" class="tw-field">
                  <option value="tarjeta" selected>Tarjeta de identidad</option>
                  <option value="cedula">Cédula de ciudadanía</option>
                </select>
              </div>
              <div>
                <label class="tw-label">Número de documento</label>
                <input type="text" name="cedula" value="<?= htmlspecialchars($u['cedula']) ?>" required class="tw-field">
              </div>
              <?php endif; ?>

              <!-- Separador cambio de contraseña -->
              <div style="border-top:1.5px solid #f1f5f9;margin:4px 0;padding-top:16px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:14px;">
                  <div style="width:28px;height:28px;border-radius:8px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg style="width:13px;height:13px;stroke:#0d9488;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                  </div>
                  <p style="font-size:12px;font-weight:700;color:#374151;margin:0;">Cambiar contraseña</p>
                  <span style="font-size:11px;color:#94a3b8;">(opcional)</span>
                </div>
                <div class="flex flex-col gap-3">
                  <div>
                    <label class="tw-label">Contraseña actual</label>
                    <input type="password" name="password_actual" id="password_actual" placeholder="••••••••" class="tw-field" minlength="6" autocomplete="current-password">
                  </div>
                  <div>
                    <label class="tw-label">Nueva contraseña</label>
                    <input type="password" name="password_nueva" id="password_nueva" placeholder="Mínimo 6 caracteres" class="tw-field" minlength="6" autocomplete="new-password">
                  </div>
                  <div>
                    <label class="tw-label">Confirmar nueva contraseña</label>
                    <input type="password" id="password_confirmar" placeholder="Repite la nueva contraseña" class="tw-field" minlength="6" autocomplete="new-password">
                  </div>
                </div>
              </div>

              <div id="msg-perfil" class="hidden text-sm px-4 py-3 rounded-xl font-medium"></div>

              <button type="submit"
                class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-white font-semibold text-sm hover:opacity-90 transition-all"
                style="background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;cursor:pointer;">
                <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v14a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar cambios
              </button>
            </form>
          </div>

          <!-- Zona de peligro -->
          <div class="flex flex-col gap-4" style="min-width:220px;max-width:280px;">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
              <p class="text-xs font-bold text-gray-400 uppercase tracking-wide mb-3">Tu cuenta</p>
              <div class="flex items-center gap-3 p-3 rounded-xl" style="background:#f8fafc;">
                <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0" style="background:linear-gradient(135deg,#0d9488,#0891b2);">
                  <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
                </div>
                <div>
                  <p style="font-weight:600;color:#1f2937;font-size:13px;margin:0;"><?= htmlspecialchars($u['nombre']) ?></p>
                  <p style="font-size:11px;color:#94a3b8;margin:0;"><?= htmlspecialchars($u['email']) ?></p>
                </div>
              </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-5">
              <p class="text-xs font-bold text-red-400 uppercase tracking-wide mb-2">Zona de peligro</p>
              <p style="font-size:12px;color:#64748b;margin-bottom:14px;line-height:1.5;">Eliminar tu cuenta borrará todos tus datos y citas de forma permanente.</p>
              <button id="btn-eliminar-perfil"
                class="w-full flex items-center justify-center gap-2 py-2.5 rounded-xl font-semibold text-sm transition-all"
                style="background:#fef2f2;color:#dc2626;border:1.5px solid #fecaca;cursor:pointer;"
                onmouseover="this.style.background='#dc2626';this.style.color='#fff';"
                onmouseout="this.style.background='#fef2f2';this.style.color='#dc2626';">
                <svg class="w-4 h-4 stroke-current fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                Eliminar mi cuenta
              </button>
            </div>
          </div>

        </div>
      </section>

    </main>
  </div>

<!-- ── MODAL SYSTEM ── -->
<div id="modal-sys-overlay" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:9999;align-items:center;justify-content:center;padding:16px;">
  <div id="modal-sys-box" style="background:#fff;border-radius:20px;padding:28px 28px 24px;width:100%;max-width:380px;box-shadow:0 20px 60px rgba(0,0,0,.18);text-align:center;">
    <div id="modal-sys-icon" style="width:52px;height:52px;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;"></div>
    <h3 id="modal-sys-title" style="font-size:16px;font-weight:700;color:#1f2937;margin:0 0 8px;"></h3>
    <p id="modal-sys-msg"   style="font-size:14px;color:#64748b;margin:0 0 22px;line-height:1.55;"></p>
    <div id="modal-sys-btns" style="display:flex;gap:10px;"></div>
  </div>
</div>
<style>#modal-sys-overlay{display:none;}#modal-sys-overlay.open{display:flex;}</style>

<script>
  const Modal = (() => {
    const overlay=document.getElementById('modal-sys-overlay'),icon=document.getElementById('modal-sys-icon'),title=document.getElementById('modal-sys-title'),msg=document.getElementById('modal-sys-msg'),btns=document.getElementById('modal-sys-btns');
    const cfg={success:{bg:'#f0fdf4',color:'#16a34a',svg:'<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>'},error:{bg:'#fef2f2',color:'#dc2626',svg:'<circle cx="12" cy="12" r="9"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>'},warning:{bg:'#fffbeb',color:'#d97706',svg:'<path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>'},danger:{bg:'#fef2f2',color:'#dc2626',svg:'<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>'},info:{bg:'#f0fdfa',color:'#0d9488',svg:'<circle cx="12" cy="12" r="9"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'}};
    const bs=(p,c)=>p?`flex:1;padding:11px;border-radius:12px;border:none;cursor:pointer;font-size:14px;font-weight:600;color:#fff;background:${c};`:`flex:1;padding:11px;border-radius:12px;border:1.5px solid #e2e8f0;cursor:pointer;font-size:14px;font-weight:600;color:#64748b;background:#fff;`;
    function show({t='',m='',type='info',isConfirm=false,okTxt='Entendido',confirmTxt='Confirmar',cancelTxt='Cancelar'}){
      const c=cfg[type]||cfg.info;
      icon.style.background=c.bg;icon.innerHTML=`<svg style="width:24px;height:24px;stroke:${c.color};fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">${c.svg}</svg>`;
      title.textContent=t||{success:'Éxito',error:'Error',warning:'Advertencia',danger:'¡Cuidado!',info:'Información'}[type];
      msg.innerHTML=m;
      return new Promise(resolve=>{
        btns.innerHTML='';
        if(isConfirm){
          const bc=document.createElement('button');bc.textContent=cancelTxt;bc.style.cssText=bs(false,c.color);bc.onclick=()=>{close();resolve(false);};
          const bo=document.createElement('button');bo.textContent=confirmTxt;bo.style.cssText=bs(true,c.color);bo.onclick=()=>{close();resolve(true);};
          btns.appendChild(bc);btns.appendChild(bo);
        }else{
          const bo=document.createElement('button');bo.textContent=okTxt;bo.style.cssText=`width:100%;padding:11px;border-radius:12px;border:none;cursor:pointer;font-size:14px;font-weight:600;color:#fff;background:${c.color};`;bo.onclick=()=>{close();resolve(true);};
          btns.appendChild(bo);
        }
        overlay.classList.add('open');
      });
    }
    function close(){overlay.classList.remove('open');}
    overlay.addEventListener('click',e=>{if(e.target===overlay)close();});
    return{alert:(m,t='',type='info')=>show({t,m,type}),success:(m,t='')=>show({t,m,type:'success'}),error:(m,t='Error')=>show({t,m,type:'error'}),warning:(m,t='Advertencia')=>show({t,m,type:'warning'}),confirm:(m,t='',type='warning')=>show({t,m,type,isConfirm:true}),danger:(m,t='¡Cuidado!')=>show({t,m,type:'danger',isConfirm:true})};
  })();
</script>

<script>
  // ── Sidebar ──────────────────────────────────────────────────────
  const titulos = { agendar:'Agendar cita', 'mis-citas':'Mis citas', perfil:'Mi perfil' };

  function activarSeccion(sec) {
    const secs = Object.keys(titulos);
    const s = secs.includes(sec) ? sec : 'agendar';
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('activo'));
    document.querySelectorAll('.seccion').forEach(el => el.classList.remove('activa'));
    const link = document.querySelector(`.nav-link[data-sec="${s}"]`);
    if (link) link.classList.add('activo');
    document.getElementById('sec-' + s).classList.add('activa');
    document.getElementById('topbar-titulo').textContent = titulos[s] || '';
    location.hash = s;
  }

  document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', e => { e.preventDefault(); activarSeccion(link.dataset.sec); });
  });

  const hashInicial = location.hash.replace('#','');
  if (hashInicial) activarSeccion(hashInicial);

  // ── Logout ───────────────────────────────────────────────────────
  document.getElementById('btn-logout').addEventListener('click', async e => {
    e.preventDefault();
    await fetch('gateway.php?svc=auth&action=logout', { method:'POST' });
    location.href = '/login';
  });

  // ── Mis citas ────────────────────────────────────────────────────
  const estadoCitaCfg = {
    confirmada:   { bg:'#f0fdf4', color:'#16a34a', label:'Confirmada'  },
    realizada:    { bg:'#eff6ff', color:'#2563eb', label:'Realizada'   },
    no_realizada: { bg:'#fef2f2', color:'#dc2626', label:'No realizada'},
    cancelada:    { bg:'#f8fafc', color:'#64748b', label:'Cancelada'   },
  };

  function horaFmt(h) {
    const [hh, mm] = h.split(':');
    let h12 = parseInt(hh);
    const ap = h12 >= 12 ? 'p.m' : 'a.m';
    if (h12 > 12) h12 -= 12; if (h12 === 0) h12 = 12;
    return `${String(h12).padStart(2,'0')}:${mm} ${ap}`;
  }

  function cargarMisCitas() {
    fetch('gateway.php?svc=appointments&action=listar')
      .then(r => r.json())
      .then(citas => {
        const tbody = document.getElementById('tbody-mis-citas');
        if (!Array.isArray(citas) || !citas.length) {
          tbody.innerHTML = '<tr><td colspan="5" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;">No tienes citas agendadas.</td></tr>';
          return;
        }
        const hoy = new Date().toISOString().split('T')[0];
        tbody.innerHTML = citas.map((c, i) => {
          const cfg = estadoCitaCfg[c.estado] || { bg:'#f1f5f9', color:'#64748b', label: c.estado };
          const [y,m,d] = c.fecha.split('-');
          const esHoy = c.fecha === hoy;
          return `<tr style="border-bottom:1px solid #f1f5f9;${i%2!==0?'background:#fafafa;':''}">
            <td style="padding:13px 20px;">
              <div style="display:flex;align-items:center;gap:8px;">
                ${esHoy ? '<span style="background:#0d9488;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">Hoy</span>' : ''}
                <div>
                  <p style="font-weight:600;color:#1f2937;font-size:13px;margin:0;">${d}/${m}/${y}</p>
                  <p style="color:#94a3b8;font-size:12px;margin:0;">${horaFmt(c.hora.substring(0,5))}</p>
                </div>
              </div>
            </td>
            <td style="padding:13px 20px;">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:30px;height:30px;border-radius:50%;background:linear-gradient(135deg,#0d9488,#0891b2);display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;font-weight:700;flex-shrink:0;">
                  ${c.medico.charAt(0).toUpperCase()}
                </div>
                <span style="font-weight:600;color:#374151;font-size:13px;">${c.medico}</span>
              </div>
            </td>
            <td style="padding:13px 20px;color:#64748b;font-size:13px;">${c.motivo || '—'}</td>
            <td style="padding:13px 20px;">
              <span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;background:${cfg.bg};color:${cfg.color};">${cfg.label}</span>
            </td>
            <td style="padding:13px 20px;">
              ${c.estado === 'confirmada' ? `
              <button onclick="cancelarCita(${c.id})"
                style="padding:5px 12px;border-radius:8px;border:1.5px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:12px;font-weight:700;cursor:pointer;"
                onmouseover="this.style.background='#dc2626';this.style.color='#fff';"
                onmouseout="this.style.background='#fef2f2';this.style.color='#dc2626';">
                Cancelar
              </button>` : ''}
            </td>
          </tr>`;
        }).join('');
      }).catch(() => {});
  }

  async function cancelarCita(id) {
    if (!await Modal.confirm('¿Seguro que deseas cancelar esta cita?', 'Cancelar cita', 'warning')) return;
    const r = await fetch(`gateway.php?svc=appointments&action=cancelar&id=${id}`);
    const d = await r.json();
    if (d.ok) { Modal.success('Tu cita ha sido cancelada.', 'Cita cancelada'); cargarMisCitas(); }
    else Modal.error(d.msg || 'No se pudo cancelar la cita.');
  }

  cargarMisCitas();
  setInterval(cargarMisCitas, 5000);

  // ── Perfil ───────────────────────────────────────────────────────
  document.getElementById('formPerfil').addEventListener('submit', async function(e) {
    e.preventDefault();
    const msg       = document.getElementById('msg-perfil');
    const actual    = document.getElementById('password_actual').value;
    const nueva     = document.getElementById('password_nueva').value;
    const confirmar = document.getElementById('password_confirmar').value;

    // Validación de contraseña si el usuario quiso cambiarla
    if (actual || nueva || confirmar) {
      if (!actual) {
        mostrarMsg(msg, false, 'Debes ingresar tu contraseña actual para cambiarla.'); return;
      }
      if (nueva.length < 6) {
        mostrarMsg(msg, false, 'La nueva contraseña debe tener al menos 6 caracteres.'); return;
      }
      if (nueva !== confirmar) {
        mostrarMsg(msg, false, 'Las contraseñas no coinciden.'); return;
      }
    }

    const body = new URLSearchParams(new FormData(this)).toString();
    const res  = await fetch('gateway.php?svc=auth&action=actualizar_perfil', {
      method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body
    });
    const data = await res.json();
    mostrarMsg(msg, data.ok, data.msg || (data.ok ? '✓ Perfil actualizado correctamente.' : 'Error al actualizar.'));
    if (data.ok) {
      // Limpiar campos de contraseña
      document.getElementById('password_actual').value    = '';
      document.getElementById('password_nueva').value     = '';
      document.getElementById('password_confirmar').value = '';
    }
  });

  function mostrarMsg(el, ok, txt) {
    el.className = ok
      ? 'text-sm px-4 py-3 rounded-xl font-medium bg-emerald-50 text-emerald-700 border border-emerald-200'
      : 'text-sm px-4 py-3 rounded-xl font-medium bg-red-50 text-red-600 border border-red-200';
    el.textContent = txt;
  }

  document.getElementById('btn-eliminar-perfil').addEventListener('click', async function() {
    if (!await Modal.danger('Esto eliminará tu cuenta y <strong>todas tus citas</strong> de forma permanente. Esta acción no se puede deshacer.', '¿Eliminar cuenta?')) return;
    const res  = await fetch('gateway.php?svc=auth&action=eliminar_perfil', { method:'POST' });
    const data = await res.json();
    if (data.ok) location.href = data.redirect;
    else Modal.error(data.msg || 'No se pudo eliminar la cuenta.');
  });

  // ── Horarios ─────────────────────────────────────────────────────
  async function cargarHorariosData() {
    const [medicos, horarios] = await Promise.all([
      fetch('gateway.php?svc=doctors&action=listar').then(r => r.json()),
      fetch('gateway.php?svc=schedules&action=listar').then(r => r.json()),
    ]);
    window.horariosData = { medicos, horarios };
    pacienteApp.init();
    cargarEspecialidades();
  }

  function cargarEspecialidades() {
    const select = document.getElementById('filtro-especialidad');
    const especialidades = [...new Set(window.horariosData.medicos.map(m => m.especialidad))].sort();
    select.innerHTML = '<option value="">Todas las especialidades</option>' +
      especialidades.map(e => `<option value="${e}">${e}</option>`).join('');
  }

  cargarHorariosData();
</script>
<script src="js/paciente.js"></script>
<script src="js/validaciones.js"></script>
</body>
</html>
