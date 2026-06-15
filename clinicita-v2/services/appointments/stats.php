<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$db = getDB();
$totalCitas     = $db->query("SELECT COUNT(*) AS n FROM citas WHERE estado='confirmada'")->fetch_assoc()['n'];
$totalPacientes = $db->query("SELECT COUNT(*) AS n FROM usuarios WHERE rol='paciente'")->fetch_assoc()['n'];
$totalMedicos   = $db->query("SELECT COUNT(*) AS n FROM medicos")->fetch_assoc()['n'];

echo json_encode([
    'ok'             => true,
    'total_citas'    => (int)$totalCitas,
    'total_pacientes'=> (int)$totalPacientes,
    'total_medicos'  => (int)$totalMedicos,
]);
