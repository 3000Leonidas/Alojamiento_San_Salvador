<?php
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
    $query = "
        SELECT 
            r.id AS id_reserva,
            r.fecha_entrada,
            r.fecha_salida,
            r.tipo_reserva,
            r.fecha_reserva,
            h.numero_habitacion,
            h.tipo_habitacion,
            hs.nombre,
            hs.apellido,
            hs.ci,
            hs.email,
            hs.numero_telefono
        FROM reservas r
        LEFT JOIN habitaciones h ON r.id_habitacion = h.id
        LEFT JOIN huespedes hs ON r.id_huesped = hs.id
        ORDER BY r.fecha_entrada DESC
    ";

    $stmt = $pdo->query($query);
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'reservas' => $reservas
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al listar reservas: ' . $e->getMessage()
    ]);
}
?>