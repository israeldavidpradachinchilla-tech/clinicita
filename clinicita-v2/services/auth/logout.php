<?php
require_once __DIR__ . '/../shared/config.php';
setcookie('clinicita_token', '', time()-3600, BASE_PATH);
echo json_encode(['ok'=>true,'redirect'=>'/login']);
