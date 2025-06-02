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

$nombre_descuento = $data['nombre_descuento'] ?? null;
$porcentaje_descuento = $data['porcentaje_descuento'] ?? null;
$fecha_inicio = $data['fecha_inicio'] ?? null;
$fecha_fin = $data['fecha_fin'] ?? null;

if (!$nombre_descuento || !$porcentaje_descuento || !$fecha_inicio || !$fecha_fin) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO descuentos_especiales (nombre_descuento, porcentaje_descuento, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre_descuento, $porcentaje_descuento, $fecha_inicio, $fecha_fin]);

    echo json_encode(['success' => true, 'message' => 'Descuento registrado correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al registrar descuento: ' . $e->getMessage()]);
}
?>