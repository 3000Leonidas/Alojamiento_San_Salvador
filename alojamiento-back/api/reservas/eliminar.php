<?php
// Cabeceras CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, DELETE, OPTIONS");
header("Content-Type: application/json");

// Manejo de preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

try {
    // Obtener ID por método GET o DELETE
    $id = $_GET['id'] ?? null;

    if (!$id) {
        throw new Exception("ID de reserva no proporcionado.");
    }

    // Verificar si la reserva existe
    $stmt = $pdo->prepare("SELECT id FROM reservas WHERE id = ?");
    $stmt->execute([$id]);

    if (!$stmt->fetch()) {
        throw new Exception("La reserva no existe.");
    }

    // Eliminar la reserva
    $delete = $pdo->prepare("DELETE FROM reservas WHERE id = ?");
    $delete->execute([$id]);

    echo json_encode([
        "success" => true,
        "message" => "Reserva eliminada correctamente."
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Error al eliminar reserva: " . $e->getMessage()
    ]);
}
