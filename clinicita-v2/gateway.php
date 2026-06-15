<?php
/**
 * API Gateway — ClíniCita Microservices
 */
ob_start();
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/services/shared/jwt.php';
require_once __DIR__ . '/services/shared/config.php';

// Limpiar cualquier output previo (warnings, notices, etc.)
ob_clean();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');

$svc    = preg_replace('/[^a-z_]/', '', strtolower($_GET['svc']    ?? ''));
$action = preg_replace('/[^a-z_]/', '', strtolower($_GET['action'] ?? ''));

// Rutas públicas (sin JWT)
$rutasPublicas = ['auth' => ['login', 'registrar']];
$esPublica = isset($rutasPublicas[$svc]) && in_array($action, $rutasPublicas[$svc]);

$GLOBALS['gateway_user'] = null;

if (!$esPublica) {
    $token = '';

    // 1. Authorization: Bearer <token>
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/^Bearer\s+(.+)$/i', $authHeader, $m)) {
        $token = $m[1];
    }

    // 2. Cookie clinicita_token
    if (!$token) {
        $token = $_COOKIE['clinicita_token'] ?? '';
    }

    if (!$token) {
        http_response_code(401);
        echo json_encode(['ok'=>false,'msg'=>'No autenticado','redirect'=>'/login']);
        exit;
    }

    $payload = JWT::decode($token);
    if (!$payload) {
        setcookie('clinicita_token', '', time()-3600, BASE_PATH);
        http_response_code(401);
        echo json_encode(['ok'=>false,'msg'=>'Sesión expirada','redirect'=>'/login']);
        exit;
    }

    $GLOBALS['gateway_user'] = $payload;
}

// Enrutar al archivo del servicio
$archivo = __DIR__ . "/services/{$svc}/{$action}.php";

if (!$svc || !$action || !file_exists($archivo)) {
    http_response_code(404);
    echo json_encode(['ok'=>false,'msg'=>"Ruta no encontrada: {$svc}/{$action}"]);
    exit;
}

try {
    require $archivo;
} catch (Throwable $e) {
    ob_clean();
    http_response_code(500);
    echo json_encode(['ok'=>false,'msg'=>'Error interno del servidor: ' . $e->getMessage()]);
}
