<?php
require 'conexion.php';
requiereRol('paciente');
$u = usuarioActual();

header('Content-Type: application/json');

$horarioId = (int)($_POST['horario_id'] ?? 0);
if (!$horarioId) { echo json_encode(['ok' => false, 'msg' => 'ID inválido']); exit; }

$conexion->begin_transaction();
try {
    // Limpiar reservas expiradas de cualquier paciente (>2 min)
    $conexion->query("DELETE FROM citas WHERE estado='pendiente' AND creada_en < NOW() - INTERVAL 2 MINUTE");

    // Liberar reserva previa de este mismo paciente (si cambió de hora)
    $conexion->query("DELETE FROM citas WHERE paciente_id={$u['id']} AND estado='pendiente'");

    // Verificar que el slot sigue disponible
    $stmt = $conexion->prepare('SELECT disponible FROM horarios WHERE id=? FOR UPDATE');
    $stmt->bind_param('i', $horarioId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if (!$row || !$row['disponible']) {
        $conexion->rollback();
        echo json_encode(['ok' => false, 'msg' => 'ocupado']);
        exit;
    }

    // Verificar que no haya cita activa (pendiente/confirmada) para este horario
    $stmt2 = $conexion->prepare("SELECT id FROM citas WHERE horario_id=? AND estado IN ('pendiente','confirmada','realizada')");
    $stmt2->bind_param('i', $horarioId);
    $stmt2->execute();
    if ($stmt2->get_result()->fetch_assoc()) {
        $conexion->rollback();
        echo json_encode(['ok' => false, 'msg' => 'ocupado']);
        exit;
    }

    // Insertar reserva temporal como pendiente
    $stmt3 = $conexion->prepare("INSERT INTO citas (paciente_id, horario_id, motivo, estado) VALUES (?, ?, '', 'pendiente')");
    $stmt3->bind_param('ii', $u['id'], $horarioId);
    $stmt3->execute();

    $conexion->commit();
    echo json_encode(['ok' => true]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['ok' => false, 'msg' => $e->getMessage()]);
}
