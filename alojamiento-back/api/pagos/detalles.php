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
$id = $data['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de pago requerido']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT p.id, p.url_codigo_qr, p.monto, p.fecha_pago, p.metodo_pago, p.id_reserva,
                                  r.fecha_entrada, r.fecha_salida, u.nombre_usuario AS huesped
                           FROM pagos p
                           LEFT JOIN reservas r ON p.id_reserva = r.id
                           LEFT JOIN usuarios u ON r.id_huesped = u.id
                           WHERE p.id = ?");
    $stmt->execute([$id]);
    $pago = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($pago) {
        echo json_encode(['success' => true, 'pago' => $pago]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Pago no encontrado']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener detalles del pago: ' . $e->getMessage()]);
}
?>
