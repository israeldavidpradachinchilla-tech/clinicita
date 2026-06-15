<?php
require_once __DIR__ . '/../shared/db.php';

$medicoId = (int)($_GET['medico_id'] ?? 0);
$fecha    = $_GET['fecha'] ?? '';

if (!$medicoId || !$fecha) {
    echo json_encode([]); exit;
}

$db   = getDB();
$stmt = $db->prepare(
    "SELECT h.id, h.disponible, c.estado AS cita_estado
     FROM horarios h
     LEFT JOIN citas c ON c.horario_id=h.id
               AND c.estado IN ('confirmada','realizada')
     WHERE h.medico_id=? AND h.fecha=?"
);
$stmt->bind_param('is', $medicoId, $fecha);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
