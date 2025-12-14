<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Contacto.php';

$database = new Database();
$db = $database->getConnection();
$contacto = new Contacto($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Validar campos requeridos
    if (!isset($data['nombre']) || !isset($data['email']) || !isset($data['mensaje'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nombre, email y mensaje son requeridos'
        ]);
        exit();
    }

    // Validar email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Email no válido'
        ]);
        exit();
    }

    $datos = [
        'nombre' => $data['nombre'],
        'email' => $data['email'],
        'telefono' => $data['telefono'] ?? null,
        'mensaje' => $data['mensaje']
    ];

    $id = $contacto->crear($datos);

    if ($id) {
        echo json_encode([
            'success' => true,
            'message' => 'Mensaje enviado correctamente',
            'id' => $id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al enviar el mensaje'
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