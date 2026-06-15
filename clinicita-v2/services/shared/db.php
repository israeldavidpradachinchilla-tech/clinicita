<?php
/**
 * Credenciales de base de datos.
 * Edita estos valores con los de tu hosting.
 */
define('_DB_HOST', 'localhost');
define('_DB_USER', 'studiospa_clinicita');
define('_DB_PASS', 'uLeDaMkTNzjM8MH56cHa');
define('_DB_NAME', 'studiospa_clinicita');

function getDB(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(_DB_HOST, _DB_USER, _DB_PASS, _DB_NAME);
        if ($conn->connect_error) {
            header('Content-Type: application/json');
            echo json_encode(['ok'=>false,'msg'=>'Error de base de datos: ' . $conn->connect_error]);
            exit;
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}
