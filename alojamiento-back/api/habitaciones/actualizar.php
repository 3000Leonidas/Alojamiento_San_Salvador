<?php
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

$id = $data['id'] ?? null;
$disponible = $data['disponible'] ?? null;

if ($id === null || $disponible === null) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE habitaciones SET esta_disponible = ? WHERE id = ?");
    $stmt->execute([$disponible, $id]);

    echo json_encode(['success' => true, 'message' => 'Disponibilidad actualizada']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
