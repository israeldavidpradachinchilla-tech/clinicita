<?php
require 'conexion.php';
requiereRol('admin');

header('Content-Type: application/json');

$stmt = $conexion->query(
  "SELECT c.id, h.fecha, h.hora, c.estado, p.nombre AS paciente, p.email AS paciente_email,
          u.nombre AS medico, m.especialidad, m.id AS medico_id
   FROM citas c
   JOIN horarios h ON h.id=c.horario_id
   JOIN medicos m ON m.id=h.medico_id
   JOIN usuarios u ON u.id=m.usuario_id
   JOIN usuarios p ON p.id=c.paciente_id
   WHERE c.estado<>'cancelada'
   ORDER BY h.fecha, h.hora"
);
$citas = $stmt->fetch_all(MYSQLI_ASSOC);
echo json_encode($citas);
