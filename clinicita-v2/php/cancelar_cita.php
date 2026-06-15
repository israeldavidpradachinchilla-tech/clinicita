<?php
require 'conexion.php';
requiereRol('paciente');
$u  = usuarioActual();
$id = (int)($_GET['id'] ?? 0);

$stmt = $conexion->prepare('SELECT horario_id FROM citas WHERE id=? AND paciente_id=?');
$stmt->bind_param('ii', $id, $u['id']);
$stmt->execute();
$cita = $stmt->get_result()->fetch_assoc();

if ($cita) {
    $conexion->query("UPDATE citas SET estado='cancelada' WHERE id=$id");
    $conexion->query("UPDATE horarios SET disponible=1 WHERE id=".(int)$cita['horario_id']);
}
header('Location: ../paciente.php');
