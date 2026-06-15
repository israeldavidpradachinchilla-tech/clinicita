<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit;
}

$db = getDB();

// Eliminar el usuario (CASCADE elimina medico, horarios y citas)
$s = $db->prepare(
    'DELETE u FROM usuarios u
     JOIN medicos m ON m.usuario_id=u.id
     WHERE m.id=?'
);
$s->bind_param('i', $id);
$s->execute();

echo json_encode(['ok'=>true]);
