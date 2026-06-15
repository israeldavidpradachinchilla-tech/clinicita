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
    "SELECT DISTINCT h.fecha
     FROM citas c
     JOIN horarios h ON h.id=c.horario_id
     WHERE h.medico_id=? AND c.estado='confirmada'
     ORDER BY h.fecha"
);
$stmt->bind_param('i', $medicoId);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
