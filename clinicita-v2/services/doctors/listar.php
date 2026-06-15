<?php
require_once __DIR__ . '/../shared/db.php';

// Cualquier usuario autenticado puede listar médicos
$db  = getDB();
$res = $db->query(
    "SELECT m.id, u.nombre, m.especialidad, u.email
     FROM medicos m
     JOIN usuarios u ON u.id = m.usuario_id
     ORDER BY m.especialidad, u.nombre"
);
echo json_encode($res->fetch_all(MYSQLI_ASSOC));
