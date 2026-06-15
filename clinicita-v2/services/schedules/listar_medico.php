<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'medico') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$medicoId = (int)($u['medico_id'] ?? 0);
if (!$medicoId) {
    echo json_encode([]); exit;
}

$db   = getDB();
$stmt = $db->prepare(
    "SELECT h.id, h.fecha, h.hora, h.disponible,
            c.estado AS cita_estado, p.nombre AS paciente
     FROM horarios h
     LEFT JOIN citas c ON c.horario_id=h.id AND c.estado <> 'cancelada'
     LEFT JOIN usuarios p ON p.id = c.paciente_id
     WHERE h.medico_id=? AND h.fecha >= CURDATE()
     ORDER BY h.fecha, h.hora"
);
$stmt->bind_param('i', $medicoId);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
