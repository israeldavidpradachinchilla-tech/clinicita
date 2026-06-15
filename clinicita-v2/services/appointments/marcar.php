<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'medico') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$id     = (int)($_GET['id'] ?? 0);
$accion = $_GET['accion'] ?? '';
$estados = ['realizada'=>true,'no_realizada'=>true];

if (!$id || !isset($estados[$accion])) {
    echo json_encode(['ok'=>false,'msg'=>'Parámetros inválidos']); exit;
}

$medicoId = (int)($u['medico_id'] ?? 0);
$db = getDB();

// Verificar que la cita pertenece a este médico
$stmt = $db->prepare(
    "SELECT c.id FROM citas c
     JOIN horarios h ON h.id=c.horario_id
     WHERE c.id=? AND h.medico_id=?"
);
$stmt->bind_param('ii', $id, $medicoId);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    echo json_encode(['ok'=>false,'msg'=>'Cita no encontrada']); exit;
}

$stmt2 = $db->prepare("UPDATE citas SET estado=? WHERE id=?");
$stmt2->bind_param('si', $accion, $id);
$stmt2->execute();
echo json_encode(['ok'=>true]);
