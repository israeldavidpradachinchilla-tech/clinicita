<?php
require 'conexion.php';
requiereRol('admin');

$disponible = (int)($_POST['disponible'] ?? 0);

// Bloquear/desbloquear cupo individual por id
if (!empty($_POST['id'])) {
  $id = (int)$_POST['id'];
  $stmt = $conexion->prepare("UPDATE horarios SET disponible=? WHERE id=?");
  $stmt->bind_param('ii', $disponible, $id);
  $ok = $stmt->execute();
  echo json_encode(['ok' => $ok]);
  exit;
}

// Bloquear/desbloquear todos los cupos de un médico en una fecha
if (!empty($_POST['medico_id']) && !empty($_POST['fecha'])) {
  $medicoId = (int)$_POST['medico_id'];
  $fecha    = $_POST['fecha'];
  $stmt = $conexion->prepare("UPDATE horarios SET disponible=? WHERE medico_id=? AND fecha=?");
  $stmt->bind_param('iis', $disponible, $medicoId, $fecha);
  $ok = $stmt->execute();
  echo json_encode(['ok' => $ok]);
  exit;
}

echo json_encode(['ok' => false]);
