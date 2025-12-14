<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pedido.php';

$database = new Database();
$db = $database->getConnection();
$pedido = new Pedido($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validar campos requeridos
    if (!isset($data['cliente_nombre']) || !isset($data['cliente_email']) || !isset($data['items'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nombre, email e items son requeridos'
        ]);
        exit();
    }

    // Validar que haya items
    if (empty($data['items']) || !is_array($data['items'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'El pedido debe contener al menos un producto'
        ]);
        exit();
    }

    // Calcular total
    $total = 0;
    foreach ($data['items'] as $item) {
        if (!isset($item['producto_nombre']) || !isset($item['cantidad']) || !isset($item['precio_unitario'])) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Datos de producto incompletos'
            ]);
            exit();
        }
        $total += $item['cantidad'] * $item['precio_unitario'];
    }

    $datos = [
        'cliente_nombre' => $data['cliente_nombre'],
        'cliente_email' => $data['cliente_email'],
        'cliente_telefono' => $data['cliente_telefono'] ?? null,
        'cliente_direccion' => $data['cliente_direccion'] ?? null,
        'total' => $total
    ];

    $id = $pedido->crear($datos, $data['items']);

    if ($id) {
        echo json_encode([
            'success' => true,
            'message' => 'Pedido creado correctamente',
            'id' => $id,
            'total' => $total
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear el pedido'
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