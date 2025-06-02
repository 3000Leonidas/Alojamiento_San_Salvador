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
$metodo_pago = $data['metodo_pago'] ?? null;
$monto = $data['monto'] ?? null;

if (!$id_reserva || !$metodo_pago || !$monto) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    $url_codigo_qr = 'uploads/simulado_qr_pago.png';

    // Registrar pago
    $stmt = $pdo->prepare("INSERT INTO pagos (id_reserva, metodo_pago, monto, url_codigo_qr, fecha_pago) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$id_reserva, $metodo_pago, $monto, $url_codigo_qr]);

    // Obtener id_habitacion desde la reserva para actualizar su disponibilidad
    $stmt = $pdo->prepare("SELECT id_habitacion FROM reservas WHERE id = ?");
    $stmt->execute([$id_reserva]);
    $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reserva) {
        $id_habitacion = $reserva['id_habitacion'];
        $stmt = $pdo->prepare("UPDATE habitaciones SET esta_disponible = 0 WHERE id = ?");
        $stmt->execute([$id_habitacion]);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Pago registrado correctamente',
        'url_codigo_qr' => $url_codigo_qr
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al registrar el pago: ' . $e->getMessage()]);
}
?>
