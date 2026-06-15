<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$nombre    = trim($_POST['nombre']   ?? '');
$cedula    = trim($_POST['cedula']   ?? '');
$email     = trim($_POST['email']    ?? '');
$esp       = trim($_POST['especialidad']       ?? '');
$espNueva  = trim($_POST['especialidad_nueva'] ?? '');
$pass      = $_POST['password'] ?? '';

if (!$nombre || !$cedula || !$email || (!$esp && !$espNueva) || strlen($pass) < 6) {
    echo json_encode(['ok'=>false,'msg'=>'Datos inválidos']); exit;
}

$especialidad = $espNueva ?: $esp;
$db = getDB();
$db->begin_transaction();
try {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $s1 = $db->prepare('INSERT INTO usuarios (nombre,cedula,email,password,rol) VALUES (?,?,?,?,"medico")');
    $s1->bind_param('ssss', $nombre, $cedula, $email, $hash);
    $s1->execute();
    $uid = $db->insert_id;

    $s2 = $db->prepare('INSERT INTO medicos (usuario_id,especialidad) VALUES (?,?)');
    $s2->bind_param('is', $uid, $especialidad);
    $s2->execute();

    $db->commit();
    echo json_encode(['ok'=>true,'msg'=>'Médico creado correctamente']);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['ok'=>false,'msg'=>'Error: correo o cédula duplicados']);
}
