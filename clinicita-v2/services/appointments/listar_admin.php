<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$db  = getDB();
$res = $db->query(
    "SELECT c.id, h.fecha, h.hora, c.estado,
            p.nombre AS paciente, p.email AS paciente_email,
            u.nombre AS medico, m.especialidad, m.id AS medico_id
     FROM citas c
     JOIN horarios h ON h.id = c.horario_id
     JOIN medicos m  ON m.id = h.medico_id
     JOIN usuarios u ON u.id = m.usuario_id
     JOIN usuarios p ON p.id = c.paciente_id
     WHERE c.estado <> 'cancelada'
     ORDER BY h.fecha, h.hora"
);
echo json_encode($res->fetch_all(MYSQLI_ASSOC));
