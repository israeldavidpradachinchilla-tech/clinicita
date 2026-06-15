<?php
require 'conexion.php';
requiereRol('paciente');
$u = usuarioActual();

/* estado_horarios.php — Consulta AJAX de horarios, Devuelve si el horario está disponible, ocupado o pendiente, Actualiza el estado en tiempo real */
$medicoId = (int)($_GET['medico_id'] ?? 0);
$fecha    = $_GET['fecha'] ?? '';

if (!$medicoId || !$fecha) { echo json_encode([]); exit; }

// Limpiar pendientes expirados antes de consultar
$conexion->query("DELETE FROM citas WHERE estado='pendiente' AND creada_en < NOW() - INTERVAL 2 MINUTE");

$stmt = $conexion->prepare(
  "SELECT h.id, h.disponible,
          CASE
            WHEN c_conf.id IS NOT NULL THEN 'confirmada'
            WHEN c_pend.id IS NOT NULL THEN 'pendiente'
            ELSE NULL
          END AS cita_estado
   FROM horarios h
   LEFT JOIN citas c_conf ON c_conf.horario_id = h.id AND c_conf.estado IN ('confirmada','realizada')
   LEFT JOIN citas c_pend ON c_pend.horario_id = h.id AND c_pend.estado = 'pendiente' AND c_pend.paciente_id != ?
   WHERE h.medico_id = ? AND DATE(h.fecha) = ?");
$stmt->bind_param('iis', $u['id'], $medicoId, $fecha);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($rows);
