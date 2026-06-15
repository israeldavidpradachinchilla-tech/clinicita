<?php
require 'conexion.php';
requiereRol('medico');
$u = usuarioActual();

header('Content-Type: application/json');

$stmt = $conexion->prepare('SELECT id FROM medicos WHERE usuario_id=?');
$stmt->bind_param('i', $u['id']);
$stmt->execute();
$med = $stmt->get_result()->fetch_assoc();
if (!$med) { echo json_encode([]); exit; }

$fecha = $_GET['fecha'] ?? date('Y-m-d');

$stmt2 = $conexion->prepare(
  "SELECT c.id, h.hora, c.motivo, c.estado, p.nombre AS paciente, p.telefono, p.cedula
   FROM citas c
   JOIN horarios h ON h.id = c.horario_id
   JOIN usuarios p ON p.id = c.paciente_id
   WHERE h.medico_id = ? AND h.fecha = ? AND c.estado <> 'cancelada'
   ORDER BY h.hora"
);
$stmt2->bind_param('is', $med['id'], $fecha);
$stmt2->execute();
$rows = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
echo json_encode($rows);
