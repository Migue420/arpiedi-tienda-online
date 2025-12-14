<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/Contacto.php';

verificarAuth();

$database = new Database();
$db = $database->getConnection();
$contacto = new Contacto($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $contactos = $contacto->listar();

    echo json_encode([
        'success' => true,
        'data' => $contactos
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>