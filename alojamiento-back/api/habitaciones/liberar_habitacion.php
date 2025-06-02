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
$id_reserva = $data['id_reserva'] ?? null;

if (!$id_reserva) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de reserva no proporcionado']);
    exit;
}

try {
    // Obtener la habitación asociada
    $stmt = $pdo->prepare("SELECT id_habitacion FROM reservas WHERE id = ?");
    $stmt->execute([$id_reserva]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reserva) throw new Exception('Reserva no encontrada');

    // Marcar habitación como disponible
    $stmt = $pdo->prepare("UPDATE habitaciones SET esta_disponible = 1 WHERE id = ?");
    $stmt->execute([$reserva['id_habitacion']]);

    echo json_encode(['success' => true, 'message' => 'Habitación liberada']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
