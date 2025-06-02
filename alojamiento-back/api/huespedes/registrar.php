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

$id_huesped = $data['id_huesped'] ?? null;
$id_habitacion = $data['id_habitacion'] ?? null;
$fecha_entrada = $data['fecha_entrada'] ?? null;
$fecha_salida = $data['fecha_salida'] ?? null;
$tipo_reserva = $data['tipo_reserva'] ?? null;

if (!$id_huesped || !$id_habitacion || !$fecha_entrada || !$fecha_salida) {
    http_response_code(400);
    echo json_encode(['success' => true, 'id_huesped' => $pdo->lastInsertId()]);
    exit;
}


try {
    $stmt = $pdo->prepare("INSERT INTO reservas (id_huesped, fecha_entrada, fecha_salida, tipo_reserva, id_habitacion, fecha_reserva) VALUES (?, ?, ?, ?, ?, CURDATE())");
    $stmt->execute([$id_huesped, $fecha_entrada, $fecha_salida, $tipo_reserva, $id_habitacion]);
    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'message' => 'Reserva registrada correctamente', 'id_reserva' => $id]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al registrar reserva: ' . $e->getMessage()]);
}
