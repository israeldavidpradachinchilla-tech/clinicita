<?php
require 'conexion.php';
requiereRol('paciente');
$u = usuarioActual();

header('Content-Type: application/json');

$stmt = $conexion->prepare(
  "SELECT c.id, c.motivo, c.estado, c.creada_en, h.fecha, h.hora, u.nombre AS medico
   FROM citas c
   JOIN horarios h ON h.id = c.horario_id
   JOIN medicos m ON m.id = h.medico_id
   JOIN usuarios u ON u.id = m.usuario_id
   WHERE c.paciente_id = ?
   ORDER BY h.fecha DESC, h.hora DESC"
);
$stmt->bind_param('i', $u['id']);
$stmt->execute();
$citas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
echo json_encode($citas);
