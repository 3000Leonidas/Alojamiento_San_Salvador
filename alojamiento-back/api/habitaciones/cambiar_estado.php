<?php
// Cabeceras para CORS y JSON
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: Content-Type");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    http_response_code(200);
    exit;
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once('../../config/db.php');

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$esta_disponible = $data['esta_disponible'] ?? null;

if ($id === null || $esta_disponible === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
    exit;
}

try {
    // ✅ USAR EL NOMBRE CORRECTO DE LA COLUMNA: esta_disponible
    $stmt = $pdo->prepare("UPDATE habitaciones SET esta_disponible = ? WHERE id = ?");
    $stmt->execute([$esta_disponible, $id]);

    echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
