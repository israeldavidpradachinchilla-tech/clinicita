<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$db = getDB();

// Bloquear/desbloquear un cupo individual
if (isset($_POST['id']) && $_POST['id'] !== '') {
    $id   = (int)$_POST['id'];
    $disp = (int)$_POST['disponible'];

    $ok = $db->query("UPDATE horarios SET disponible=$disp WHERE id=$id");

    if (!$ok) {
        echo json_encode(['ok'=>false,'msg'=>$db->error]); exit;
    }
    echo json_encode(['ok'=>true,'id'=>$id,'disp'=>$disp,'afectados'=>$db->affected_rows]);
    exit;
}

// Bloquear/desbloquear todos los cupos de un día
if (isset($_POST['medico_id']) && $_POST['medico_id'] !== '' && isset($_POST['fecha']) && $_POST['fecha'] !== '') {
    $medicoId = (int)$_POST['medico_id'];
    $fecha    = $db->real_escape_string($_POST['fecha']);
    $disp     = (int)$_POST['disponible'];

    $ok = $db->query("UPDATE horarios SET disponible=$disp WHERE medico_id=$medicoId AND fecha='$fecha'");

    if (!$ok) {
        echo json_encode(['ok'=>false,'msg'=>$db->error]); exit;
    }
    echo json_encode(['ok'=>true,'afectados'=>$db->affected_rows]);
    exit;
}

echo json_encode(['ok'=>false,'msg'=>'Parámetros inválidos','post'=>$_POST]);
