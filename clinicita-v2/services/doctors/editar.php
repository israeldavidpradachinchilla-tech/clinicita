<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$id          = (int)($_POST['id'] ?? 0);
$nombre      = trim($_POST['nombre']       ?? '');
$especialidad= trim($_POST['especialidad'] ?? '');
$email       = trim($_POST['email']        ?? '');
$pass        = $_POST['password'] ?? '';

if (!$id || !$nombre || !$especialidad || !$email) {
    echo json_encode(['ok'=>false,'msg'=>'Datos incompletos']); exit;
}

$db = getDB();
$db->begin_transaction();
try {
    // Obtener usuario_id del médico
    $s = $db->prepare('SELECT usuario_id FROM medicos WHERE id=?');
    $s->bind_param('i', $id);
    $s->execute();
    $row = $s->get_result()->fetch_assoc();
    if (!$row) throw new Exception('Médico no encontrado');

    $uid = $row['usuario_id'];

    if ($pass && strlen($pass) >= 6) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $s1 = $db->prepare('UPDATE usuarios SET nombre=?,email=?,password=? WHERE id=?');
        $s1->bind_param('sssi', $nombre, $email, $hash, $uid);
    } else {
        $s1 = $db->prepare('UPDATE usuarios SET nombre=?,email=? WHERE id=?');
        $s1->bind_param('ssi', $nombre, $email, $uid);
    }
    $s1->execute();

    $s2 = $db->prepare('UPDATE medicos SET especialidad=? WHERE id=?');
    $s2->bind_param('si', $especialidad, $id);
    $s2->execute();

    $db->commit();
    echo json_encode(['ok'=>true,'msg'=>'Médico actualizado']);
} catch (Exception $e) {
    $db->rollback();
    echo json_encode(['ok'=>false,'msg'=>$e->getMessage()]);
}
