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

$fecha = $_GET['fecha'] ?? date('Y-m-d');
$db    = getDB();

$stmt = $db->prepare(
    "SELECT c.id, h.hora, c.motivo, c.estado,
            p.nombre AS paciente, p.telefono, p.cedula
     FROM citas c
     JOIN horarios h ON h.id = c.horario_id
     JOIN usuarios p ON p.id = c.paciente_id
     WHERE h.medico_id=? AND h.fecha=? AND c.estado <> 'cancelada'
     ORDER BY h.hora"
);
$stmt->bind_param('is', $medicoId, $fecha);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
