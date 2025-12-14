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

if ($method === 'DELETE') {
    if (!isset($_GET['id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID es requerido'
        ]);
        exit();
    }

    if ($medidaModel->eliminar($_GET['id'])) {
        echo json_encode([
            'success' => true,
            'message' => 'Medida eliminada correctamente'
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar la medida'
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