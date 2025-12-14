<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Medida.php';

$database = new Database();
$db = $database->getConnection();
$medida = new Medida($db);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Procesar datos del formulario
    $datos = [
        'cliente_nombre' => $_POST['cliente_nombre'] ?? '',
        'cliente_email' => $_POST['cliente_email'] ?? '',
        'cliente_telefono' => $_POST['cliente_telefono'] ?? null,
        'pie_derecho_largo' => $_POST['pie_derecho_largo'] ?? null,
        'pie_derecho_ancho' => $_POST['pie_derecho_ancho'] ?? null,
        'pie_izquierdo_largo' => $_POST['pie_izquierdo_largo'] ?? null,
        'pie_izquierdo_ancho' => $_POST['pie_izquierdo_ancho'] ?? null,
        'notas' => $_POST['notas'] ?? null
    ];

    // Validar campos requeridos
    if (empty($datos['cliente_nombre']) || empty($datos['cliente_email'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Nombre y email son requeridos'
        ]);
        exit();
    }

    // Procesar archivos subidos
    $fotos = [
        'foto_derecha' => null,
        'foto_izquierda' => null,
        'foto_lateral' => null,
        'foto_superior' => null
    ];

    foreach ($fotos as $campo => &$valor) {
        if (isset($_FILES[$campo]) && $_FILES[$campo]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$campo];

            // Validar tamaño
            if ($file['size'] > MAX_FILE_SIZE) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'El archivo ' . $campo . ' excede el tamaño máximo de 5MB'
                ]);
                exit();
            }

            // Validar extensión
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($extension, ALLOWED_EXTENSIONS)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Tipo de archivo no permitido para ' . $campo
                ]);
                exit();
            }

            // Generar nombre único
            $nombreArchivo = 'medida-' . time() . '-' . rand(1000, 9999) . '.' . $extension;
            $rutaDestino = UPLOAD_DIR . $nombreArchivo;

            // Crear directorio si no existe
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0777, true);
            }

            // Mover archivo
            if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
                $valor = $nombreArchivo;
            }
        }
    }

    // Guardar en base de datos
    $id = $medida->crear($datos, $fotos);

    if ($id) {
        echo json_encode([
            'success' => true,
            'message' => 'Medidas guardadas correctamente',
            'id' => $id
        ]);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Error al guardar las medidas'
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