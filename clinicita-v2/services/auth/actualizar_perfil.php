<?php
require_once __DIR__ . '/../shared/db.php';

$u = $GLOBALS['gateway_user'];
if ($u['rol'] !== 'paciente') {
    echo json_encode(['ok'=>false,'msg'=>'Sin permiso']); exit;
}

$email    = trim($_POST['email']    ?? '');
$telefono = trim($_POST['telefono'] ?? '');

if (!$email) {
    echo json_encode(['ok'=>false,'msg'=>'El correo no puede estar vacío']); exit;
}

$db = getDB();

// ── Cambio de contraseña (opcional) ──────────────────────────────
$passActual = $_POST['password_actual'] ?? '';
$passNueva  = $_POST['password_nueva']  ?? '';

if ($passActual !== '' || $passNueva !== '') {
    // Verificar contraseña actual
    $row = $db->query("SELECT password FROM usuarios WHERE id={$u['id']}")->fetch_assoc();
    if (!$row || !password_verify($passActual, $row['password'])) {
        echo json_encode(['ok'=>false,'msg'=>'La contraseña actual es incorrecta.']); exit;
    }
    if (strlen($passNueva) < 6) {
        echo json_encode(['ok'=>false,'msg'=>'La nueva contraseña debe tener al menos 6 caracteres.']); exit;
    }
    $hash = password_hash($passNueva, PASSWORD_DEFAULT);
    $stmt = $db->prepare('UPDATE usuarios SET password=? WHERE id=?');
    $stmt->bind_param('si', $hash, $u['id']);
    if (!$stmt->execute()) {
        echo json_encode(['ok'=>false,'msg'=>'Error al actualizar la contraseña.']); exit;
    }
}

// ── Datos personales ──────────────────────────────────────────────
if ($u['tipo_documento'] === 'tarjeta' && !empty($_POST['cedula'])) {
    $cedula   = trim($_POST['cedula']);
    $tipo_doc = ($_POST['tipo_documento'] ?? 'tarjeta') === 'cedula' ? 'cedula' : 'tarjeta';
    $stmt = $db->prepare('UPDATE usuarios SET email=?,telefono=?,cedula=?,tipo_documento=? WHERE id=?');
    $stmt->bind_param('ssssi', $email, $telefono, $cedula, $tipo_doc, $u['id']);
} else {
    $stmt = $db->prepare('UPDATE usuarios SET email=?,telefono=? WHERE id=?');
    $stmt->bind_param('ssi', $email, $telefono, $u['id']);
}

if (!$stmt->execute()) {
    echo json_encode(['ok'=>false,'msg'=>'No se pudo actualizar: ese correo o documento ya está en uso.']); exit;
}

$msg = ($passActual !== '' && $passNueva !== '')
    ? 'Perfil y contraseña actualizados correctamente.'
    : 'Datos actualizados correctamente.';

echo json_encode(['ok'=>true,'msg'=>$msg]);
