<?php
require_once __DIR__ . '/../shared/db.php';

// Slots futuros disponibles para que el paciente reserve
$db  = getDB();
$res = $db->query(
    "SELECT h.id, h.medico_id,
            DATE_FORMAT(h.fecha,'%Y-%m-%d') AS fecha,
            h.hora, h.disponible,
            c.estado AS cita_estado
     FROM horarios h
     LEFT JOIN citas c ON c.horario_id=h.id
               AND c.estado IN ('confirmada','realizada')
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
