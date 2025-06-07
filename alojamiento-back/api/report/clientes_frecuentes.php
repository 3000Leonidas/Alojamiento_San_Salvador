<?php
header("Access-Control-Allow-Origin: *"); 
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
            h.id,
            h.nombre,
            h.apellido,
            h.email,
            COUNT(r.id) AS cantidad_reservas,
            MAX(r.fecha_reserva) AS ultima_reserva
        FROM huespedes h
        INNER JOIN reservas r ON r.id_huesped = h.id
        GROUP BY h.id, h.nombre, h.apellido, h.email
        ORDER BY cantidad_reservas DESC
        LIMIT 10
    ");
    
    $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'clientes' => $clientes
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error al obtener reporte de clientes frecuentes: ' . $e->getMessage()
    ]);
}
?>
