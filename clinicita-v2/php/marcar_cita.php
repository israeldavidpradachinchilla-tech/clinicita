<?php
require 'conexion.php';
requiereRol('medico');
$u = usuarioActual();

header('Content-Type: application/json');

// Obtener el medico_id del médico actual
$stmt = $conexion->prepare('SELECT id FROM medicos WHERE usuario_id=?');
$stmt->bind_param('i', $u['id']);
$stmt->execute();
$med = $stmt->get_result()->fetch_assoc();

if (!$med) {
    echo json_encode(['ok' => false, 'error' => 'Médico no encontrado']);
    exit;
}

$medico_id = $med['id'];

$id = (int)($_GET['id'] ?? 0);
$accion = $_GET['accion'] ?? '';
$estados = ['realizada', 'no_realizada'];

if (!$id || !in_array($accion, $estados, true)) {
    echo json_encode(['ok' => false, 'error' => 'Parámetros inválidos']);
    exit;
}

// Verificar que la cita pertenece al médico actual
$stmt = $conexion->prepare(
  'SELECT h.medico_id FROM citas c JOIN horarios h ON h.id=c.horario_id WHERE c.id=?'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$cita = $stmt->get_result()->fetch_assoc();

if (!$cita) {
    echo json_encode(['ok' => false, 'error' => 'Cita no encontrada']);
    exit;
}

if ((int)$cita['medico_id'] !== (int)$medico_id) {
    echo json_encode(['ok' => false, 'error' => 'No autorizado - cita de otro médico']);
    exit;
}

// Actualizar el estado de la cita
$stmt = $conexion->prepare('UPDATE citas SET estado=? WHERE id=?');
$stmt->bind_param('si', $accion, $id);
if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'estado' => $accion]);
} else {
    echo json_encode(['ok' => false, 'error' => 'Error al actualizar: ' . $stmt->error]);
}
exit;

