<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Admin.php';

$database = new Database();
$db = $database->getConnection();
$admin = new Admin($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['username']) || !isset($data['password'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Usuario y contraseña son requeridos'
        ]);
        exit();
    }

    if ($admin->existeUsername($data['username'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'El usuario ya existe'
        ]);
        exit();
    }

    $id = $admin->crear($data['username'], $data['password'], $data['email'] ?? null);

    if ($id) {
        echo json_encode([
            'success' => true,
            'message' => 'Administrador creado correctamente',
            'id' => $id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al crear administrador'
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