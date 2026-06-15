<?php
require_once __DIR__ . '/../shared/db.php';

$u  = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'paciente') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit;
}

$db   = getDB();
$stmt = $db->prepare('SELECT horario_id FROM citas WHERE id=? AND paciente_id=?');
$stmt->bind_param('ii', $id, $u['id']);
$stmt->execute();
$cita = $stmt->get_result()->fetch_assoc();

if (!$cita) {
    echo json_encode(['ok'=>false,'msg'=>'Cita no encontrada']); exit;
}

$db->query("UPDATE citas SET estado='cancelada' WHERE id=$id");
$db->query("UPDATE horarios SET disponible=1 WHERE id=" . (int)$cita['horario_id']);
echo json_encode(['ok'=>true]);
