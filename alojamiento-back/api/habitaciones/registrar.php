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

$nombre = $data['nombre'] ?? null;
$apellido = $data['apellido'] ?? null;
$ci = $data['ci'] ?? null;
$email = $data['email'] ?? null;
$numero_telefono = $data['numero_telefono'] ?? null;
$id_habitacion = $data['id_habitacion'] ?? null;
$fecha_entrada = $data['fecha_entrada'] ?? null;
$fecha_salida = $data['fecha_salida'] ?? null;
$tipo_reserva = $data['tipo_reserva'] ?? 'normal';

if (!$nombre || !$apellido || !$ci || !$email || !$id_habitacion || !$fecha_entrada || !$fecha_salida) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id FROM huespedes WHERE ci = ?");
    $stmt->execute([$ci]);
    $huesped = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($huesped) {
        $id_huesped = $huesped['id'];
    } else {
        $stmt = $pdo->prepare("INSERT INTO huespedes (nombre, apellido, ci, email, numero_telefono, fecha_entrada, fecha_salida) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$nombre, $apellido, $ci, $email, $numero_telefono, $fecha_entrada, $fecha_salida]);
        $id_huesped = $pdo->lastInsertId();
    }

    $stmt = $pdo->prepare("INSERT INTO reservas (id_huesped, fecha_entrada, fecha_salida, tipo_reserva, id_habitacion, fecha_reserva) VALUES (?, ?, ?, ?, ?, CURDATE())");
    $stmt->execute([$id_huesped, $fecha_entrada, $fecha_salida, $tipo_reserva, $id_habitacion]);
    $id_reserva = $pdo->lastInsertId();

    $stmt = $pdo->prepare("UPDATE habitaciones SET esta_disponible = 0 WHERE id = ?");
    $stmt->execute([$id_habitacion]);

    echo json_encode([
        'success' => true,
        'message' => 'Reserva registrada correctamente',
        'id_reserva' => $id_reserva
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al registrar reserva: ' . $e->getMessage()]);
}
?>
