<?php
require 'conexion.php';
requiereRol('paciente');
$u = usuarioActual();

/* crear_cita.php — Reserva de cita por paciente, Recibe horario y motivo, Comprueba disponibilidad, Inserta la cita, Marca el horario como no disponible */
header('Content-Type: application/json');

$horarioId = (int)($_POST['horario_id'] ?? 0);
$motivo    = trim($_POST['motivo'] ?? '');

$conexion->begin_transaction();
try {
    $stmt = $conexion->prepare('SELECT disponible FROM horarios WHERE id=? FOR UPDATE');
    $stmt->bind_param('i', $horarioId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row || !$row['disponible']) throw new Exception('no_disponible');

    $stmt2 = $conexion->prepare("SELECT id FROM citas WHERE horario_id=? AND estado IN ('confirmada','realizada')");
    $stmt2->bind_param('i', $horarioId);
    $stmt2->execute();
    if ($stmt2->get_result()->fetch_assoc()) throw new Exception('no_disponible');

    $stmt3 = $conexion->prepare("INSERT INTO citas (paciente_id,horario_id,motivo,estado) VALUES (?,?,?,'confirmada')");
    $stmt3->bind_param('iis', $u['id'], $horarioId, $motivo);
    $stmt3->execute();

    $conexion->query("UPDATE horarios SET disponible=0 WHERE id=$horarioId");
    $conexion->commit();
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['ok' => false, 'horario_id' => $horarioId]);
}
