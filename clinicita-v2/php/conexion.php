<?php
// Conexión a la base de datos MySQL (XAMPP)
$DB_HOST = 'localhost';
$DB_USER = 'root';      // usuario por defecto de XAMPP
$DB_PASS = '';          // contraseña por defecto vacía
$DB_NAME = 'citas';

$conexion = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conexion->connect_error) {
    die('Error de conexión: ' . $conexion->connect_error);
}
$conexion->set_charset('utf8mb4');

// Sesiones para login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function usuarioActual() {
    return $_SESSION['usuario'] ?? null;
}
function requiereRol($rol) {
    $u = usuarioActual();
    $esAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) || 
              (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
              (isset($_POST['horario_id']) || isset($_GET['medico_id']));

    if (!$u || $u['rol'] !== $rol) {
        if ($esAjax) { header('Content-Type: application/json'); echo json_encode(['ok'=>false,'redirect'=>'/clinicita/login.html']); exit; }
        header('Location: /clinicita/login.html'); exit;
    }
    global $conexion;
    $stmt = $conexion->prepare('SELECT session_token FROM usuarios WHERE id=?');
    $stmt->bind_param('i', $u['id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    if (!$row || $row['session_token'] !== session_id()) {
        session_destroy();
        if ($esAjax) { header('Content-Type: application/json'); echo json_encode(['ok'=>false,'redirect'=>'/clinicita/login.html?sesion=expirada']); exit; }
        header('Location: /clinicita/login.html?sesion=expirada'); exit;
    }
}
