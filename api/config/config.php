<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'arpiedi_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuración JWT
define('JWT_SECRET', 'arpiedi_secret_key_change_in_production_2024');
define('JWT_EXPIRATION', 86400); // 24 horas en segundos

// Configuración de archivos
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Configuración general
define('TIMEZONE', 'Europe/Madrid');
date_default_timezone_set(TIMEZONE);

// Habilitar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>
