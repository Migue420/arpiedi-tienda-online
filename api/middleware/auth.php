<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/jwt.php';

function verificarAuth()
{
    $headers = getallheaders();

    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'No se proporcionó token de autenticación'
        ]);
        exit();
    }

    $authHeader = $headers['Authorization'];
    $token = str_replace('Bearer ', '', $authHeader);

    $payload = JWT::decode($token);

    if (!$payload) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Token inválido o expirado'
        ]);
        exit();
    }

    return $payload;
}
?>