<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/Pedido.php';

verificarAuth();

$database = new Database();
$db = $database->getConnection();
$pedidoModel = new Pedido($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID es requerido'
        ]);
        exit();
    }

    $pedido = $pedidoModel->obtenerPorId($_GET['id']);

    if ($pedido) {
        echo json_encode([
            'success' => true,
            'data' => $pedido
        ]);
    } else {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Pedido no encontrado'
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ]);
}
?>