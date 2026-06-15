<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$db  = getDB();
$res = $db->query(
    "SELECT h.id, h.medico_id, h.fecha, h.hora, h.disponible,
            u.nombre AS medico
     FROM horarios h
     JOIN medicos m  ON m.id = h.medico_id
     JOIN usuarios u ON u.id = m.usuario_id
     WHERE h.fecha >= CURDATE()
     ORDER BY h.fecha, h.hora"
);
$rows = $res->fetch_all(MYSQLI_ASSOC);
foreach ($rows as &$r) {
    $r['id']         = (int)$r['id'];
    $r['medico_id']  = (int)$r['medico_id'];
    $r['disponible'] = (int)$r['disponible'];
}
echo json_encode($rows);
