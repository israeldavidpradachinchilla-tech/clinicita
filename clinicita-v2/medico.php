<?php
require_once __DIR__ . '/services/shared/jwt.php';

$token = $_COOKIE['clinicita_token'] ?? '';
$u     = $token ? JWT::decode($token) : null;
if (!$u || $u['rol'] !== 'medico') {
    header('Location: /login'); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel Médico · ClíniCita</title>
  <link rel="stylesheet" href="css/estilos.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: { colors: { brand: { DEFAULT:'#0d9488', dark:'#0f766e' } } } },
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
    @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.75)} }
  </style>
</head>
<body class="font-sans text-gray-800">

  <!-- ── SIDEBAR ──────────────────────────────────────────────────── -->
  <aside id="sidebar" class="flex flex-col h-screen sticky top-0 overflow-y-auto"
    style="background:linear-gradient(175deg,#0d9488 0%,#0a7a6e 50%,#0891b2 100%);box-shadow:4px 0 20px rgba(0,0,0,0.12);">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10">
      <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-white/20 shadow-inner">
        <svg class="w-6 h-6 fill-white" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6.5 3.5 5 5.5 5c1.54 0 2.99.99 3.57 2.36h1.87C12.51 5.99 13.96 5 15.5 5 17.5 5 19 6.5 19 8.5c0 3.78-3.4 6.86-8.65 11.54L12 21.35z"/></svg>
      </div>
      <div>
        <p class="text-white font-bold text-lg leading-tight">ClíniCita</p>
        <p class="text-white/60 text-xs font-medium uppercase tracking-widest">Médico</p>
      </div>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 flex flex-col gap-1">
      <p class="text-white/40 text-xs font-bold uppercase tracking-widest px-3 mb-2">Panel</p>

      <a href="#" class="nav-link activo group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 border-l-4 border-transparent text-sm font-medium no-underline" data-sec="inicio">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 transition-all shrink-0">
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><path d="M9 21V12h6v9"/>
          </svg>
        </span>
        Inicio
      </a>

      <p class="text-white/40 text-xs font-bold uppercase tracking-widest px-3 mt-5 mb-2">Atención</p>

      <a href="#" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 border-l-4 border-transparent text-sm font-medium no-underline" data-sec="consultas">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
          </svg>
        </span>
        Consultas
      </a>

      <a href="#" class="nav-link group flex items-center gap-3 px-3 py-2.5 rounded-xl text-white/80 hover:text-white hover:bg-white/10 transition-all duration-150 border-l-4 border-transparent text-sm font-medium no-underline" data-sec="cupos">
        <span class="nav-icon-wrap flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 shrink-0">
          <svg class="w-4 h-4 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 12"/>
          </svg>
        </span>
        Mis Cupos
      </a>
    </nav>

    <!-- Usuario + logout -->
    <div class="px-4 py-4 border-t border-white/10">
      <div class="flex items-center gap-3 px-2 py-2 rounded-xl bg-white/10 mb-3">
        <div class="flex items-center justify-center w-9 h-9 rounded-full bg-white/20 shrink-0 text-white font-bold text-sm">
          <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
        </div>
        <div class="overflow-hidden">
          <p class="text-white text-sm font-semibold truncate"><?= htmlspecialchars($u['nombre']) ?></p>
          <p class="text-white/50 text-xs">Médico</p>
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
    <header class="flex items-center justify-between bg-white px-8 h-16 shrink-0"
      style="box-shadow:0 1px 3px rgba(0,0,0,0.08);border-bottom:1px solid #f1f5f9;">
      <div class="flex items-center gap-3">
        <div class="w-1.5 h-7 rounded-full" style="background:linear-gradient(180deg,#0d9488,#0891b2);"></div>
        <span id="topbar-titulo" class="font-semibold text-gray-700 text-base">Inicio</span>
      </div>
      <div class="flex items-center gap-3">
        <span class="text-sm text-gray-400 hidden sm:block"><?= date('d \d\e F, Y') ?></span>
        <div class="w-px h-5 bg-gray-200"></div>
        <div class="flex items-center gap-2 text-sm text-gray-600 font-medium">
          <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold"
            style="background:linear-gradient(135deg,#0d9488,#0891b2);">
            <?= strtoupper(substr($u['nombre'], 0, 1)) ?>
          </div>
          Dr. <?= htmlspecialchars(explode(' ', $u['nombre'])[0]) ?>
        </div>
      </div>
    </header>

    <!-- Contenido -->
    <main class="flex-1 overflow-y-auto p-8" style="background:#f1f5f9;">

      <!-- ── INICIO ────────────────────────────────────────────── -->
      <section class="seccion activa" id="sec-inicio">

        <div class="mb-7">
          <h1 class="text-2xl font-bold text-gray-800">Bienvenido, Dr. <?= htmlspecialchars(explode(' ', $u['nombre'])[0]) ?> 👋</h1>
          <p class="text-sm text-gray-400 mt-1">Resumen de tu agenda y cupos disponibles.</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
          <div class="bg-white rounded-2xl p-6 flex items-center gap-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl shrink-0" style="background:linear-gradient(135deg,#0d9488,#14b8a6);">
              <svg class="w-7 h-7 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 12"/></svg>
            </div>
            <div>
              <p class="text-3xl font-extrabold text-gray-800 leading-none" id="stat-total">—</p>
              <p class="text-sm text-gray-400 mt-1 font-medium">Total cupos</p>
            </div>
          </div>
          <div class="bg-white rounded-2xl p-6 flex items-center gap-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl shrink-0" style="background:linear-gradient(135deg,#16a34a,#4ade80);">
              <svg class="w-7 h-7 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
              <p class="text-3xl font-extrabold text-gray-800 leading-none" id="stat-libres">—</p>
              <p class="text-sm text-gray-400 mt-1 font-medium">Disponibles</p>
            </div>
          </div>
          <div class="bg-white rounded-2xl p-6 flex items-center gap-5 shadow-sm border border-gray-100">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl shrink-0" style="background:linear-gradient(135deg,#6366f1,#8b5cf6);">
              <svg class="w-7 h-7 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
            </div>
            <div>
              <p class="text-3xl font-extrabold text-gray-800 leading-none" id="stat-ocupados">—</p>
              <p class="text-sm text-gray-400 mt-1 font-medium">Con cita</p>
            </div>
          </div>
        </div>

        <!-- Tabla cupos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100" style="overflow:visible;">
          <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <div>
              <h2 class="font-bold text-gray-800 text-base">Mis cupos próximos</h2>
              <p class="text-xs text-gray-400 mt-0.5">Agenda de atención futura</p>
            </div>
            <div style="display:flex;align-items:center;gap:8px;border:1.5px solid #e2e8f0;border-radius:10px;padding:7px 12px;background:#fff;min-width:180px;">
              <svg style="width:13px;height:13px;stroke:#94a3b8;fill:none;flex-shrink:0;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
              <input id="buscar-cupo" type="text" placeholder="Buscar fecha u hora…"
                style="border:none;outline:none;font-size:12px;color:#374151;background:transparent;width:100%;"
                oninput="renderCupos()">
            </div>
          </div>
          <div class="card-table" style="overflow-x:auto;border-radius:0 0 16px 16px;overflow:hidden;">
            <table style="width:100%;border-collapse:collapse;margin:0;">
              <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #f1f5f9;">
                  <th style="padding:11px 24px;text-align:left;background:transparent;">Fecha</th>
                  <th style="padding:11px 24px;text-align:left;background:transparent;">Hora</th>
                  <th style="padding:11px 24px;text-align:left;background:transparent;">Estado</th>
                  <th style="padding:11px 24px;text-align:left;background:transparent;">Paciente</th>
                </tr>
              </thead>
              <tbody id="tbody-cupos"></tbody>
            </table>
          </div>
        </div>

      </section>

      <!-- ── CONSULTAS ─────────────────────────────────────────── -->
      <section class="seccion" id="sec-consultas">

        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Consultas del día</h1>
          <p class="text-sm text-gray-400 mt-1">Pacientes agendados por fecha.</p>
        </div>

        <!-- Selector de fecha -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6 flex items-center gap-4 flex-wrap" style="overflow:visible;">

          <div class="flex items-center justify-center w-10 h-10 rounded-xl shrink-0" style="background:linear-gradient(135deg,#0d9488,#0891b2);">
            <svg class="w-5 h-5 stroke-white fill-none" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>

          <div style="flex:1;min-width:220px;max-width:360px;position:relative;" id="fecha-drop-wrap">
            <label class="tw-label">Fecha de consulta</label>
            <!-- Trigger -->
            <div id="fecha-drop-trigger" onclick="toggleFechaDrop()"
              style="display:flex;align-items:center;gap:10px;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;background:#fff;cursor:pointer;transition:border-color .15s;">
              <!-- Icono calendario mini -->
              <div id="fecha-drop-cal" style="width:38px;height:38px;border-radius:10px;background:#f0fdfa;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;overflow:hidden;">
                <span id="fecha-drop-mes" style="font-size:9px;font-weight:700;color:#0d9488;text-transform:uppercase;line-height:1;"></span>
                <span id="fecha-drop-dia" style="font-size:17px;font-weight:800;color:#0d9488;line-height:1.1;"></span>
              </div>
              <div style="flex:1;min-width:0;">
                <p id="fecha-drop-label" style="font-size:13px;font-weight:600;color:#94a3b8;margin:0;">— Elige una fecha —</p>
                <p id="fecha-drop-sub" style="font-size:11px;color:#cbd5e1;margin:0;display:none;"></p>
              </div>
              <svg id="fecha-drop-chevron" style="width:14px;height:14px;stroke:#94a3b8;fill:none;flex-shrink:0;transition:transform .2s;" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>

            <!-- Panel -->
            <div id="fecha-drop-panel"
              style="display:none;position:absolute;top:calc(100% + 6px);left:0;min-width:280px;background:#fff;border-radius:14px;border:1.5px solid #e2e8f0;box-shadow:0 8px 24px rgba(0,0,0,.12);z-index:100;overflow:hidden;">
              <div style="padding:10px 12px;border-bottom:1px solid #f1f5f9;background:#f8fafc;">
                <p style="font-size:11px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em;margin:0;">Fechas disponibles</p>
              </div>
              <div id="fecha-drop-list" style="max-height:260px;overflow-y:auto;padding:6px;"></div>
            </div>

            <input type="hidden" id="select-fecha-consulta">
          </div>

          <div class="flex items-center gap-2 ml-auto">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#22c55e;animation:pulse-dot 2s infinite;"></span>
            <span style="font-size:12px;color:#64748b;font-weight:500;">En vivo</span>
          </div>

        </div>

        <!-- Lista citas -->
        <div id="lista-citas" class="flex flex-col gap-3"></div>

        <!-- Sin citas -->
        <div id="sin-citas" style="display:none;">
          <div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
            <svg style="width:48px;height:48px;stroke:#cbd5e1;fill:none;margin:0 auto 12px;" viewBox="0 0 24 24" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <p style="color:#94a3b8;font-size:15px;font-weight:500;">No hay citas para esta fecha.</p>
          </div>
        </div>

      </section>

      <!-- ── MIS CUPOS ─────────────────────────────────────────── -->
      <section class="seccion" id="sec-cupos">

        <div class="mb-6">
          <h1 class="text-2xl font-bold text-gray-800">Mis Cupos</h1>
          <p class="text-sm text-gray-400 mt-1">Vista completa de todos tus horarios.</p>
        </div>

        <!-- Stats cupos -->
        <div class="grid grid-cols-3 gap-4 mb-6">
          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100">
            <div style="width:40px;height:40px;border-radius:12px;background:#f0fdfa;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg style="width:20px;height:20px;stroke:#0d9488;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15.5 12"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="cupo-total2">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Total</p>
            </div>
          </div>
          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100">
            <div style="width:40px;height:40px;border-radius:12px;background:#f0fdf4;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg style="width:20px;height:20px;stroke:#16a34a;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="cupo-libres2">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Disponibles</p>
            </div>
          </div>
          <div class="bg-white rounded-2xl px-5 py-4 flex items-center gap-4 shadow-sm border border-gray-100">
            <div style="width:40px;height:40px;border-radius:12px;background:#eef2ff;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
              <svg style="width:20px;height:20px;stroke:#6366f1;fill:none;" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <div>
              <p class="text-2xl font-extrabold text-gray-800 leading-none" id="cupo-ocupados2">—</p>
              <p class="text-xs text-gray-400 mt-0.5 font-medium">Con cita</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
          <div class="card-table" style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;margin:0;">
              <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #f1f5f9;">
                  <th style="padding:11px 24px;text-align:left;background:transparent;">Fecha</th>
                  <th style="padding:11px 24px;text-align:left;background:transparent;">Hora</th>
                  <th style="padding:11px 24px;text-align:left;background:transparent;">Estado</th>
                  <th style="padding:11px 24px;text-align:left;background:transparent;">Paciente</th>
                </tr>
              </thead>
              <tbody id="tbody-cupos2"></tbody>
            </table>
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
  // ── Títulos por sección ──────────────────────────────────────────
  const titulos = { inicio:'Inicio', consultas:'Consultas del día', cupos:'Mis Cupos' };

  // ── Sidebar ──────────────────────────────────────────────────────
  function activarSeccion(sec) {
    const secs = Object.keys(titulos);
    const s = secs.includes(sec) ? sec : 'inicio';
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

  // ── Helpers ──────────────────────────────────────────────────────
  function horaLabel(h) {
    const [hh, mm] = h.split(':');
    let h12 = parseInt(hh);
    const ap = h12 >= 12 ? 'p.m' : 'a.m';
    if (h12 > 12) h12 -= 12;
    if (h12 === 0) h12 = 12;
    return `${String(h12).padStart(2,'0')}:${mm} ${ap}`;
  }

  function fechaFmt(f) {
    const [y,m,d] = f.split('-');
    return `${d}/${m}/${y}`;
  }

  const estadoCfg = {
    confirmada:   { bg:'#f0fdf4', color:'#16a34a', label:'Confirmada'   },
    realizada:    { bg:'#eff6ff', color:'#2563eb', label:'Realizada'     },
    no_realizada: { bg:'#fef2f2', color:'#dc2626', label:'No realizada'  },
    disponible:   { bg:'#f0fdfa', color:'#0d9488', label:'Disponible'    },
    con_cita:     { bg:'#eef2ff', color:'#6366f1', label:'Con cita'      },
    bloqueado:    { bg:'#f8fafc', color:'#64748b', label:'Bloqueado'     },
  };

  function badge(key) {
    const c = estadoCfg[key] || { bg:'#f1f5f9', color:'#64748b', label: key };
    return `<span style="padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;background:${c.bg};color:${c.color};">${c.label}</span>`;
  }

  // ── Cupos globales ────────────────────────────────────────────────
  let cuposData = [];

  function renderCupos() {
    const q = (document.getElementById('buscar-cupo')?.value || '').toLowerCase();
    const lista = cuposData.filter(c =>
      !q || c.fecha.includes(q) || c.hora.includes(q) || (c.paciente||'').toLowerCase().includes(q)
    );

    const hoy = new Date().toISOString().split('T')[0];

    const filas = (arr, tbodyId) => {
      document.getElementById(tbodyId).innerHTML = arr.length
        ? arr.map((c, i) => {
            let estKey = 'disponible';
            if (!parseInt(c.disponible)) estKey = 'bloqueado';
            else if (['confirmada','realizada'].includes(c.cita_estado)) estKey = c.cita_estado;
            else if (c.cita_estado === 'no_realizada') estKey = 'no_realizada';
            else if (c.cita_estado) estKey = 'con_cita';

            const esHoy = c.fecha === hoy;
            return `<tr style="border-bottom:1px solid #f1f5f9;${i%2!==0?'background:#fafafa;':''}">
              <td style="padding:13px 24px;">
                <div style="display:flex;align-items:center;gap:8px;">
                  ${esHoy ? '<span style="background:#0d9488;color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">Hoy</span>' : ''}
                  <span style="font-weight:600;color:#1f2937;font-size:13px;">${fechaFmt(c.fecha)}</span>
                </div>
              </td>
              <td style="padding:13px 24px;font-weight:600;color:#374151;font-size:13px;">${horaLabel(c.hora.substring(0,5))}</td>
              <td style="padding:13px 24px;">${badge(estKey)}</td>
              <td style="padding:13px 24px;color:#64748b;font-size:13px;">${c.paciente || '—'}</td>
            </tr>`;
          }).join('')
        : `<tr><td colspan="4" style="padding:40px;text-align:center;color:#94a3b8;font-size:14px;">${q ? 'Sin resultados.' : 'No hay cupos próximos.'}</td></tr>`;
    };

    filas(lista, 'tbody-cupos');
    filas(cuposData, 'tbody-cupos2');
  }

  function cargarCupos() {
    fetch('gateway.php?svc=schedules&action=listar_medico')
      .then(r => r.json())
      .then(data => {
        if (!Array.isArray(data)) return;
        cuposData = data;

        const total    = data.length;
        const libres   = data.filter(c => parseInt(c.disponible) && !c.cita_estado).length;
        const ocupados = data.filter(c => ['confirmada','realizada'].includes(c.cita_estado)).length;

        ['stat-total','cupo-total2'].forEach(id => document.getElementById(id).textContent = total);
        ['stat-libres','cupo-libres2'].forEach(id => document.getElementById(id).textContent = libres);
        ['stat-ocupados','cupo-ocupados2'].forEach(id => document.getElementById(id).textContent = ocupados);

        renderCupos();
      }).catch(() => {});
  }

  // ── Dropdown fecha consultas ─────────────────────────────────────
  const diasSem  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
  const mesesCrt = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
  const mesesFull= ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  let fechaDropOpen = false;
  let fechasDisponibles = [];

  function toggleFechaDrop() {
    fechaDropOpen = !fechaDropOpen;
    document.getElementById('fecha-drop-panel').style.display     = fechaDropOpen ? 'block' : 'none';
    document.getElementById('fecha-drop-chevron').style.transform  = fechaDropOpen ? 'rotate(180deg)' : 'rotate(0deg)';
    document.getElementById('fecha-drop-trigger').style.borderColor = fechaDropOpen ? '#0d9488' : '#e2e8f0';
    if (fechaDropOpen) setTimeout(() => document.addEventListener('click', closeFechaDropOutside), 0);
  }

  function closeFechaDropOutside(e) {
    if (!document.getElementById('fecha-drop-wrap').contains(e.target)) {
      fechaDropOpen = false;
      document.getElementById('fecha-drop-panel').style.display     = 'none';
      document.getElementById('fecha-drop-chevron').style.transform  = 'rotate(0deg)';
      document.getElementById('fecha-drop-trigger').style.borderColor = '#e2e8f0';
      document.removeEventListener('click', closeFechaDropOutside);
    }
  }

  function selectFecha(fecha, citasCount) {
    document.getElementById('select-fecha-consulta').value = fecha;

    const [y, m, d] = fecha.split('-');
    const dateObj   = new Date(+y, +m-1, +d);
    const hoy       = new Date().toISOString().split('T')[0];
    const esHoy     = fecha === hoy;

    document.getElementById('fecha-drop-mes').textContent  = mesesCrt[+m-1];
    document.getElementById('fecha-drop-dia').textContent  = d;
    document.getElementById('fecha-drop-cal').style.background = esHoy ? 'linear-gradient(135deg,#0d9488,#0891b2)' : '#f0fdfa';
    document.getElementById('fecha-drop-mes').style.color  = esHoy ? '#fff' : '#0d9488';
    document.getElementById('fecha-drop-dia').style.color  = esHoy ? '#fff' : '#0d9488';

    const label = document.getElementById('fecha-drop-label');
    const sub   = document.getElementById('fecha-drop-sub');
    label.textContent  = `${diasSem[dateObj.getDay()]}, ${d} de ${mesesFull[+m-1]} ${y}`;
    label.style.color  = '#1f2937';
    sub.textContent    = esHoy ? '📅 Hoy' : `${citasCount} cita${citasCount !== 1 ? 's' : ''}`;
    sub.style.display  = 'block';
    sub.style.color    = esHoy ? '#0d9488' : '#94a3b8';

    toggleFechaDrop();
    cargarCitas();
  }

  function buildFechaDropList() {
    const hoy  = new Date().toISOString().split('T')[0];
    const list = document.getElementById('fecha-drop-list');

    if (!fechasDisponibles.length) {
      list.innerHTML = '<p style="text-align:center;color:#94a3b8;font-size:13px;padding:20px;">No hay fechas con citas.</p>';
      return;
    }

    list.innerHTML = fechasDisponibles.map(f => {
      const [y, m, d]  = f.fecha.split('-');
      const dateObj    = new Date(+y, +m-1, +d);
      const esHoy      = f.fecha === hoy;
      const color      = esHoy ? '#0d9488' : '#374151';
      const calBg      = esHoy ? 'linear-gradient(135deg,#0d9488,#0891b2)' : '#f0fdfa';
      const calColor   = esHoy ? '#fff' : '#0d9488';
      const selected   = document.getElementById('select-fecha-consulta').value === f.fecha;

      return `
      <div onclick="selectFecha('${f.fecha}',${f.total||0})"
        style="display:flex;align-items:center;gap:12px;padding:9px 10px;border-radius:10px;cursor:pointer;margin-bottom:2px;${selected?'background:#f0fdfa;':'background:transparent;'}"
        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='${selected?'#f0fdfa':'transparent'}'">

        <!-- Mini calendario -->
        <div style="width:42px;height:42px;border-radius:10px;background:${calBg};display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;">
          <span style="font-size:9px;font-weight:700;color:${calColor};text-transform:uppercase;line-height:1;">${mesesCrt[+m-1]}</span>
          <span style="font-size:17px;font-weight:800;color:${calColor};line-height:1.1;">${d}</span>
        </div>

        <div style="flex:1;min-width:0;">
          <div style="display:flex;align-items:center;gap:6px;">
            <p style="font-weight:700;font-size:13px;color:${color};margin:0;">${diasSem[dateObj.getDay()]}, ${d} de ${mesesFull[+m-1]}</p>
            ${esHoy ? '<span style="background:#0d9488;color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:20px;">Hoy</span>' : ''}
          </div>
          <p style="font-size:11px;color:#94a3b8;margin:0;margin-top:2px;">${f.total || 0} cita${(f.total||0) !== 1 ? 's' : ''} agendada${(f.total||0) !== 1 ? 's' : ''}</p>
        </div>

        ${selected ? '<svg style="width:14px;height:14px;stroke:#0d9488;fill:none;flex-shrink:0;" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : ''}
      </div>`;
    }).join('');
  }

  // ── Consultas ─────────────────────────────────────────────────────
  function cargarFechas() {
    fetch('gateway.php?svc=schedules&action=fechas_citas')
      .then(r => r.json())
      .then(data => {
        if (!Array.isArray(data)) return;
        fechasDisponibles = data;
        buildFechaDropList();
      }).catch(() => {});
  }

  function cargarCitas() {
    const fecha = document.getElementById('select-fecha-consulta').value;
    const lista = document.getElementById('lista-citas');
    const sinCitas = document.getElementById('sin-citas');
    if (!fecha) { lista.innerHTML = ''; sinCitas.style.display = 'none'; return; }

    fetch(`gateway.php?svc=appointments&action=listar_medico&fecha=${fecha}`)
      .then(r => r.json())
      .then(data => {
        if (data.redirect) { location.href = data.redirect; return; }
        if (!data.length) { lista.innerHTML = ''; sinCitas.style.display = 'block'; return; }
        sinCitas.style.display = 'none';

        lista.innerHTML = data.map(c => {
          const cfg = estadoCfg[c.estado] || { bg:'#f1f5f9', color:'#64748b', label: c.estado };
          return `
          <div style="background:#fff;border-radius:16px;padding:16px 20px;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.05);display:flex;align-items:center;gap:16px;flex-wrap:wrap;">

            <!-- Hora -->
            <div style="text-align:center;min-width:72px;background:linear-gradient(135deg,#0d9488,#0891b2);border-radius:12px;padding:10px 8px;">
              <p style="font-size:18px;font-weight:800;color:#fff;margin:0;line-height:1;">${horaLabel(c.hora)}</p>
              <p style="font-size:10px;color:rgba(255,255,255,.7);margin:0;margin-top:2px;">hora</p>
            </div>

            <!-- Info paciente -->
            <div style="flex:1;min-width:160px;">
              <p style="font-size:15px;font-weight:700;color:#1f2937;margin:0;">${c.paciente}</p>
              <div style="display:flex;align-items:center;gap:10px;margin-top:5px;flex-wrap:wrap;">
                <span style="font-size:12px;color:#64748b;">📄 ${c.motivo || 'Sin motivo'}</span>
                ${c.telefono ? `<span style="font-size:12px;color:#64748b;">📞 ${c.telefono}</span>` : ''}
                <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;background:${cfg.bg};color:${cfg.color};">${cfg.label}</span>
              </div>
            </div>

            <!-- Acciones -->
            ${c.estado === 'confirmada' ? `
            <div style="display:flex;gap:8px;flex-shrink:0;">
              <button onclick="marcarCita(${c.id},'realizada')"
                style="padding:8px 16px;border-radius:10px;border:none;background:linear-gradient(135deg,#0d9488,#0891b2);color:#fff;font-size:12px;font-weight:700;cursor:pointer;">
                ✓ Realizada
              </button>
              <button onclick="marcarCita(${c.id},'no_realizada')"
                style="padding:8px 16px;border-radius:10px;border:1.5px solid #fecaca;background:#fef2f2;color:#dc2626;font-size:12px;font-weight:700;cursor:pointer;">
                ✗ No realizada
              </button>
            </div>` : ''}

          </div>`;
        }).join('');
      }).catch(() => {});
  }

  async function marcarCita(id, accion) {
    const r = await fetch(`gateway.php?svc=appointments&action=marcar&id=${id}&accion=${accion}`);
    const d = await r.json();
    if (d.ok) cargarCitas(); else Modal.error(d.msg || 'No se pudo actualizar la cita.');
  }

  // Polling cada 5 segundos
  setInterval(() => {
    const fecha = document.getElementById('select-fecha-consulta')?.value;
    if (fecha) { cargarCitas(); cargarFechas(); }
  }, 5000);

  // Carga inicial
  cargarCupos();
  cargarFechas();
</script>
</body>
</html>
