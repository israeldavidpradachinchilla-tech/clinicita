<?php
/**
 * Detecta automáticamente la ruta base del proyecto.
 * Funciona tanto si está en / como en /clinicita/ u otra subcarpeta.
 */
if (!defined('BASE_PATH')) {
    $scriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
    define('BASE_PATH', rtrim(str_replace('\\', '/', $scriptDir), '/') . '/');
}
