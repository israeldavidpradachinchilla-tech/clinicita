<?php
require 'conexion.php';
requiereRol('admin');

$medicoId   = (int)($_POST['medico_id'] ?? 0);
$fecha      = $_POST['fecha'] ?? '';
$fechaFin   = $_POST['fecha_fin'] ?? '';
$horaInicio = $_POST['hora_inicio'] ?? '';
$horaFin    = $_POST['hora_fin'] ?? '';
$intervalo  = (int)($_POST['intervalo'] ?? 30);
$dias       = $_POST['dias'] ?? [];

if (!$medicoId || !$fecha || !$horaInicio || !$horaFin || $intervalo < 1 || $intervalo > 1440) {
    echo "<script>alert('Datos inválidos');history.back();</script>"; exit;
}

$start = DateTime::createFromFormat('H:i', $horaInicio);
$end = DateTime::createFromFormat('H:i', $horaFin);
if (!$start || !$end || $start >= $end) {
    echo "<script>alert('Rango de hora inválido');history.back();</script>"; exit;
}

$days = array_map('intval', $dias);
$current = new DateTime($fecha);
$endDate = $fechaFin ? new DateTime($fechaFin) : clone $current;

if ($endDate < $current) {
    echo "<script>alert('Fecha final debe ser igual o posterior a la fecha inicial');history.back();</script>"; exit;
}

$stmt = $conexion->prepare(
  'INSERT IGNORE INTO horarios (medico_id, fecha, hora, disponible) VALUES (?, ?, ?, 1)'
);
if (!$stmt) {
    echo "<script>alert('Error de base de datos');history.back();</script>"; exit;
}

$created = 0;
while ($current <= $endDate) {
    if (!$days || in_array((int)$current->format('N'), $days, true)) {
        $slot = clone $start;
        while ($slot < $end) {
            $fechaSlot = $current->format('Y-m-d');
            $horaSlot = $slot->format('H:i:s');
            $stmt->bind_param('iss', $medicoId, $fechaSlot, $horaSlot);
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                $created++;
            }
            $slot->modify("+{$intervalo} minutes");
        }
    }
    $current->modify('+1 day');
}

if ($created > 0) {
    echo "<script>alert('{$created} cupos creados');location.href='../admin.php';</script>";
} else {
    echo "<script>alert('No se crearon cupos nuevos, posiblemente ya existen');history.back();</script>";
}
