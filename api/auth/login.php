<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt.php';
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

    $resultado = $admin->login($data['username'], $data['password']);

    if ($resultado) {
        $token = JWT::encode([
            'id' => $resultado['id'],
            'username' => $resultado['username']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Login exitoso',
            'token' => $token,
            'admin' => [
                'id' => $resultado['id'],
                'username' => $resultado['username'],
                'email' => $resultado['email']
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Credenciales inválidas'
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