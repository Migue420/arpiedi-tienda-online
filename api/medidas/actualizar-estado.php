<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../middleware/auth.php';
require_once __DIR__ . '/../models/Medida.php';

verificarAuth();

$database = new Database();
$db = $database->getConnection();
$medidaModel = new Medida($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($_GET['id']) || !isset($data['estado'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID y estado son requeridos'
        ]);
        exit();
    }

    $estadosPermitidos = ['pendiente', 'procesando', 'completado'];
    if (!in_array($data['estado'], $estadosPermitidos)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Estado no válido'
        ]);
        exit();
    }

    if ($medidaModel->actualizarEstado($_GET['id'], $data['estado'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el estado'
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