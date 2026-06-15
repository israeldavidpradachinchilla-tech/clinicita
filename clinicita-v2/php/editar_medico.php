<?php
require 'conexion.php';
requiereRol('admin');

$id           = (int)$_POST['id'];
$nombre       = trim($_POST['nombre']);
$especialidad = trim($_POST['especialidad']);
$email        = trim($_POST['email']);

// Obtener usuario_id del médico
$res = $conexion->query("SELECT usuario_id FROM medicos WHERE id=$id");
if (!$res || $res->num_rows === 0) {
  header('Location: ../admin.php?error=medico_no_encontrado');
  exit;
}
$usuario_id = (int)$res->fetch_assoc()['usuario_id'];

$stmtU = $conexion->prepare("UPDATE usuarios SET nombre=?, email=? WHERE id=?");
$stmtU->bind_param('ssi', $nombre, $email, $usuario_id);
$stmtU->execute();

if (!empty($_POST['password'])) {
  $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $stmtP = $conexion->prepare("UPDATE usuarios SET password=? WHERE id=?");
  $stmtP->bind_param('si', $hash, $usuario_id);
  $stmtP->execute();
}

$stmtM = $conexion->prepare("UPDATE medicos SET especialidad=? WHERE id=?");
$stmtM->bind_param('si', $especialidad, $id);
$stmtM->execute();

header('Location: ../admin.php?ok=medico_editado');
exit;
