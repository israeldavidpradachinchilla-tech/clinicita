<?php
require 'conexion.php';
requiereRol('admin');

$id = (int)$_POST['id'];

// Obtener usuario_id del médico
$res = $conexion->query("SELECT usuario_id FROM medicos WHERE id=$id");
if (!$res || $res->num_rows === 0) {
  header('Location: ../admin.php?error=medico_no_encontrado');
  exit;
}
$usuario_id = (int)$res->fetch_assoc()['usuario_id'];

// Eliminar en cascada: citas -> horarios -> medico -> usuario
$conexion->query("DELETE c FROM citas c JOIN horarios h ON h.id=c.horario_id WHERE h.medico_id=$id");
$conexion->query("DELETE FROM horarios WHERE medico_id=$id");
$conexion->query("DELETE FROM medicos WHERE id=$id");
$conexion->query("DELETE FROM usuarios WHERE id=$usuario_id");

header('Location: ../admin.php?ok=medico_eliminado');
exit;
