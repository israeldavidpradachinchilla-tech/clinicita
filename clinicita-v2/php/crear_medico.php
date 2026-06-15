<?php
require 'conexion.php';
requiereRol('admin');

/* crear_medico.php — Registro de médico, Valida campos obligatorios, Inserta usuario con rol médico, Guarda especialidad */
$nombre = trim($_POST['nombre'] ?? '');
$cedula = trim($_POST['cedula'] ?? '');
$email  = trim($_POST['email']  ?? '');
$esp    = trim($_POST['especialidad'] ?? '');
$espNueva = trim($_POST['especialidad_nueva'] ?? '');
$pass   = $_POST['password'] ?? '';

if (!$nombre || !$cedula || !$email || (!$esp && !$espNueva) || strlen($pass) < 6) {
    echo "<script>alert('Datos inválidos');history.back();</script>"; exit;
}

$especialidad = $espNueva ?: $esp;

$conexion->begin_transaction();
try {
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $conexion->prepare(
      'INSERT INTO usuarios (nombre,cedula,email,password,rol) VALUES (?,?,?,?,"medico")'
    );
    $stmt->bind_param('ssss', $nombre, $cedula, $email, $hash);
    $stmt->execute();
    $uid = $conexion->insert_id;

    $stmt = $conexion->prepare('INSERT INTO medicos (usuario_id,especialidad) VALUES (?,?)');
    $stmt->bind_param('is', $uid, $especialidad);
    $stmt->execute();

    $conexion->commit();
    echo "<script>alert('Médico creado');location.href='../admin.php';</script>";
} catch (Exception $e) {
    $conexion->rollback();
    echo "<script>alert('Error: correo o cédula duplicados');history.back();</script>";
}
