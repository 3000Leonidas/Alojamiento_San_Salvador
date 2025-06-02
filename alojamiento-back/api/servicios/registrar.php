<?php
// Cabeceras para CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"), true);

$nombre_servicio = $data['nombre_servicio'] ?? null;
$precio = $data['precio'] ?? null;

if (!$nombre_servicio || !$precio) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO servicios_adicionales (nombre_servicio, precio) VALUES (?, ?)");
    $stmt->execute([$nombre_servicio, $precio]);

    echo json_encode(['success' => true, 'message' => 'Servicio registrado correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al registrar servicio: ' . $e->getMessage()]);
}
?>
