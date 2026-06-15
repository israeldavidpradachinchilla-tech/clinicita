<?php
require 'conexion.php';

$nombre   = trim($_POST['nombre']   ?? '');
$cedula   = trim($_POST['cedula']   ?? '');
$tipo_doc = ($_POST['tipo_documento'] ?? 'cedula') === 'tarjeta' ? 'tarjeta' : 'cedula';
$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$pass     = $_POST['password']      ?? '';

if (!$nombre || !$cedula || !$email || strlen($pass) < 6) {
    echo "<script>alert('Datos inválidos');history.back();</script>"; exit;
}

$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $conexion->prepare(
  'INSERT INTO usuarios (nombre,cedula,tipo_documento,email,password,rol,telefono) VALUES (?,?,?,?,?,"paciente",?)'
);
$stmt->bind_param('ssssss', $nombre, $cedula, $tipo_doc, $email, $hash, $telefono);

if (!$stmt->execute()) {
    echo "<script>alert('No se pudo registrar: correo o cédula ya existen');history.back();</script>";
    exit;
}
echo "<script>alert('Cuenta creada. Inicia sesión');location.href='../login.html';</script>";
