<?php
require 'conexion.php';
requiereRol('paciente');
$u = usuarioActual();

$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if (!$email) {
  echo "<script>alert('El correo no puede estar vacío');history.back();</script>";
  exit;
}

// Si el paciente tiene tarjeta de identidad puede actualizar su documento
if ($u['tipo_documento'] === 'tarjeta' && !empty($_POST['cedula'])) {
  $cedula    = trim($_POST['cedula']);
  $tipo_doc  = $_POST['tipo_documento'] === 'cedula' ? 'cedula' : 'tarjeta';
  $stmt = $conexion->prepare("UPDATE usuarios SET email=?, telefono=?, cedula=?, tipo_documento=? WHERE id=?");
  $stmt->bind_param('ssssi', $email, $telefono, $cedula, $tipo_doc, $u['id']);
} else {
  $stmt = $conexion->prepare("UPDATE usuarios SET email=?, telefono=? WHERE id=?");
  $stmt->bind_param('ssi', $email, $telefono, $u['id']);
}

if (!$stmt->execute()) {
  echo "<script>alert('No se pudo actualizar: ese correo o documento ya está en uso');history.back();</script>";
  exit;
}

echo "<script>alert('Datos actualizados correctamente');location.href='../paciente.php';</script>";
