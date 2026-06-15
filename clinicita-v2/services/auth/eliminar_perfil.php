<?php
require_once __DIR__ . '/../shared/db.php';
require_once __DIR__ . '/../shared/config.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'paciente') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$db   = getDB();
$stmt = $db->prepare('DELETE FROM usuarios WHERE id=? AND rol="paciente"');
$stmt->bind_param('i', $u['id']);
$stmt->execute();

setcookie('clinicita_token', '', time()-3600, BASE_PATH);
echo json_encode(['ok'=>true,'redirect'=>BASE_PATH.'index.html']);
