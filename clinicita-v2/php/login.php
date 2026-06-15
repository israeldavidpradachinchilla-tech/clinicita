<?php
require 'conexion.php';

/* login.php — Inicio de sesión, Buscar usuario por email, Validar contraseña, Verificar sesión activa en otro dispositivo, Redirigir según rol */
$email = trim($_POST['email'] ?? '');
$pass  = $_POST['password'] ?? '';

$stmt = $conexion->prepare('SELECT id,nombre,cedula,tipo_documento,email,telefono,password,rol,session_token FROM usuarios WHERE email=?');
$stmt->bind_param('s', $email);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if (!$res || !password_verify($pass, $res['password'])) {
    echo "<script>alert('Credenciales incorrectas');location.href='../login.html';</script>";
    exit;
}

// Si había un token anterior, lo reemplazamos al iniciar sesión.
// Esto evita el bloqueo cuando el usuario cierra el navegador sin usar "Cerrar sesión".
if (!empty($res['session_token']) && $res['session_token'] !== session_id()) {
    // El token viejo se anula al iniciar una nueva sesión válida.
}

// Guardar el token de sesión actual en la BD
$token = session_id();
$stmtT = $conexion->prepare('UPDATE usuarios SET session_token=? WHERE id=?');
$stmtT->bind_param('si', $token, $res['id']);
$stmtT->execute();

unset($res['password'], $res['session_token']);
$_SESSION['usuario'] = $res;

switch ($res['rol']) {
  case 'paciente': header('Location: ../paciente.php'); break;
  case 'medico':   header('Location: ../medico.php');   break;
  case 'admin':    header('Location: ../admin.php');    break;
}
exit;
