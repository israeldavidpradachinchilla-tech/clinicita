<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'paciente') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$db   = getDB();
$stmt = $db->prepare(
    "SELECT c.id, c.motivo, c.estado, c.creada_en, h.fecha, h.hora, u.nombre AS medico
     FROM citas c
     JOIN horarios h ON h.id = c.horario_id
     JOIN medicos m  ON m.id = h.medico_id
     JOIN usuarios u ON u.id = m.usuario_id
     WHERE c.paciente_id = ?
     ORDER BY h.fecha DESC, h.hora DESC"
);
$stmt->bind_param('i', $u['id']);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
