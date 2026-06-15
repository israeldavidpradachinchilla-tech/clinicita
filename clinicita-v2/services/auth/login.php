<?php
require_once __DIR__ . '/../shared/jwt.php';
require_once __DIR__ . '/../shared/db.php';
require_once __DIR__ . '/../shared/config.php';

$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password']  ?? '';

if (!$email || !$pass) {
    echo json_encode(['ok'=>false,'msg'=>'Datos incompletos']); exit;
}

$db   = getDB();
$stmt = $db->prepare(
    'SELECT id,nombre,cedula,tipo_documento,email,telefono,password,rol FROM usuarios WHERE email=?'
);
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($pass, $user['password'])) {
    echo json_encode(['ok'=>false,'msg'=>'Credenciales incorrectas']); exit;
}

unset($user['password']);

// Incluir medico_id en el token para evitar consultas extras en cada request
if ($user['rol'] === 'medico') {
    $s = $db->prepare('SELECT id, especialidad FROM medicos WHERE usuario_id=?');
    $s->bind_param('i', $user['id']);
    $s->execute();
    $med = $s->get_result()->fetch_assoc();
    if ($med) {
        $user['medico_id']    = $med['id'];
        $user['especialidad'] = $med['especialidad'];
    }
}

$token = JWT::encode($user);

setcookie('clinicita_token', $token, time() + JWT::TTL, BASE_PATH);

$destinos = ['paciente'=>'paciente.php','medico'=>'medico.php','admin'=>'admin.php'];
echo json_encode([
    'ok'      => true,
    'token'   => $token,
    'usuario' => $user,
    'redirect'=> BASE_PATH . ($destinos[$user['rol']] ?? 'login.html'),
]);
