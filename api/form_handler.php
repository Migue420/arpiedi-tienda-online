<?php
// api/form_handler.php

header('Content-Type: application/json');

// Allow CORS if necessary (adjust the origin as needed)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
    exit;
}

require_once 'config.php';

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Datos inválidos.']);
    exit;
}

$form_type = isset($input['form_type']) ? $input['form_type'] : '';

// Helper function to sanitize input
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

try {
    if ($form_type === 'contacto') {
        // Validate inputs
        if (empty($input['nombre']) || empty($input['email']) || empty($input['mensaje'])) {
            throw new Exception('Faltan campos obligatorios.');
        }

        $nombre = sanitize($input['nombre']);
        $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        $telefono = isset($input['telefono']) ? sanitize($input['telefono']) : '';
        $mensaje = sanitize($input['mensaje']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido.');
        }

        $stmt = $pdo->prepare("INSERT INTO contacto_web (nombre, email, telefono, mensaje) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $email, $telefono, $mensaje]);

        echo json_encode(['success' => true, 'message' => 'Mensaje enviado correctamente.']);

    } elseif ($form_type === 'newsletter') {
        if (empty($input['email'])) {
            throw new Exception('Email es obligatorio.');
        }

        $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido.');
        }

        // Check if email already exists
        $stmt_check = $pdo->prepare("SELECT id FROM suscripciones WHERE email = ?");
        $stmt_check->execute([$email]);
        if ($stmt_check->fetch()) {
             // Return success even if exists to prevent email enumeration, or specific message if desired.
             // Here we return a friendly message.
             echo json_encode(['success' => true, 'message' => 'Ya estás suscrito.']);
             exit;
        }

        $stmt = $pdo->prepare("INSERT INTO suscripciones (email) VALUES (?)");
        $stmt->execute([$email]);

        echo json_encode(['success' => true, 'message' => 'Suscripción realizada con éxito.']);

    } elseif ($form_type === 'presupuesto') {
        if (empty($input['nombre']) || empty($input['email']) || empty($input['detalles_proyecto'])) {
            throw new Exception('Faltan campos obligatorios.');
        }

        $nombre = sanitize($input['nombre']);
        $empresa = isset($input['empresa']) ? sanitize($input['empresa']) : '';
        $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
        $detalles = sanitize($input['detalles_proyecto']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email inválido.');
        }

        $stmt = $pdo->prepare("INSERT INTO presupuestos_solicitados (nombre, empresa, detalles_proyecto, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$nombre, $empresa, $detalles, $email]);

        echo json_encode(['success' => true, 'message' => 'Solicitud de presupuesto enviada.']);

    } else {
        throw new Exception('Tipo de formulario desconocido.');
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    // Log detailed error internally
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al guardar los datos.']);
}
?>
