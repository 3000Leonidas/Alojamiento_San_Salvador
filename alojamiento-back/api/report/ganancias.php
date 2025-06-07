<?php
header("Access-Control-Allow-Origin: *"); // o especifica tu frontend: http://localhost:5173
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once('../../config/db.php');

try {
    $stmt = $pdo->query("
        SELECT 
            h.numero_habitacion,
            h.tipo_habitacion,
            COUNT(r.id) AS total_reservas,
            SUM(p.monto) AS total_ganancias,
            MAX(p.fecha_pago) AS ultima_reserva
        FROM habitaciones h
        INNER JOIN reservas r ON r.id_habitacion = h.id
        INNER JOIN pagos p ON p.id_reserva = r.id
        GROUP BY h.id, h.numero_habitacion, h.tipo_habitacion
        ORDER BY total_ganancias DESC
    ");

    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'reporte' => $resultados
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al generar reporte de ganancias: ' . $e->getMessage()
    ]);
}
?>
