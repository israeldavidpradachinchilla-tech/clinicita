<?php
require 'conexion.php';
$u = usuarioActual();
if ($u) {
    $stmt = $conexion->prepare('UPDATE usuarios SET session_token=NULL WHERE id=?');
    $stmt->bind_param('i', $u['id']);
    $stmt->execute();
}
session_destroy();
header('Location: ../index.html');
exit;
