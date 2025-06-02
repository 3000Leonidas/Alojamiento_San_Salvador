<?php
// Cabeceras para CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

try {
    $stmt = $pdo->query("SELECT r.id, r.id_huesped, r.fecha_entrada, r.fecha_salida, r.tipo_reserva, r.id_habitacion, r.fecha_reserva,
                                u.nombre_usuario AS nombre_huesped,
                                h.numero_habitacion
                         FROM reservas r
                        LEFT JOIN huespedes h ON r.id_huesped = h.id
                        LEFT JOIN habitaciones hab ON r.id_habitacion = hab.id
                         ORDER BY r.fecha_reserva DESC");

    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'reservas' => $reservas]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar reservas: ' . $e->getMessage()]);
}
?>
