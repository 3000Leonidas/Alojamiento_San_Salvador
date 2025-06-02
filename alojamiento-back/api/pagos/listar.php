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
    $stmt = $pdo->query("SELECT p.id, p.url_codigo_qr, p.monto, p.fecha_pago, p.metodo_pago, p.id_reserva,
                                r.fecha_entrada, r.fecha_salida, u.nombre_usuario AS huesped
                         FROM pagos p
                         LEFT JOIN reservas r ON p.id_reserva = r.id
                         LEFT JOIN usuarios u ON r.id_huesped = u.id
                         ORDER BY p.fecha_pago DESC");

    $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'pagos' => $pagos]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al listar pagos: ' . $e->getMessage()]);
}
?>