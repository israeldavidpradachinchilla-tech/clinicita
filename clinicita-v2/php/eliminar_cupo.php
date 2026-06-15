<?php
require 'conexion.php';
requiereRol('admin');

// Eliminar cupo individual por id
if (!empty($_POST['id'])) {
  $id   = (int)$_POST['id'];
  $stmt = $conexion->prepare("DELETE FROM horarios WHERE id=?");
  $stmt->bind_param('i', $id);
  echo json_encode(['ok' => $stmt->execute()]);
  exit;
}

// Eliminar todos los cupos de un médico en una fecha
if (!empty($_POST['medico_id']) && !empty($_POST['fecha'])) {
  $medicoId = (int)$_POST['medico_id'];
  $fecha    = $_POST['fecha'];
  $stmt = $conexion->prepare("DELETE FROM horarios WHERE medico_id=? AND fecha=?");
  $stmt->bind_param('is', $medicoId, $fecha);
  echo json_encode(['ok' => $stmt->execute()]);
  exit;
}

echo json_encode(['ok' => false]);
