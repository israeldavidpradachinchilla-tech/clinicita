<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'paciente') {
    echo json_encode(['ok'=>false,'msg'=>'Solo los pacientes pueden agendar citas']); exit;
}

$horarioId = (int)($_POST['horario_id'] ?? 0);
$motivo    = trim($_POST['motivo'] ?? '');

if (!$horarioId) {
    echo json_encode(['ok'=>false,'msg'=>'Horario inválido']); exit;
}

$db = getDB();
$db->begin_transaction();
try {
    $stmt = $db->prepare('SELECT disponible FROM horarios WHERE id=? FOR UPDATE');
    $stmt->bind_param('i', $horarioId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row || !$row['disponible']) throw new Exception('no_disponible');

    $stmt2 = $db->prepare("SELECT id FROM citas WHERE horario_id=? AND estado IN ('confirmada','realizada')");
    $stmt2->bind_param('i', $horarioId);
    $stmt2->execute();
    if ($stmt2->get_result()->fetch_assoc()) throw new Exception('no_disponible');

    $pacienteId = $u['id'];
    $stmt3 = $db->prepare("INSERT INTO citas (paciente_id,horario_id,motivo,estado) VALUES (?,?,?,'confirmada')");
    $stmt3->bind_param('iis', $pacienteId, $horarioId, $motivo);
    $stmt3->execute();

    $db->query("UPDATE horarios SET disponible=0 WHERE id=$horarioId");
    $db->commit();
    echo json_encode(['ok'=>true]);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['ok'=>false,'horario_id'=>$horarioId]);
}
