<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'admin') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$medicoId   = (int)($_POST['medico_id']   ?? 0);
$fecha      = $_POST['fecha']      ?? '';
$fechaFin   = $_POST['fecha_fin']  ?? '';
$horaInicio = $_POST['hora_inicio'] ?? '';
$horaFin    = $_POST['hora_fin']    ?? '';
$intervalo  = (int)($_POST['intervalo'] ?? 30);
$dias       = $_POST['dias'] ?? [];

if (!$medicoId || !$fecha || !$horaInicio || !$horaFin || $intervalo < 1 || $intervalo > 1440) {
    echo json_encode(['ok'=>false,'msg'=>'Datos inválidos']); exit;
}

$start = DateTime::createFromFormat('H:i', $horaInicio);
$end   = DateTime::createFromFormat('H:i', $horaFin);
if (!$start || !$end || $start >= $end) {
    echo json_encode(['ok'=>false,'msg'=>'Rango de hora inválido']); exit;
}

$days    = array_map('intval', $dias);
$current = new DateTime($fecha);
$endDate = $fechaFin ? new DateTime($fechaFin) : clone $current;
if ($endDate < $current) {
    echo json_encode(['ok'=>false,'msg'=>'Fecha final inválida']); exit;
}

$db   = getDB();
$stmt = $db->prepare('INSERT IGNORE INTO horarios (medico_id,fecha,hora,disponible) VALUES (?,?,?,1)');
if (!$stmt) {
    echo json_encode(['ok'=>false,'msg'=>'Error de base de datos']); exit;
}

$created = 0;
while ($current <= $endDate) {
    if (!$days || in_array((int)$current->format('N'), $days, true)) {
        $slot = clone $start;
        while ($slot < $end) {
            $f = $current->format('Y-m-d');
            $h = $slot->format('H:i:s');
            $stmt->bind_param('iss', $medicoId, $f, $h);
            $stmt->execute();
            if ($stmt->affected_rows > 0) $created++;
            $slot->modify("+{$intervalo} minutes");
        }
    }
    $current->modify('+1 day');
}

echo json_encode(['ok'=>true,'created'=>$created,'msg'=>"$created cupos creados"]);
