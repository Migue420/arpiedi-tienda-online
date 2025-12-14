<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/Medida.php';

verificarAuth();

$database = new Database();
$db = $database->getConnection();
$medida = new Medida($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $medidas = $medida->listar();

    echo json_encode([
        'success' => true,
        'data' => $medidas
    ]);
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>