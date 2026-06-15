<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$db = getDB();

// Eliminar cupo individual
if (!empty($_POST['id'])) {
    $id = (int)$_POST['id'];
    $db->query("DELETE FROM horarios WHERE id=$id");
    echo json_encode(['ok'=>true]); exit;
}

// Eliminar todos los cupos de un día
if (!empty($_POST['medico_id']) && !empty($_POST['fecha'])) {
    $medicoId = (int)$_POST['medico_id'];
    $fecha    = $db->real_escape_string($_POST['fecha']);
    $db->query("DELETE FROM horarios WHERE medico_id=$medicoId AND fecha='$fecha'");
    echo json_encode(['ok'=>true]); exit;
}

echo json_encode(['ok'=>false,'msg'=>'Parámetros inválidos']);
