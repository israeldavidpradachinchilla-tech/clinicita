<?php
require_once __DIR__ . '/../shared/db.php';

$nombre   = trim($_POST['nombre']   ?? '');
$cedula   = trim($_POST['cedula']   ?? '');
$tipo_doc = ($_POST['tipo_documento'] ?? 'cedula') === 'tarjeta' ? 'tarjeta' : 'cedula';
$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$pass     = $_POST['password']      ?? '';

if (!$nombre || !$cedula || !$email || strlen($pass) < 6) {
    echo json_encode(['ok'=>false,'msg'=>'Datos inválidos: revisa que todos los campos estén completos']); exit;
}

$db   = getDB();
$hash = password_hash($pass, PASSWORD_DEFAULT);
$stmt = $db->prepare(
    'INSERT INTO usuarios (nombre,cedula,tipo_documento,email,password,rol,telefono) VALUES (?,?,?,?,?,"paciente",?)'
);
$stmt->bind_param('ssssss', $nombre, $cedula, $tipo_doc, $email, $hash, $telefono);

if (!$stmt->execute()) {
    echo json_encode(['ok'=>false,'msg'=>'El correo o la cédula ya están registrados']); exit;
}

echo json_encode(['ok'=>true,'msg'=>'Cuenta creada. Ya puedes iniciar sesión']);
